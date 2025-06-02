<?php
// api/endpoints/delete_admin.php
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

// Set admin ID to delete
$admin->admin_id = $data->admin_id;

// Delete Admin
if ($admin->delete()) {
    echo json_encode(['message' => 'Admin Deleted!']);
} else {
    echo json_encode(['message' => 'Admin not Deleted']);
}
?>
