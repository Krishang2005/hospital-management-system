<?php
require_once '../includes/db.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$specialization = isset($_GET['specialization']) ? $conn->real_escape_string($_GET['specialization']) : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

$sql = "SELECT d.*, 
        (SELECT COUNT(*) FROM appointments a WHERE a.doctor_id = d.id AND a.appointment_date = CURDATE() AND a.status != 'Cancelled') as todays_bookings
        FROM doctors d WHERE 1=1";

if($search) {
    $sql .= " AND (d.doctor_name LIKE '%$search%' OR d.hospital LIKE '%$search%')";
}
if($specialization) {
    $sql .= " AND d.specialization = '$specialization'";
}
$sql .= " ORDER BY d.experience DESC";

if($limit > 0) {
    $sql .= " LIMIT $limit";
}

$result = $conn->query($sql);

$doctors = [];
while($row = $result->fetch_assoc()) {
    $slots = explode(',', $row['available_time']);
    $max_slots = count(array_filter($slots)); 
    $is_fully_booked = ($row['todays_bookings'] >= $max_slots && $max_slots > 0);
    
    if ($is_fully_booked) {
        $row['status'] = 'Fully Booked';
    }
    $doctors[] = $row;
}
sendJsonResponse('success', 'Doctors loaded', $doctors);
?>
