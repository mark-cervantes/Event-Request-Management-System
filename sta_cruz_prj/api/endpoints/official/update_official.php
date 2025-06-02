<?php
// api/endpoints/update_official.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/official.php';

$database = new Database();
$db = $database->connect();

$official = new Official($db);
$data = json_decode(file_get_contents("php://input"));

// Set official properties
$official->id = $data->id ?? null;
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

// Validate required fields
if (!empty($official->id)) {
    if ($official->update()) {
        echo json_encode(['message' => 'Official updated successfully']);
    } else {
        echo json_encode(['message' => 'Official not updated']);
    }
} else {
    echo json_encode(['message' => 'Missing required field: id']);
}
?>
