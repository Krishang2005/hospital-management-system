<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
requireAdmin();

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET") {
    $sql = "SELECT u.id, u.name, u.email, u.phone, u.created_at, 
            (SELECT d.doctor_name FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.user_id = u.id ORDER BY a.appointment_date DESC, a.appointment_time DESC LIMIT 1) as last_doctor,
            (SELECT a.status FROM appointments a WHERE a.user_id = u.id ORDER BY a.appointment_date DESC, a.appointment_time DESC LIMIT 1) as last_status
            FROM users u 
            WHERE u.role = 'patient' 
            ORDER BY u.id DESC";
    $result = $conn->query($sql);
    $patients = [];
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    sendJsonResponse('success', 'Patients loaded', $patients);
} elseif ($method == "DELETE") {
    $input = getJsonInput();
    if (!$input) {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    } else {
        $id = intval($input['patient_id']);
    }

    if ($id <= 0) {
        sendJsonResponse('error', 'Invalid patient ID');
    }

    // Since appointments table has ON DELETE CASCADE on user_id, deleting the user will delete their appointments
    if ($conn->query("DELETE FROM users WHERE id = $id AND role = 'patient'")) {
        sendJsonResponse('success', 'Patient deleted successfully');
    } else {
        sendJsonResponse('error', 'Failed to delete patient');
    }
}
?>
