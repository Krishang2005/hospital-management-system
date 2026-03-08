<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();
requireAdmin();

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET") {
    // List doctors
    $sql = "SELECT * FROM doctors ORDER BY id DESC";
    $result = $conn->query($sql);
    $doctors = [];
    while($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    sendJsonResponse('success', 'Doctors loaded', $doctors);

} elseif ($method == "POST") {
    // Check if handling FormData (file upload)
    $doctor_name = $conn->real_escape_string($_POST['doctor_name']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $experience = intval($_POST['experience']);
    $qualification = $conn->real_escape_string($_POST['qualification']);
    $hospital = $conn->real_escape_string($_POST['hospital']);
    $available_days = $conn->real_escape_string($_POST['available_days']);
    $available_time = $conn->real_escape_string($_POST['available_time']); // Comma separated slots
    
    // Add Specialization to DB if it doesn't exist
    $spec_check = $conn->query("SELECT id FROM specializations WHERE specialization_name = '$specialization'");
    if($spec_check->num_rows == 0) {
        $conn->query("INSERT INTO specializations (specialization_name) VALUES ('$specialization')");
    }

    // Default image
    $photo_name = "default-doctor.jpg"; 

    // Handle File Upload
    if(isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
        $target_dir = "../../images/";
        $file_ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $photo_name = uniqid() . "." . $file_ext;
        $target_file = $target_dir . $photo_name;

        if(!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            sendJsonResponse('error', 'Error uploading image.');
        }
    }

    $sql = "INSERT INTO doctors (doctor_name, specialization, experience, qualification, hospital, available_days, available_time, photo) 
            VALUES ('$doctor_name', '$specialization', $experience, '$qualification', '$hospital', '$available_days', '$available_time', '$photo_name')";
    
    if ($conn->query($sql)) {
        sendJsonResponse('success', 'Doctor added successfully');
    } else {
        sendJsonResponse('error', 'Database error: ' . $conn->error);
    }

} elseif ($method == "DELETE") {
    // Delete doctor
    $input = getJsonInput();
    $id = intval($input['id']);
    
    if ($conn->query("DELETE FROM doctors WHERE id = $id")) {
        sendJsonResponse('success', 'Doctor deleted successfully');
    } else {
        sendJsonResponse('error', 'Failed to delete doctor');
    }
} elseif ($method == "PUT") {
    // Toggle status
    $input = getJsonInput();
    $id = intval($input['id']);
    $newStatus = $conn->real_escape_string($input['status']);

    if ($conn->query("UPDATE doctors SET status = '$newStatus' WHERE id = $id")) {
        sendJsonResponse('success', 'Status updated successfully');
    } else {
        sendJsonResponse('error', 'Failed to update status');
    }
}
?>
