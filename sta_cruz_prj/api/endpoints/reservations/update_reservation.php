<?php
// api/endpoints/update_reservation.php

// Include centralized CORS configuration
include_once '../../config/cors.php';

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);
$data = json_decode(file_get_contents("php://input"));

// Set reservation properties
$reservation->id = $data->id ?? null;
$reservation->user_id = (!empty($data->user_id) && $data->user_id !== '') ? (int)$data->user_id : null;
$reservation->official_id = (!empty($data->official_id) && $data->official_id !== '') ? (int)$data->official_id : null;
$reservation->facility = $data->facility ?? null;
$reservation->event_name = $data->event_name ?? null;
$reservation->start_datetime = $data->start_datetime ?? null;
$reservation->end_datetime = $data->end_datetime ?? null;
$reservation->status = $data->status ?? null;
$reservation->requested_by = $data->requested_by ?? null;
$reservation->contact_number = $data->contact_number ?? null;
$reservation->purpose = $data->purpose ?? null;
$reservation->expected_attendees = (!empty($data->expected_attendees) && $data->expected_attendees !== '') ? (int)$data->expected_attendees : 0;

// Validate required fields
if (!empty($reservation->id)) {
    // Validate foreign key references only if they are provided
    if ($reservation->user_id !== null && !$reservation->validateUser($reservation->user_id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid user ID provided']);
        exit;
    }

    if ($reservation->official_id !== null && !$reservation->validateOfficial($reservation->official_id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid official ID provided']);
        exit;
    }

    // Check for scheduling conflicts if datetime is being changed
    if (!empty($reservation->start_datetime) && !empty($reservation->end_datetime) && !empty($reservation->facility)) {
        if ($reservation->checkConflict()) {
            http_response_code(409); // Conflict
            echo json_encode(['message' => 'Schedule conflict: The facility is already reserved for the requested time']);
            exit;
        }
    }
    
    if ($reservation->update()) {
        echo json_encode(['message' => 'Reservation updated successfully']);
    } else {
        echo json_encode(['message' => 'Reservation not updated']);
    }
} else {
    echo json_encode(['message' => 'Missing required field: id']);
}
?>
