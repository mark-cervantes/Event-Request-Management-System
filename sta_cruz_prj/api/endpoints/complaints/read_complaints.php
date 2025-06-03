<?php
// api/endpoints/read_complaints.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/complaints.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate Complaints
$complaint = new ComplaintForm($db);

// Complaints query
$result = $complaint->read();
$num = $result->rowCount();

if ($num > 0) {
    $complaints_arr = array();
    $complaints_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $complaint_item = array(
            'brngy_case_no' => $brngy_case_no,
            'case_type' => $case_type,
            'complainant_fullname' => $complainant_fullname,
            'complainant_address' => $complainant_address,
            'complainant_cellphone' => $complainant_cellphone,
            'respondent_fullname' => $respondent_fullname,
            'respondent_address' => $respondent_address,
            'respondent_cellphone' => $respondent_cellphone,
            'complaint_description' => $complaint_description,
            'attachment_file' => $attachment_file,
            'date_of_incident' => $date_of_incident,
            'submitted_date' => $submitted_date,
            'status' => $status
        );

        array_push($complaints_arr['data'], $complaint_item);
    }

    echo json_encode($complaints_arr);
} else {
    echo json_encode(array("message" => "No complaints found"));
}
?>