<?php
// api/endpoints/readOfficial.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/official.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate Official
$official = new Official($db);

// Official query
$result = $official->read();

// Get row count
$num = $result->rowCount();

// Check if there are officials
if ($num > 0) {
    $officials_arr = array();
    $officials_arr['data'] = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $official_item = array(
            'id' => $row['id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'gender' => $row['gender'],
            'birth_date' => $row['birth_date'],
            'position_title' => $row['position_title'],
            'email' => $row['email'],
            'contact_number' => $row['contact_number'],
            'start_term' => $row['start_term'],
            'end_term' => $row['end_term'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        );

        // Push to 'data' array
        array_push($officials_arr['data'], $official_item);
    }

    // Convert to JSON and output
    echo json_encode($officials_arr);
} else {
    // No officials found
    echo json_encode(array('message' => 'No officials found'));
}
?>
