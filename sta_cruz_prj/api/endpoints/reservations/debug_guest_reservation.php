<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);

// Get the raw input for debugging
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input);

// Debug information
$debug_info = array(
    "raw_input" => $raw_input,
    "decoded_data" => $data,
    "required_fields_check" => array(
        "event_name" => array(
            "exists" => isset($data->event_name),
            "not_empty" => !empty($data->event_name),
            "value" => isset($data->event_name) ? $data->event_name : "NOT_SET"
        ),
        "facility" => array(
            "exists" => isset($data->facility),
            "not_empty" => !empty($data->facility),
            "value" => isset($data->facility) ? $data->facility : "NOT_SET"
        ),
        "start_datetime" => array(
            "exists" => isset($data->start_datetime),
            "not_empty" => !empty($data->start_datetime),
            "value" => isset($data->start_datetime) ? $data->start_datetime : "NOT_SET"
        ),
        "end_datetime" => array(
            "exists" => isset($data->end_datetime),
            "not_empty" => !empty($data->end_datetime),
            "value" => isset($data->end_datetime) ? $data->end_datetime : "NOT_SET"
        ),
        "requested_by" => array(
            "exists" => isset($data->requested_by),
            "not_empty" => !empty($data->requested_by),
            "value" => isset($data->requested_by) ? $data->requested_by : "NOT_SET"
        ),
        "contact_number" => array(
            "exists" => isset($data->contact_number),
            "not_empty" => !empty($data->contact_number),
            "value" => isset($data->contact_number) ? $data->contact_number : "NOT_SET"
        )
    )
);

// Check if all required fields are present and not empty
$validation_passed = !empty($data->event_name) && !empty($data->facility) && !empty($data->start_datetime) && 
                    !empty($data->end_datetime) && !empty($data->requested_by) && !empty($data->contact_number);

if ($validation_passed) {
    $reservation->event_name = $data->event_name;
    $reservation->facility = $data->facility;
    $reservation->start_datetime = $data->start_datetime;
    $reservation->end_datetime = $data->end_datetime;
    $reservation->status = 'Pending'; // Always set to pending for guest reservations
    $reservation->requested_by = $data->requested_by;
    $reservation->contact_number = $data->contact_number;
    $reservation->purpose = isset($data->purpose) ? $data->purpose : '';
    $reservation->expected_attendees = isset($data->expected_attendees) ? $data->expected_attendees : 0;
    $reservation->user_id = isset($data->user_id) ? $data->user_id : null;
    $reservation->official_id = null; // Guest reservations don't have official assignment initially
    
    if ($reservation->create()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Reservation created successfully.",
            "data" => array(
                "id" => $reservation->id,
                "status" => "Pending"
            ),
            "debug" => $debug_info
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to create reservation.",
            "debug" => $debug_info
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to create reservation. Required data is missing.",
        "debug" => $debug_info
    ));
}
?>
