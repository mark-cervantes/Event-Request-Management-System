<?php
// api/endpoints/create_singleReservation.php

// Include centralized CORS configuration
include_once '../config/cors.php';

include_once '../config/database.php';
include_once '../models/reservation.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Reservation object
$reservation = new Reservation($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Basic validation for required fields
if (
    !empty($data->facility) &&
    !empty($data->event_name) &&
    !empty($data->start_datetime) &&
    !empty($data->end_datetime) &&
    !empty($data->requested_by)
) {
    // Set reservation properties
    $reservation->user_id = $data->user_id ?? null;
    $reservation->official_id = $data->official_id ?? null;
    $reservation->facility = $data->facility;
    $reservation->event_name = $data->event_name;
    $reservation->start_datetime = $data->start_datetime;
    $reservation->end_datetime = $data->end_datetime;
    $reservation->status = $data->status ?? 'Pending';
    $reservation->requested_by = $data->requested_by;
    $reservation->contact_number = $data->contact_number ?? null;
    $reservation->purpose = $data->purpose ?? null;
    $reservation->expected_attendees = $data->expected_attendees ?? 0;

    // Check for scheduling conflicts
    if ($reservation->checkConflict()) {
        http_response_code(409); // Conflict
        echo json_encode(['message' => 'Schedule conflict: The facility is already reserved for the requested time']);
    } else {
        // Create reservation
        if ($reservation->create()) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Reservation created successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Reservation not created']);
        }
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Missing required fields: facility, event_name, start_datetime, end_datetime, requested_by']);
}
?>
