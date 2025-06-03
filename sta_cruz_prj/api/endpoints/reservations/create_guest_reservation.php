<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/cors.php';
include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);

// Get and decode the input data
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input);

// Log input for debugging (remove in production)
error_log("Raw input: " . $raw_input);
error_log("Decoded data: " . print_r($data, true));

// Check if JSON was properly decoded
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid JSON format.",
        "error" => json_last_error_msg()
    ));
    exit;
}

// Check for required fields with detailed validation
$required_fields = ['event_name', 'facility', 'start_datetime', 'end_datetime', 'requested_by', 'contact_number'];
$missing_fields = array();

foreach ($required_fields as $field) {
    if (!isset($data->$field) || empty(trim($data->$field))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Required data is missing: " . implode(", ", $missing_fields),
        "missing_fields" => $missing_fields,
        "received_data" => $data
    ));
} else {
    // All required fields are present, proceed with creation
    $reservation->event_name = trim($data->event_name);
    $reservation->facility = trim($data->facility);
    $reservation->start_datetime = trim($data->start_datetime);
    $reservation->end_datetime = trim($data->end_datetime);
    $reservation->status = 'Pending'; // Always set to pending for guest reservations
    $reservation->requested_by = trim($data->requested_by);
    $reservation->contact_number = trim($data->contact_number);
    $reservation->purpose = isset($data->purpose) ? trim($data->purpose) : '';
    $reservation->expected_attendees = isset($data->expected_attendees) ? intval($data->expected_attendees) : 0;
    $reservation->user_id = isset($data->user_id) && !empty($data->user_id) ? intval($data->user_id) : null;
    $reservation->official_id = null; // Guest reservations don't have official assignment initially
    
    if ($reservation->create()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Reservation created successfully.",
            "data" => array(
                "id" => $reservation->id,
                "status" => "Pending"
            )
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to create reservation. Database error occurred."
        ));
    }
}
?>