<?php
// api/endpoints/createOfficial.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');	
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/official.php'; 

// Instantiate database and connect
$database = new Database();
$db = $database->connect();

// Instantiate official object
$official = new Official($db); 

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Set official properties
$official->first_name = $data->first_name ?? null;
$official->middle_name = $data->middle_name ?? null;
$official->last_name = $data->last_name ?? null;
$official->gender = $data->gender ?? null;
$official->birth_date = $data->birth_date ?? null;
$official->position_title = $data->position_title ?? null;
$official->email = $data->email ?? null;
$official->contact_number = $data->contact_number ?? null;
$official->start_term = $data->start_term ?? null;
$official->end_term = $data->end_term ?? null;

// Create official
if ($official->create()) {
    echo json_encode(array('message' => 'Official Inserted'));
} else {
    echo json_encode(array('message' => 'Official Not Inserted'));
}
?>
