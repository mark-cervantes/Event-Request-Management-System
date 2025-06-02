<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../../config/database.php';
include_once '../../models/reservation.php';

$database = new Database();
$db = $database->getConnection();

$reservation = new Reservation($db);

// Get user_id from query parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'message' => 'User ID is required'
    ));
    exit();
}

try {
    // Query to get reservations for specific user
    $query = "SELECT 
                r.id,
                r.user_id,
                r.official_id,
                r.facility,
                r.event_name,
                r.start_datetime,
                r.end_datetime,
                r.status,
                r.requested_by,
                r.contact_number,
                r.purpose,
                r.expected_attendees,
                r.created_at,
                r.updated_at,
                u.username as user_username,
                o.name as official_name
              FROM reservations r
              LEFT JOIN users u ON r.user_id = u.id
              LEFT JOIN officials o ON r.official_id = o.id
              WHERE r.user_id = :user_id
              ORDER BY r.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $reservations = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $reservation_item = array(
            'id' => $id,
            'user_id' => $user_id,
            'official_id' => $official_id,
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
            'updated_at' => $updated_at,
            'user_username' => $user_username,
            'official_name' => $official_name
        );
        
        array_push($reservations, $reservation_item);
    }

    http_response_code(200);
    echo json_encode(array(
        'success' => true,
        'data' => $reservations,
        'count' => count($reservations)
    ));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Error retrieving user reservations: ' . $e->getMessage()
    ));
}
?>
