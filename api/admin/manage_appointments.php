<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
requireAdmin();

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET") {
    $sql = "SELECT a.*, u.id as user_id, u.name as patient_name, u.phone as patient_phone, u.email as patient_email, d.doctor_name, d.specialization 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id 
            JOIN doctors d ON a.doctor_id = d.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
    $result = $conn->query($sql);
    $appointments = [];
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    sendJsonResponse('success', 'Appointments loaded', $appointments);

} elseif ($method == "POST") {
    $input = getJsonInput();
    $id = intval($input['appointment_id']);
    $status = $conn->real_escape_string($input['status']);
    
    // Allowed statuses
    $allowed = ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'Ongoing', 'No-Show'];
    if (!in_array($status, $allowed)) {
        sendJsonResponse('error', 'Invalid status update');
    }

    if ($conn->query("UPDATE appointments SET status = '$status' WHERE id = $id")) {
        sendJsonResponse('success', "Appointment marked as $status");
    } else {
        sendJsonResponse('error', 'Failed to update appointment');
    }
} elseif ($method == "DELETE") {
    $input = getJsonInput();
    if (!$input) {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    } else {
        $id = intval($input['appointment_id']);
    }

    if ($id <= 0) {
        sendJsonResponse('error', 'Invalid appointment ID');
    }

    if ($conn->query("DELETE FROM appointments WHERE id = $id")) {
        sendJsonResponse('success', 'Appointment deleted successfully');
    } else {
        sendJsonResponse('error', 'Failed to delete appointment');
    }
}
?>
