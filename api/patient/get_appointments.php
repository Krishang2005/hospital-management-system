<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
$user_id = $_SESSION['user_id'];

$sql = "SELECT a.*, d.doctor_name, d.specialization, d.hospital 
        FROM appointments a JOIN doctors d ON a.doctor_id = d.id 
        WHERE a.user_id = $user_id ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = $conn->query($sql);

$appointments = [];
while($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
sendJsonResponse('success', 'Loaded appointments', $appointments);
?>
