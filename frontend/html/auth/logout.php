<?php
// Start PHP session to load stored session variables
session_start();
// Check if user session variable exists
if (isset($_SESSION["user_id"])) {
    // Include database connection file
    require_once __DIR__ . "/../../../database/db_connect.php";
    // Cast active user_id session value to integer
    $userId = (int) $_SESSION["user_id"];
    // Get username from session or default to admin
    $username = $_SESSION["username"] ?? "admin";
    // Capture remote IP address of client or default to placeholder
    $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
    // Prepare AuditTrail insert statement to log logout action
    $auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, 'Logout', ?, NOW())");
    // Bind parameters for user_id, username, and ip_address to prepared statement
    $auditStmt->bind_param("iss", $userId, $username, $ipAddressVal);
    // Execute SQL statement to insert audit log row
    $auditStmt->execute();
    // Close prepared audit log statement
    $auditStmt->close();
    // Close active database connection link
    $conn->close();
}
// Remove all global session variables
session_unset();
// Destroy session data from server storage
session_destroy();
// Redirect client browser to login page
header("Location: login.php");
// Terminate script execution immediately
exit;
?>