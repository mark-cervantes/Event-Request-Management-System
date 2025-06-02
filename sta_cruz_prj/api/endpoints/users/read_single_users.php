<?php
// api/endpoints/read_single_user.php
// author: Aldrin Danila 

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/users.php';

// instantiate database
$database = new Database();
$db = $database->connect();

// instantiate user object
$user = new users($db);

// get raw posted data
$data = json_decode(file_get_contents("php://input"));

// set user_id to read
$user->user_id = $data->user_id ?? null;

// user query - read single user
$user->read_single();

// check if user exists (you can check a required field like username)
if ($user->username != null) {
    echo json_encode($user);
} else {
    echo json_encode(['message' => 'No user found']);
}
?>
