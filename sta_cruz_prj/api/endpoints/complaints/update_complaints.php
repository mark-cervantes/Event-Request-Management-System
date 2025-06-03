<?php
// api/endpoints/update_complaint.php

date_default_timezone_set('Asia/Manila');

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
$db = $database->connect();

// Instantiate complaint object
$complaint = new ComplaintForm($db);

// Get raw posted data as associative array
$data = json_decode(file_get_contents("php://input"), true);

// Check if brngy_case_no is provided
if (empty($data['brngy_case_no'])) {
    http_response_code(400);
    echo json_encode(['message' => 'brngy_case_no is required']);
    exit();
}

// Call the partial update method
if ($complaint->updatePartial($data)) {
    echo json_encode(['message' => 'Complaint Updated!']);
} else {
    http_response_code(503);
    echo json_encode(['message' => 'Complaint not Updated']);
}