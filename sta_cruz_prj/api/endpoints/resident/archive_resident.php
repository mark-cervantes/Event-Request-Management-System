<?php
// api/endpoints/resident/archive_resident.php
// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/resident.php';

// Instantiate database 
$database = new Database();
$db = $database->connect();

// Instantiate resident object
$resident = new Resident($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input")); 

if (!isset($data->resident_id)) {
    echo json_encode(['message' => 'Resident ID is required']);
    return;
}

// Set resident ID
$resident->resident_id = $data->resident_id;

// Archive Resident (update status)
if ($resident->archive()) {
    echo json_encode(['message' => 'Resident Archived!']);
} else {
    echo json_encode(['message' => 'Resident not Archived']);
}
?>
