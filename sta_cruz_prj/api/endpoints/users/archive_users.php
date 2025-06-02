<?php
// api/endpoints/resident/archive_users.php
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

$users = new Users($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input")); 

if (!isset($data->user_id)) {
    echo json_encode(['message' => 'User ID is required']);
    return;
}

$users->user_id = $data->user_id;

if ($users->archive()) {
    echo json_encode(['message' => 'User Archived!']);
} else {
    echo json_encode(['message' => 'User not Archived']);
}
?>
