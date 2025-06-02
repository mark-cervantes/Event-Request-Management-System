<?php
// api/endpoints/update_reservation_status.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);
$data = json_decode(file_get_contents("php://input"));

// Set reservation properties
$reservation->id = $data->id ?? null;
$reservation->status = $data->status ?? null;
$reservation->official_id = $data->official_id ?? null;

// Validate required fields
if (!empty($reservation->id) && !empty($reservation->status)) {
    if ($reservation->updateStatus()) {
        echo json_encode(['message' => 'Reservation status updated successfully']);
    } else {
        echo json_encode(['message' => 'Reservation status not updated']);
    }
} else {
    echo json_encode(['message' => 'Missing required fields: id and status']);
}
?>
