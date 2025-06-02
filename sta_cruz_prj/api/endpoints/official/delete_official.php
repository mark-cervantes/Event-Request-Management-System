<?php
// api/endpoints/delete_official.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE, POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/official.php';

$database = new Database();
$db = $database->connect();

$official = new Official($db);
$data = json_decode(file_get_contents("php://input"));

$official->id = $data->id ?? null;

// Validate required field
if (!empty($official->id)) {
    if ($official->delete()) {
        echo json_encode(['message' => 'Official deleted successfully']);
    } else {
        echo json_encode(['message' => 'Official not deleted']);
    }
} else {
    echo json_encode(['message' => 'Missing required field: id']);
}
?>
