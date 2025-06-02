<?php
// api/endpoints/read_admin.php
// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/admin.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate Admin
$admin = new Admin($db);

// Admin query
$result = $admin->read();
$num = $result->rowCount();

// Check if there are records
if ($num > 0){
    $admin_arr = array();
    $admin_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $admin_item = array(
            'admin_id' => $admin_id,
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'official_id' => $official_id
        );

        array_push($admin_arr['data'], $admin_item);
    }

    echo json_encode($admin_arr);
} else {
    echo json_encode(array("message" => "No admins found"));
}
?>
