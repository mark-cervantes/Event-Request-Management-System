<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);

// Get user_id from query parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($user_id) {
    // Read reservations for specific user
    $stmt = $reservation->getByUser($user_id);
} else {
    // If no user_id provided, return empty result
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "User ID is required."
    ));
    exit;
}

$num = $stmt->rowCount();

if ($num > 0) {
    $reservations_arr = array();
    $reservations_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $reservation_item = array(
            "id" => $id,
            "user_id" => $user_id,
            "official_id" => $official_id,
            "facility" => $facility,
            "event_name" => $event_name,
            "start_datetime" => $start_datetime,
            "end_datetime" => $end_datetime,
            "status" => $status,
            "requested_by" => $requested_by,
            "contact_number" => $contact_number,
            "purpose" => $purpose,
            "expected_attendees" => $expected_attendees,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($reservations_arr["records"], $reservation_item);
    }

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $reservations_arr["records"]
    ));
} else {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => array(),
        "message" => "No reservations found for this user."
    ));
}
?>