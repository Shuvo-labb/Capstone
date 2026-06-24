<?php
// Start PHP session to load stored user attributes
session_start();
// Check if user_id is not set in active session variables
if (!isset($_SESSION["user_id"])) {
    // Redirect client browser to login page
    header("Location: ../auth/login.php");
    // Terminate script execution immediately
    exit;
}
?>
