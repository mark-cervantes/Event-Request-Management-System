<?php
// api/endpoints/delete_user.php
// author: Aldrin Danila

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

// Set user ID to delete
$user->user_id = $data->user_id;

// Delete User
if ($user->delete()) {
    echo json_encode(['message' => 'User Deleted!']);
} else {
    echo json_encode(['message' => 'User not Deleted']);
}
?>
