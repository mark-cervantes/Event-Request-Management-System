<?php
// api/endpoints/create_user.php
// author: Aldrin Danila
date_default_timezone_set('Asia/Manila');

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/users.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate user object
$user = new Users($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input")); 

// Set user properties (raw, no password hashing)
$user->user_id = $data->user_id;
$user->username = $data->username;
$user->password = $data->password;
$user->email = $data->email;   
$user->role = $data->role;
$user->status = $data->status;
$user->resident_id = $data->resident_id;
$user->start_term = $data->start_term;
$user->end_term = $data->end_term;
$user->position_title = $data->position_title;


// Check if resident already has a user account
$query = "SELECT * FROM users WHERE resident_id = :resident_id AND status != 'archived' LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':resident_id', $user->resident_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['message' => 'This resident already has an account']);
    return;
}
// Create User
if($user->create()){
    echo json_encode(['message' => 'User Added!']);
} else {
    echo json_encode(['message' => 'User not Added']);
}
?>
