<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = getJsonInput();
    
    // Fallback to $_POST if not JSON
    $name = isset($input['name']) ? $input['name'] : (isset($_POST['name']) ? $_POST['name'] : null);
    $email = isset($input['email']) ? $input['email'] : (isset($_POST['email']) ? $_POST['email'] : null);
    $phone = isset($input['phone']) ? $input['phone'] : (isset($_POST['phone']) ? $_POST['phone'] : null);
    $password = isset($input['password']) ? $input['password'] : (isset($_POST['password']) ? $_POST['password'] : null);

    if (!$name || !$email || !$password || !$phone) {
        sendJsonResponse('error', 'All fields are required.');
    }

    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $phone = $conn->real_escape_string($phone);

    // Check if email exists
    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        sendJsonResponse('error', 'Email already registered!');
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$hashed_password', 'patient')";
        
        if ($conn->query($sql)) {
            sendJsonResponse('success', 'Registration successful! You can now login.');
        } else {
            sendJsonResponse('error', 'Database error: ' . $conn->error);
        }
    }
} else {
    sendJsonResponse('error', 'Invalid request method.');
}
?>
