<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = getJsonInput();
    $phone = isset($input['phone']) ? $conn->real_escape_string($input['phone']) : null;
    $new_password = isset($input['new_password']) ? $input['new_password'] : null;
    $user_id = $_SESSION['user_id'];

    if (!$phone) {
        sendJsonResponse('error', 'Phone number is required');
    }

    $update_query = "UPDATE users SET phone = '$phone'";
    
    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query .= ", password = '$hashed'";
    }
    
    $update_query .= " WHERE id = $user_id";
    
    if ($conn->query($update_query)) {
        sendJsonResponse('success', 'Profile updated successfully!');
    } else {
        sendJsonResponse('error', 'Error updating profile.');
    }
} else {
    // GET request logic to fetch user profile details
    $user_id = $_SESSION['user_id'];
    $user = $conn->query("SELECT name, email, phone FROM users WHERE id = $user_id")->fetch_assoc();
    sendJsonResponse('success', 'Profile loaded', $user);
}
?>
