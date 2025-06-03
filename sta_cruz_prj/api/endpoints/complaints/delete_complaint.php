<?php
// api/endpoints/delete_complaint.php// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/complaints.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Instantiate database 
$database = new Database();
$db = $database->connect();  // Adjust method if needed

// Instantiate complaint object
$complaint = new ComplaintForm($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Validate brngy_case_no is provided
if (empty($data->brngy_case_no)) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Barangay Case Number (brngy_case_no) is required']);
    exit();
}

// Set brngy_case_no to delete
$complaint->brngy_case_no = $data->brngy_case_no;

// Delete complaint
if ($complaint->delete()) {
    http_response_code(200);
     echo json_encode(['message' => 'Complaint Deleted!']);
} else {
    http_response_code(503); // Service unavailable
    echo json_encode(['message' => 'Complaint not Deleted']);
}