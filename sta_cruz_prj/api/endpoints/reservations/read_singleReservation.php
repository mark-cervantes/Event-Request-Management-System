<?php
// api/endpoints/read_singleReservation.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 0); 
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/reservation.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Reservation object
$reservation = new Reservation($db);

// Get id from query parameter (GET method)
if (isset($_GET['id'])) {
    $reservation->id = $_GET['id'];

    // Try to read single reservation record
    if ($reservation->read_single()) {
        // Return reservation data as JSON
        echo json_encode([
            'message' => 'Reservation record found',
            'reservation' => [
                'id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'official_id' => $reservation->official_id,
                'facility' => $reservation->facility,
                'event_name' => $reservation->event_name,
                'start_datetime' => $reservation->start_datetime,
                'end_datetime' => $reservation->end_datetime,
                'date_time_requested' => $reservation->date_time_requested,
                'status' => $reservation->status,
                'requested_by' => $reservation->requested_by,
                'contact_number' => $reservation->contact_number,
                'purpose' => $reservation->purpose,
                'expected_attendees' => $reservation->expected_attendees,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at
            ]
        ]);
    } else {
        // If no record is found
        echo json_encode([
            'message' => 'No reservation found'
        ]);
    }
} else {
    // If id is missing
    echo json_encode([
        'message' => 'Invalid input: id not provided'
    ]);
}
?>
