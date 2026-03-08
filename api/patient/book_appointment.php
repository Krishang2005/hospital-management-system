<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = getJsonInput();
    $doctor_id = intval($input['doctor_id']);
    $date = $conn->real_escape_string($input['date']);
    $time = $conn->real_escape_string($input['time']);
    $user_id = $_SESSION['user_id'];

    if(!$date || !$time || !$doctor_id) {
        sendJsonResponse('error', 'Please select date and time.');
    }

    $check = $conn->query("SELECT id FROM appointments WHERE doctor_id = $doctor_id AND appointment_date = '$date' AND appointment_time = '$time' AND status != 'Cancelled'");
    if($check->num_rows > 0) {
        sendJsonResponse('error', 'Sorry, this slot is already booked.');
    } else {
        $sql = "INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time) VALUES ($user_id, $doctor_id, '$date', '$time')";
        if($conn->query($sql)) {
            $doc_name = $conn->query("SELECT doctor_name FROM doctors WHERE id = $doctor_id")->fetch_row()[0];
            sendJsonResponse('success', 'Appointment booked successfully!', [
                'doctor_name' => $doc_name,
                'date' => $date,
                'time' => $time
            ]);
        } else {
            sendJsonResponse('error', 'Error booking appointment.');
        }
    }
}
?>
