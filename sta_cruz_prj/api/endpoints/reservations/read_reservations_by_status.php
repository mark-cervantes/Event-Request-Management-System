<?php
// api/endpoints/read_reservations_by_status.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/reservation.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate Reservation
$reservation = new Reservation($db);

// Get status from query parameter
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    
    // Reservation query by status
    $result = $reservation->getByStatus($status);
    
    // Get row count
    $num = $result->rowCount();
    
    // Check if there are reservations
    if ($num > 0) {
        $reservations_arr = array();
        $reservations_arr['data'] = array();
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $reservation_item = array(
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'official_id' => $row['official_id'],
                'official_name' => $row['official_name'] ?? null,
                'official_position' => $row['official_position'] ?? null,
                'facility' => $row['facility'],
                'event_name' => $row['event_name'],
                'start_datetime' => $row['start_datetime'],
                'end_datetime' => $row['end_datetime'],
                'date_time_requested' => $row['date_time_requested'],
                'status' => $row['status'],
                'requested_by' => $row['requested_by'],
                'contact_number' => $row['contact_number'],
                'purpose' => $row['purpose'],
                'expected_attendees' => $row['expected_attendees'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
            
            // Push to 'data' array
            array_push($reservations_arr['data'], $reservation_item);
        }
        
        // Convert to JSON and output
        echo json_encode($reservations_arr);
    } else {
        // No reservations found
        echo json_encode(array('message' => 'No reservations found with status: ' . $status));
    }
} else {
    // If status is missing
    echo json_encode([
        'message' => 'Invalid input: status not provided'
    ]);
}
?>
