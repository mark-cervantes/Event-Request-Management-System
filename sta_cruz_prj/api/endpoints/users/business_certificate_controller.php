<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../../config/database.php';

// Instantiate database
$database = new Database();
$db = $database->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Handle JSON data with base64 images
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['action'])) {
        echo json_encode(['message' => 'No action specified']);
        exit;
    }

    $action = $data['action'];

    if ($action === 'add') {
        // Add new business certificate
        if (
            isset($data['business_type']) &&
            isset($data['status']) &&
            isset($data['business_name']) &&
            isset($data['business_address']) &&
            isset($data['date_registered']) &&
            isset($data['issued_date']) &&
            isset($data['expiry_date']) &&
            isset($data['business_owner'])
        ) {
            // Convert status to integer: 1 for 'Approved', 0 otherwise
            $status = (strtolower($data['status']) === 'approved') ? 1 : 0;
    
            $query = "INSERT INTO business_certificate 
                (business_type, status, business_name, business_address, date_registered, issued_date, image_data, expiry_date, business_owner)
                VALUES
                (:business_type, :status, :business_name, :business_address, :date_registered, :issued_date, :image_data, :expiry_date, :business_owner)";
            $stmt = $db->prepare($query);
    
            // Assign variables for bindParam (requires references)
            $business_type = $data['business_type'];
            $business_name = $data['business_name'];
            $business_address = $data['business_address'];
            $date_registered = $data['date_registered'];
            $issued_date = $data['issued_date'];
            $image_data = $data['image_data'] ?? null;
            $expiry_date = $data['expiry_date'];
            $business_owner = $data['business_owner'];
            
            $stmt->bindParam(':business_type', $business_type);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':business_name', $business_name);
            $stmt->bindParam(':business_address', $business_address);
            $stmt->bindParam(':date_registered', $date_registered);
            $stmt->bindParam(':issued_date', $issued_date);
            $stmt->bindParam(':image_data', $image_data);
            $stmt->bindParam(':expiry_date', $expiry_date);
            $stmt->bindParam(':business_owner', $business_owner);
    
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Business certificate added']);
                exit;
            } else {
                echo json_encode(['message' => 'Failed to add business certificate']);
                exit;
            }
        } else {
            echo json_encode(['message' => 'Incomplete data']);
            exit;
        }
    }
    if ($action === 'edit' && isset($data['id'])) {
        // Update business certificate
        $id = $data['id'];
    
        $fields = [];
        $params = [':id' => $id];
    
        foreach (['business_type', 'status', 'business_name', 'business_address', 'date_registered', 'issued_date', 'image_data', 'expiry_date', 'business_owner'] as $field) {
            if (isset($data[$field])) {
                if ($field === 'status') {
                    // Convert status to integer: 1 for 'Approved', 0 otherwise
                    $statusValue = (strtolower($data[$field]) === 'approved') ? 1 : 0;
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $statusValue;
                } else {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
        }
    
        if (!empty($fields)) {
            $query = "UPDATE business_certificate SET " . implode(', ', $fields) . " WHERE business_certificate_id = :id";
            $stmt = $db->prepare($query);
    
            if ($stmt->execute($params)) {
                echo json_encode(['message' => 'Business certificate updated']);
            } else {
                echo json_encode(['message' => 'Failed to update business certificate']);
            }
        } else {
            echo json_encode(['message' => 'No fields to update']);
        }
        exit;
    }

    if ($action === 'delete' && isset($data['id'])) {
        // Delete business certificate
        $id = $data['id'];
        $query = "DELETE FROM business_certificate WHERE business_certificate_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Business certificate deleted']);
        } else {
            echo json_encode(['message' => 'Failed to delete business certificate']);
        }
        exit;
    }

    echo json_encode(['message' => 'Invalid action or missing parameters', 'action' => $action]);
    exit;
 
}

// GET request (default)
$condition = "";
$params = [];
if (isset($_GET['id'])) {
    $condition = " WHERE business_owner = :id";
    $params[':id'] = $_GET['id'];
}

$query = "SELECT * FROM business_certificate{$condition}";
$stmt = $db->prepare($query);
$stmt->execute($params);

$num = $stmt->rowCount();

if ($num > 0) {
    $cert_arr = [];
    $cert_arr['data'] = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cert_item = array(
            'business_certificate_id' => $row['business_certificate_id'],
            'business_type' => $row['business_type'],
            'status' => $row['status'],
            'business_name' => $row['business_name'],
            'business_address' => $row['business_address'],
            'date_registered' => $row['date_registered'],
            'issued_date' => $row['issued_date'],
            'image_data' => $row['image_data'],
            'expiry_date' => $row['expiry_date'],
            'business_owner' => $row['business_owner']
        );

        array_push($cert_arr['data'], $cert_item);
    }

    echo json_encode($cert_arr);
} else {
    echo json_encode(["message" => "No business certificates found"]);
}
