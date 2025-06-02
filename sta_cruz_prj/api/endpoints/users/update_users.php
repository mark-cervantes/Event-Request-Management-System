<?php
// api/endpoints/update_user.php
// author: Aldrin Danila
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Manila');

header('Access-Control-Allow-Origin: *');
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

if (empty($data)) {
    echo json_encode(['message' => 'No data received']);
    exit;
}

// Set user properties
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

// Update User
if ($user->update()) {
    echo json_encode(['message' => 'User Updated!']);
} else {
    echo json_encode(['message' => 'User not Updated']);
}
?>
