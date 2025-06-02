<?php
// api/endpoints/read_single_admin.php
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

// Set admin_id to read
$admin->admin_id = $data->admin_id ?? null;

// Admin query - read single admin
$admin->read_single();

// Check if admin exists
if ($admin->username != null) {
    echo json_encode($admin);
} else {
    echo json_encode(['message' => 'No admin found']);
}
?>
