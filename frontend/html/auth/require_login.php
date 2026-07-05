<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : require_login.php
// Description     : Authentication page or handler
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
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
