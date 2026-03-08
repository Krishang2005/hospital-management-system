<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = getJsonInput();
    
    $email = isset($input['email']) ? $input['email'] : (isset($_POST['email']) ? $_POST['email'] : null);
    $password = isset($input['password']) ? $input['password'] : (isset($_POST['password']) ? $_POST['password'] : null);
    $is_admin_login = isset($input['admin_login']) ? filter_var($input['admin_login'], FILTER_VALIDATE_BOOLEAN) : false;

    if (!$email || !$password) {
        sendJsonResponse('error', 'Email and password are required.');
    }

    $email = $conn->real_escape_string($email);

    $query = "SELECT * FROM users WHERE email = '$email'";
    if ($is_admin_login) {
        $query .= " AND role = 'admin'";
    }

    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            sendJsonResponse('success', 'Login successful', ['role' => $user['role'], 'name' => $user['name']]);
        } else {
            sendJsonResponse('error', 'Invalid password.');
        }
    } else {
        $error_msg = $is_admin_login ? 'No admin found with this email.' : 'No user found with this email.';
        sendJsonResponse('error', $error_msg);
    }
} else {
    sendJsonResponse('error', 'Invalid request method.');
}
?>
