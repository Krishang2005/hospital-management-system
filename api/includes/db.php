<?php
// Added header to allow returning JSON responses easily
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "hospital_system";
$port = 3307; 

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$conn->select_db($dbname);

// Function to send standardized JSON responses
function sendJsonResponse($status, $message, $data = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}
?>
