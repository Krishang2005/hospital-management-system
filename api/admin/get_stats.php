<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
requireAdmin();

$total_patients = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'patient'")->fetch_row()[0];
$total_doctors = $conn->query("SELECT COUNT(*) FROM doctors")->fetch_row()[0];
$total_appointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];

// Appointments by date for chart
$chart_data_q = $conn->query("SELECT appointment_date, COUNT(*) as count FROM appointments GROUP BY appointment_date ORDER BY appointment_date DESC LIMIT 7");
$chart_raw = [];
while($row = $chart_data_q->fetch_assoc()) {
    $chart_raw[] = $row;
}
$chart_raw = array_reverse($chart_raw); // Chronological

$chart_labels = [];
$chart_counts = [];
foreach($chart_raw as $d) {
    $chart_labels[] = date('M d', strtotime($d['appointment_date']));
    $chart_counts[] = $d['count'];
}

sendJsonResponse('success', 'Admin stats loaded', [
    'total_patients' => $total_patients,
    'total_doctors' => $total_doctors,
    'total_appointments' => $total_appointments,
    'chart' => [
        'labels' => $chart_labels,
        'data' => $chart_counts
    ]
]);
?>
