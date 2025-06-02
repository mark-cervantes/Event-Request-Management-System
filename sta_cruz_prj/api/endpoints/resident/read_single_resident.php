<?php
// api/endpoints/read_single_resident.php
// author: Aldrin Danila 

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/resident.php';

// instantiate database
$database = new Database();
$db = $database->connect();

// instantiate resident object
$resident = new Resident($db);

// get raw posted data
$data = json_decode(file_get_contents("php://input"));

// set Resident_ID to read
$resident->resident_id = $data->resident_id ?? null;

// resident query - read single resident
$resident->read_single();

// check if resident exists
if ($resident->first_name != null) {
    echo json_encode($resident);
} else {
    echo json_encode(['message' => 'No resident found']);
}
?>
