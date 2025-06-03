<?php
// api/endpoints/reservations/update_guest_reservation.php

// Include centralized CORS configuration
include_once '../../config/cors.php';

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(['message' => 'Reservation ID is required']);
    exit;
}

// First, get the current reservation to check status and ownership
$reservation->id = $data->id;
$current_reservation = $reservation->readSingle();

if (!$current_reservation) {
    http_response_code(404);
    echo json_encode(['message' => 'Reservation not found']);
    exit;
}

// Check if the reservation belongs to the requesting user
if (!empty($data->user_id) && $current_reservation['user_id'] != $data->user_id) {
    http_response_code(403);
    echo json_encode(['message' => 'You can only edit your own reservations']);
    exit;
}

// Only allow editing of pending reservations
if ($current_reservation['status'] !== 'Pending') {
    http_response_code(400);
    echo json_encode(['message' => 'Only pending reservations can be edited']);
    exit;
}

// Set only the fields that guests are allowed to modify
$reservation->id = $data->id;
$reservation->user_id = $current_reservation['user_id']; // Keep original user_id
$reservation->official_id = $current_reservation['official_id']; // Keep original official_id
$reservation->facility = $data->facility ?? $current_reservation['facility'];
$reservation->event_name = $data->event_name ?? $current_reservation['event_name'];
$reservation->start_datetime = $data->start_datetime ?? $current_reservation['start_datetime'];
$reservation->end_datetime = $data->end_datetime ?? $current_reservation['end_datetime'];
$reservation->status = 'Pending'; // Always keep status as Pending for guest updates
$reservation->requested_by = $data->requested_by ?? $current_reservation['requested_by'];
$reservation->contact_number = $data->contact_number ?? $current_reservation['contact_number'];
$reservation->purpose = $data->purpose ?? $current_reservation['purpose'];
$reservation->expected_attendees = $data->expected_attendees ?? $current_reservation['expected_attendees'];

// Validate required fields
if (empty($reservation->facility) || empty($reservation->event_name) || 
    empty($reservation->start_datetime) || empty($reservation->end_datetime) || 
    empty($reservation->requested_by)) {
    http_response_code(400);
    echo json_encode(['message' => 'All required fields must be provided']);
    exit;
}

// Validate datetime format and logic
$start_time = strtotime($reservation->start_datetime);
$end_time = strtotime($reservation->end_datetime);

if (!$start_time || !$end_time) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid datetime format']);
    exit;
}

if ($end_time <= $start_time) {
    http_response_code(400);
    echo json_encode(['message' => 'End time must be after start time']);
    exit;
}

// Check if the new datetime is in the future
if ($start_time <= time()) {
    http_response_code(400);
    echo json_encode(['message' => 'Start time must be in the future']);
    exit;
}

// Check for scheduling conflicts if datetime or facility is being changed
if ($reservation->start_datetime !== $current_reservation['start_datetime'] || 
    $reservation->end_datetime !== $current_reservation['end_datetime'] || 
    $reservation->facility !== $current_reservation['facility']) {
    
    if ($reservation->checkConflict()) {
        http_response_code(409); // Conflict
        echo json_encode(['message' => 'Schedule conflict: The facility is already reserved for the requested time']);
        exit;
    }
}

// Validate expected attendees
if (!empty($reservation->expected_attendees) && $reservation->expected_attendees < 1) {
    http_response_code(400);
    echo json_encode(['message' => 'Expected attendees must be at least 1']);
    exit;
}

// Update reservation
if ($reservation->update()) {
    http_response_code(200);
    echo json_encode([
        'message' => 'Reservation updated successfully',
        'data' => [
            'id' => $reservation->id,
            'status' => 'Pending' // Always return Pending status for guest updates
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to update reservation']);
}
?>
