<?php
// api/endpoints/search_reservations.php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

include_once '../config/database.php';
include_once '../models/reservation.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate reservation object
$reservation = new Reservation($db);

// Get search keywords from URL parameters
$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';

if (empty($keywords)) {
    http_response_code(400);
    echo json_encode(array('message' => 'Search keywords parameter is required'));
    exit();
}

// Search reservations
$result = $reservation->search($keywords);
$num = $result->rowCount();

// Check if any reservations found
if ($num > 0) {
    $reservations_arr = array();
    $reservations_arr['data'] = array();
    $reservations_arr['search_query'] = $keywords;

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $reservation_item = array(
            'id' => $id,
            'user_id' => $user_id,
            'official_id' => $official_id,
            'official_name' => $official_name ?? 'N/A',
            'official_position' => $official_position ?? 'N/A',
            'facility' => $facility,
            'event_name' => $event_name,
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime,
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

    http_response_code(200);
    echo json_encode($reservations_arr);
} else {
    http_response_code(404);
    echo json_encode(array(
        'message' => 'No reservations found matching keywords: ' . $keywords,
        'search_query' => $keywords
    ));
}
?>
