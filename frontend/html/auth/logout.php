<?php
session_start();

if (isset($_SESSION["user_id"])) {
    require_once __DIR__ . "/../../../database/db_connect.php";
    $userId = (int) $_SESSION["user_id"];
    $username = $_SESSION["username"] ?? "admin";
    $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";

    $auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, 'Logout', ?, NOW())");
    $auditStmt->bind_param("iss", $userId, $username, $ipAddressVal);
    $auditStmt->execute();
    $auditStmt->close();
    $conn->close();
}

session_unset();
session_destroy();
header("Location: login.php");
exit;
?>