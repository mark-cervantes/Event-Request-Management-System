<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->getConnection();

$reservation = new Reservation($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->event_name) && !empty($data->facility) && !empty($data->start_datetime) && 
    !empty($data->end_datetime) && !empty($data->requested_by) && !empty($data->contact_number)) {
    
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
            )
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to create reservation."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to create reservation. Required data is missing."
    ));
}
?>