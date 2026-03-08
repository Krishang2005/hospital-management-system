<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        sendJsonResponse('error', 'Unauthorized access. Please login first.');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        sendJsonResponse('error', 'Forbidden. Admin privileges required.');
    }
}

// Function to read JSON input from fetch body
function getJsonInput() {
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}
?>
