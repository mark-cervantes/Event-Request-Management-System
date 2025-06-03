<?php
// api/endpoints/create_complaint.php
date_default_timezone_set('Asia/Manila');

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/complaints.php';

// Handle OPTIONS preflight request and exit early
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate complaint object
$complaint = new ComplaintForm($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

/// Validate required fields (excluding brngy_case_no)
if (
    empty($data->case_type) || empty($data->complainant_fullname) ||
    empty($data->complainant_address) || empty($data->complainant_cellphone) ||
    empty($data->respondent_fullname) || empty($data->respondent_address) ||
    empty($data->respondent_cellphone) || empty($data->complaint_description) ||
    empty($data->date_of_incident)
) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Incomplete data. Please fill all required fields.']);
    exit();
}

// Generate brngy_case_no on server
$complaint->brngy_case_no = $complaint->generateBrngyCaseNo();

// Set other properties
$complaint->case_type = $data->case_type;
$complaint->complainant_fullname = $data->complainant_fullname;
$complaint->complainant_address = $data->complainant_address;
$complaint->complainant_cellphone = $data->complainant_cellphone;
$complaint->respondent_fullname = $data->respondent_fullname;
$complaint->respondent_address = $data->respondent_address;
$complaint->respondent_cellphone = $data->respondent_cellphone;
$complaint->complaint_description = $data->complaint_description;
$complaint->attachment_file = isset($data->attachment_file) ? $data->attachment_file : null;
$complaint->date_of_incident = $data->date_of_incident;

// Create complaint
if ($complaint->create()) {
    http_response_code(201); // Created
    echo json_encode([
        'message' => 'Complaint Added!',
        'brngy_case_no' => $complaint->brngy_case_no // return generated case no
    ]);
} else {
    http_response_code(503); // Service unavailable
    echo json_encode(['message' => 'Complaint not Added']);
}