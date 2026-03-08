<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = getJsonInput();
    $id = intval($input['appointment_id']);
    $user_id = $_SESSION['user_id'];
    
    if ($conn->query("UPDATE appointments SET status = 'Cancelled' WHERE id = $id AND user_id = $user_id AND status IN ('Pending', 'Confirmed')")) {
        sendJsonResponse('success', 'Appointment cancelled');
    } else {
        sendJsonResponse('error', 'Failed to cancel appointment');
    }
}
?>
