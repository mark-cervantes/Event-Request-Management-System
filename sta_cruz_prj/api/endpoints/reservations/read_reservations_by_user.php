<?php
// api/endpoints/reservations/read_reservations_by_user.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/reservation.php';

// Instantiate database and connect
$database = new Database();
$db = $database->connect();

// Instantiate reservation object
$reservation = new Reservation($db);

// Get user_id from URL parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(['message' => 'User ID is required']);
    exit;
}

// Get reservations by user
$stmt = $reservation->getByUser($user_id);
$num = $stmt->rowCount();

if ($num > 0) {
    $reservations_arr = array();
    $reservations_arr['data'] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $reservation_item = array(
            'id' => $id,
            'user_id' => $user_id,
            'official_id' => $official_id,
            'official_name' => $official_name,
            'official_position' => $official_position,
            'created_by_username' => $created_by_username,
            'created_by_name' => $created_by_name,
            'facility' => $facility,
            'event_name' => $event_name,
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
            'date_time_requested' => $date_time_requested,
            'status' => $status,
            'requested_by' => $requested_by,
            'contact_number' => $contact_number,
            'purpose' => $purpose,
            'expected_attendees' => $expected_attendees,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        );

        array_push($reservations_arr['data'], $reservation_item);
    }

    echo json_encode($reservations_arr);
} else {
    echo json_encode(array('message' => 'No reservations found for this user'));
}
?>
