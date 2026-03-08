<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    sendJsonResponse('success', 'Session active', [
        'user_id' => $_SESSION['user_id'],
        'name' => $_SESSION['name'],
        'role' => $_SESSION['role']
    ]);
} else {
    sendJsonResponse('error', 'No active session');
}
?>
