<?php
// api/endpoints/createReservation.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');	
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/reservation.php'; 

// Instantiate database and connect
$database = new Database();
$db = $database->connect();

// Instantiate reservation object
$reservation = new Reservation($db); 

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->facility) || empty($data->event_name) || 
    empty($data->start_datetime) || empty($data->end_datetime) || 
    empty($data->requested_by)) {
    echo json_encode(array('message' => 'Missing required fields'));
    exit();
}

// Set reservation properties
$reservation->user_id = $data->user_id ?? null;
$reservation->official_id = $data->official_id ?? null;
$reservation->facility = $data->facility ?? null;
$reservation->event_name = $data->event_name ?? null;
$reservation->start_datetime = $data->start_datetime ?? null;
$reservation->end_datetime = $data->end_datetime ?? null;
$reservation->status = $data->status ?? 'Pending';
$reservation->requested_by = $data->requested_by ?? null;
$reservation->contact_number = $data->contact_number ?? null;
$reservation->purpose = $data->purpose ?? null;
$reservation->expected_attendees = $data->expected_attendees ?? 0;

// Validate foreign key references
if (!$reservation->validateUser($reservation->user_id)) {
    echo json_encode(array('message' => 'Invalid user ID provided'));
    exit();
}

if (!$reservation->validateOfficial($reservation->official_id)) {
    echo json_encode(array('message' => 'Invalid official ID provided'));
    exit();
}

// Check for scheduling conflicts
if ($reservation->checkConflict()) {
    echo json_encode(array('message' => 'Scheduling conflict detected for this facility and time'));
    exit();
}

// Create reservation
if ($reservation->create()) {
    echo json_encode(array('message' => 'Reservation created successfully'));
} else {
    echo json_encode(array('message' => 'Failed to create reservation'));
}
?>
