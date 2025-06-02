<?php
// api/resident/read_count_resident.php
// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/resident.php';

$database = new Database();
$db = $database->connect();

$resident = new Resident($db);

$stmt = $resident->read();
$num = $stmt->rowCount();

echo json_encode([
    'total_residents' => $num
]);
?>
