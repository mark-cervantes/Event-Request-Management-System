<?php
// api/endpoints/read_users.php
// author: Aldrin Danila

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/users.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

// Instantiate Users
$user = new users($db);

// Users query
$result = $user->read();
$num = $result->rowCount();

// Check if there are records
if ($num > 0){
    $user_arr = array();
    $user_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $user_item = array(
            'user_id' => $user_id,
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'role' => $role,
            'status' => $status,
            'resident_id' => $resident_id,
            'start_term' => $start_term,
            'end_term' => $end_term,
            'position_title' => $position_title

        );

        array_push($user_arr['data'], $user_item);
    }

    echo json_encode($user_arr);
} else {
    echo json_encode(array("message" => "No users found"));
}
?>
