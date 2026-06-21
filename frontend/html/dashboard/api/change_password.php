<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

// Accept JSON request from JavaScript fetch()
// Also supports normal HTML form submission if needed later.
$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

if (strpos($contentType, "application/json") !== false) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        $input = [];
    }
} else {
    $input = $_POST;
}

$currentPassword = trim($input["current_password"] ?? "");
$newPassword = trim($input["new_password"] ?? "");
$confirmPassword = trim($input["confirm_password"] ?? "");

// Function to return JSON and stop the script
function sendResponse($success, $message, $statusCode = 200)
{
    http_response_code($statusCode);

    echo json_encode([
        "success" => $success,
        "message" => $message
    ]);

    exit;
}

// Check login session
if (empty($_SESSION["user_id"])) {
    sendResponse(false, "Your session has expired. Please log in again.", 401);
}

// Check all fields
if ($currentPassword === "" || $newPassword === "") {
    sendResponse(false, "Please fill in all required fields.", 400);
}

// Confirm password is only required if your HTML sends it
if ($confirmPassword !== "" && $newPassword !== $confirmPassword) {
    sendResponse(false, "New password and confirmation password do not match.", 400);
}

// Do not allow spaces-only password
if (preg_match('/^\s+$/', $newPassword)) {
    sendResponse(false, "Password cannot contain only spaces.", 400);
}

// Password requirements
if (strlen($newPassword) < 8) {
    sendResponse(false, "New password must be at least 8 characters long.", 400);
}

if (!preg_match('/[A-Z]/', $newPassword)) {
    sendResponse(false, "New password must contain at least one uppercase letter.", 400);
}

if (!preg_match('/[a-z]/', $newPassword)) {
    sendResponse(false, "New password must contain at least one lowercase letter.", 400);
}

if (!preg_match('/[0-9]/', $newPassword)) {
    sendResponse(false, "New password must contain at least one number.", 400);
}

if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
    sendResponse(false, "New password must contain at least one special character.", 400);
}

// Basic common-password blacklist
$commonPasswords = [
    "password",
    "password123",
    "12345678",
    "123456789",
    "qwerty",
    "qwerty123",
    "admin123",
    "welcome123",
    "letmein"
];

if (in_array(strtolower($newPassword), $commonPasswords, true)) {
    sendResponse(false, "Please choose a stronger password.", 400);
}

$userId = (int) $_SESSION["user_id"];

// Get current password hash from database
$stmt = $conn->prepare("
    SELECT password_hash
    FROM Users
    WHERE user_id = ?
    LIMIT 1
");

if (!$stmt) {
    sendResponse(false, "Database error. Please try again later.", 500);
}

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

// Verify current password
if (!$user || !password_verify($currentPassword, $user["password_hash"])) {
    sendResponse(false, "Current password is incorrect.", 400);
}

// Do not allow same password as old password
if (password_verify($newPassword, $user["password_hash"])) {
    sendResponse(false, "New password cannot be the same as your current password.", 400);
}

// Hash the new password securely
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password
$update = $conn->prepare("
    UPDATE Users
    SET password_hash = ?
    WHERE user_id = ?
");

if (!$update) {
    sendResponse(false, "Unable to update password. Please try again.", 500);
}

$update->bind_param("si", $newHash, $userId);

if (!$update->execute()) {
    $update->close();
    sendResponse(false, "Unable to update password. Please try again.", 500);
}

$update->close();

// Create audit trail record
$username = $_SESSION["username"] ?? "admin";
$ipAddress = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";

$auditStmt = $conn->prepare("
    INSERT INTO AuditTrail
    (user_id, username, action, ip_address, created_at)
    VALUES (?, ?, 'Changed Password', ?, NOW())
");

if ($auditStmt) {
    $auditStmt->bind_param(
        "iss",
        $userId,
        $username,
        $ipAddress
    );

    $auditStmt->execute();
    $auditStmt->close();
}

// Prevent session fixation after a password update
session_regenerate_id(true);

$conn->close();

sendResponse(true, "Password updated successfully.");
?>