<?php
// api/endpoints/read_resident.php
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

// Instantiate Resident
$resident = new Resident($db);

// Resident query
$result = $resident->read();
$num = $result->rowCount();

// Check if there are records
if ($num > 0){
    $resident_arr = array();
    $resident_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $resident_item = array(
            'resident_id' => $resident_id,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'contact_number' => $contact_number,
            'address' => $address,
            'birth_date' => $birth_date,
            'gender' => $gender,
            'civil_status' => $civil_status,
            'household_no' => $household_no,
            'religion' => $religion,
            'status' => $status
        );

        array_push($resident_arr['data'], $resident_item);
    }

    echo json_encode($resident_arr);
} else {
    echo json_encode(array("message" => "No residents found"));
}
?>
