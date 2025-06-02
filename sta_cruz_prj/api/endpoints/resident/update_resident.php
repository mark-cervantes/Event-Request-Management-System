<?php
// api/endpoints/update_resident.php
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

// Set resident properties
$resident->resident_id = $data->resident_id;
$resident->first_name = $data->first_name;
$resident->middle_name = $data->middle_name;
$resident->last_name = $data->last_name;
$resident->contact_number = $data->contact_number;
$resident->address = $data->address;
$resident->birth_date = $data->birth_date;
$resident->gender = $data->gender;
$resident->civil_status = $data->civil_status;
$resident->household_no = $data->household_no;
$resident->religion = $data->religion;
$resident->status = $data->status;

// Update Resident
if ($resident->update()) {
    echo json_encode(array('message' => 'Resident Updated!'));
} else {
    echo json_encode(array('message' => 'Resident not Updated'));
}
?>
