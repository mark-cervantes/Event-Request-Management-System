<?php
// api/endpoints/read_single_complaint.php


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/complaints.php';

// instantiate database
$database = new Database();
$db = $database->connect();

// instantiate complaint object
$complaint = new ComplaintForm($db);

// get raw posted data
$data = json_decode(file_get_contents("php://input"));

// set brngy_case_no to read
$complaint->brngy_case_no = $data->brngy_case_no ?? null;

// complaint query - read single complaint
$complaint->read_single();

// check if complaint exists (you can check a required field like complainant_fullname)
if ($complaint->complainant_fullname != null) {
    echo json_encode($complaint);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'No complaint found']);
}