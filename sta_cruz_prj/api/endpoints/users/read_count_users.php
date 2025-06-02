<?php
// api/resident/read_count_users.php
// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/users.php';

$database = new Database();
$db = $database->connect();

$users = new Users($db);

$stmt = $users->read();
$num = $stmt->rowCount();

echo json_encode([
    'total_users' => $num
]);
?>
