<?php
//api/endpoints/delete_resident.php
//author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/resident.php';

//Instantiate database 
$database = new Database();
$db = $database->connect();

//Instantiate resident object
$resident = new Resident($db);
 
//get raw posted data
$data = json_decode(file_get_contents("php://input")); 

//set resident properties
$resident->resident_id = $data->resident_id;

//Delete Resident
if($resident->delete()){
    //convert to JSON and output
    echo json_encode(
        array('message' => 'Resident Deleted!')
    );
}else{
    //no resident deleted
    echo json_encode(
        array('message' => 'Resident not Deleted')
    );
}
?>
