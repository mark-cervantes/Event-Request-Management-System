<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/database.php';
include_once '../../models/complaints.php';

$database = new Database();
$db = $database->connect();

$complaint = new ComplaintForm($db);

// Generate next brngy_case_no
$nextCaseNo = $complaint->generateBrngyCaseNo();

echo json_encode(['next_case_no' => $nextCaseNo]);