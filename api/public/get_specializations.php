<?php
require_once '../includes/db.php';

$sql = "SELECT * FROM specializations ORDER BY specialization_name ASC";
$result = $conn->query($sql);

$specs = [];
while($row = $result->fetch_assoc()) {
    $specs[] = $row;
}
sendJsonResponse('success', 'Specializations loaded', $specs);
?>
