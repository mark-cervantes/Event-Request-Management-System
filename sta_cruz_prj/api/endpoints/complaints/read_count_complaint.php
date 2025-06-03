<?php
// api/complaint/read_count_complaints.php


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/complaints.php';

$database = new Database();
$db = $database->connect();

$complaints = new ComplaintForm($db);

$stmt = $complaints->read();
$num = $stmt->rowCount();

echo json_encode([
    'total_complaints' => $num
]);
?>