<?php
// api/endpoints/update_admin.php
// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/admin.php';

// Instantiate database 
$database = new Database();
$db = $database->connect();

// Instantiate admin object
$admin = new Admin($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input")); 

// Set admin properties
$admin->admin_id = $data->admin_id;
$admin->username = $data->username;
$admin->password = $data->password;
$admin->email = $data->email;
$admin->created_at = $data->created_at;
$admin->updated_at = $data->updated_at;
$admin->official_id = $data->official_id;

// Update Admin
if ($admin->update()) {
    echo json_encode(['message' => 'Admin Updated!']);
} else {
    echo json_encode(['message' => 'Admin not Updated']);
}
?>
