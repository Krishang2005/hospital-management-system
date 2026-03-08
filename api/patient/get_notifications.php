<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, message, created_at FROM notifications WHERE user_id = $user_id AND is_read = 0 ORDER BY created_at DESC";
$result = $conn->query($sql);

$notifications = [];
while($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

sendJsonResponse('success', 'Notifications fetched', $notifications);
?>
