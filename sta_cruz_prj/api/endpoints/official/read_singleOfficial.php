<?php
// api/endpoints/read_singleOfficial.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 0); 
error_reporting(E_ALL);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/official.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Official object
$official = new Official($db);

// Get id from query parameter (GET method)
if (isset($_GET['id'])) {
    $official->id = $_GET['id'];

    // Try to read single official record
    if ($official->read_single()) {
        // Return official data as JSON
        echo json_encode([
            'message' => 'Official record found',
            'official' => [
                'id' => $official->id,
                'first_name' => $official->first_name,
                'middle_name' => $official->middle_name,
                'last_name' => $official->last_name,
                'gender' => $official->gender,
                'birth_date' => $official->birth_date,
                'position_title' => $official->position_title,
                'email' => $official->email,
                'contact_number' => $official->contact_number,
                'start_term' => $official->start_term,
                'end_term' => $official->end_term,
                'created_at' => $official->created_at,
                'updated_at' => $official->updated_at
            ]
        ]);
    } else {
        // If no record is found
        echo json_encode([
            'message' => 'No official found'
        ]);
    }
} else {
    // If id is missing
    echo json_encode([
        'message' => 'Invalid input: id not provided'
    ]);
}
?>
