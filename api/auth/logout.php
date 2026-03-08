<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

session_unset();
session_destroy();
sendJsonResponse('success', 'Logged out successfully');
?>
