<?php
// api/endpoints/create_singleOfficial.php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');	
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/official.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Official object
$official = new Official($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Basic validation for required fields
if (
    !empty($data->first_name) &&
    !empty($data->last_name) &&
    !empty($data->position_title) &&
    !empty($data->start_term)
) {
    // Set official properties
    $official->first_name = $data->first_name;
    $official->middle_name = $data->middle_name ?? null;
    $official->last_name = $data->last_name;
    $official->gender = $data->gender ?? null;
    $official->birth_date = $data->birth_date ?? null;
    $official->position_title = $data->position_title;
    $official->email = $data->email ?? null;
    $official->contact_number = $data->contact_number ?? null;
    $official->start_term = $data->start_term;
    $official->end_term = $data->end_term ?? null;

    // Create official
    if ($official->create()) {
        http_response_code(201); // Created
        echo json_encode(['message' => 'Official created successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Official not created']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Missing required fields: first_name, last_name, position_title, start_term']);
}
?>
