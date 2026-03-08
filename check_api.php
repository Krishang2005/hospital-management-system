<?php
require_once 'api/includes/db.php';
$id = 5;
$result = $conn->query("SELECT * FROM doctors WHERE id = $id");
echo json_encode($result->fetch_assoc());
?>
