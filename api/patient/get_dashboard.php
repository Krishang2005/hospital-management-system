<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

$pending = $conn->query("SELECT COUNT(*) FROM appointments WHERE user_id = $user_id AND status = 'Pending'")->fetch_row()[0];
$confirmed = $conn->query("SELECT COUNT(*) FROM appointments WHERE user_id = $user_id AND status = 'Confirmed'")->fetch_row()[0];
$total = $conn->query("SELECT COUNT(*) FROM appointments WHERE user_id = $user_id")->fetch_row()[0];

sendJsonResponse('success', 'Dashboard loaded', [
    'user_name' => $user_name,
    'total' => $total,
    'pending' => $pending,
    'confirmed' => $confirmed
]);
?>
