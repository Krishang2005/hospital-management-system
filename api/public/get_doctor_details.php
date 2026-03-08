<?php
require_once '../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : date('Y-m-d', strtotime('+1 day'));

if ($id <= 0) {
    sendJsonResponse('error', 'Invalid Doctor ID');
}

$result = $conn->query("SELECT * FROM doctors WHERE id = $id");

if ($result->num_rows == 0) {
    sendJsonResponse('error', 'Doctor not found');
}

$doc = $result->fetch_assoc();

// Fetch booked slots
$booked_slots_query = $conn->query("SELECT appointment_time FROM appointments WHERE doctor_id = $id AND appointment_date = '$date' AND status != 'Cancelled'");
$booked_slots = [];
while($row = $booked_slots_query->fetch_assoc()) {
    $booked_slots[] = $row['appointment_time'];
}

$doc['booked_slots'] = $booked_slots;
$doc['requested_date'] = $date;

sendJsonResponse('success', 'Doctor details loaded', $doc);
?>
