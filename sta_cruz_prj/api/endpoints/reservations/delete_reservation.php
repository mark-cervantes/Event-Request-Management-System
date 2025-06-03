<?php
// api/endpoints/delete_reservation.php

// Include centralized CORS configuration
include_once '../../config/cors.php';

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->connect();

$reservation = new Reservation($db);
$data = json_decode(file_get_contents("php://input"));

$reservation->id = $data->id ?? null;

// Validate required field
if (!empty($reservation->id)) {
    if ($reservation->delete()) {
        echo json_encode(['message' => 'Reservation deleted successfully']);
    } else {
        echo json_encode(['message' => 'Reservation not deleted']);
    }
} else {
    echo json_encode(['message' => 'Missing required field: id']);
}
?>
