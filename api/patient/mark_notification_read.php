<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$input = getJsonInput();
$notification_id = intval($input['notification_id']);
$user_id = $_SESSION['user_id'];

if ($notification_id <= 0) {
    sendJsonResponse('error', 'Invalid notification ID');
}

$sql = "UPDATE notifications SET is_read = 1 WHERE id = $notification_id AND user_id = $user_id";

if ($conn->query($sql)) {
    sendJsonResponse('success', 'Notification marked as read');
} else {
    sendJsonResponse('error', 'Failed to update notification');
}
?>
