<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : change_password.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Start PHP code block
// Include database connection file
require_once __DIR__ . "/../../../../database/db_connect.php";
// Include login verification guard
require_once __DIR__ . "/../../auth/require_login.php";

// Set response content type header to JSON
header("Content-Type: application/json");

// Read and decode incoming raw JSON payload as associative array
$input = json_decode(file_get_contents("php://input"), true);
// Retrieve current password input parameter
$currentPassword = $input["current_password"] ?? "";
// Retrieve new password input parameter
$newPassword = $input["new_password"] ?? "";

// Initialize default response payload array
$response = ["success" => false, "message" => ""];

// Check if any of the password inputs are empty
if ($currentPassword === "" || $newPassword === "") {
    // Set error message for missing fields
    $response["message"] = "Please fill in all fields.";
    // Output JSON-encoded response
    echo json_encode($response);
    // Terminate script execution
    exit;
}

// Verify that the new password is at least 8 characters long
if (strlen($newPassword) < 8) {
    // Set error message for short password
    $response["message"] = "New password must be at least 8 characters.";
    // Output JSON-encoded response
    echo json_encode($response);
    // Terminate script execution
    exit;
}

// Retrieve active user ID from session context
$userId = (int) $_SESSION["user_id"];

// Prepare select statement to fetch password hash for user
$stmt = $conn->prepare("SELECT password_hash FROM Users WHERE user_id = ?");
// Bind active user ID parameter to statement
$stmt->bind_param("i", $userId);
// Execute prepared select query statement
$stmt->execute();
// Fetch user row associative array
$user = $stmt->get_result()->fetch_assoc();
// Close prepared SELECT statement handle
$stmt->close();

// Verify current password match
// Ponytail: password_verify uses CPU-bound Bcrypt hashing.
// Upgrade path: implement rate limiting at application layer to restrict password verification attempts.
if (!$user || !password_verify($currentPassword, $user["password_hash"])) {
    // Set error message for incorrect password
    $response["message"] = "Current password is incorrect.";
    // Output JSON-encoded response
    echo json_encode($response);
    // Terminate script execution
    exit;
}

// Hash the new password value
// Ponytail: password_hash uses Bcrypt which is blocking and CPU intensive.
// Upgrade path: offload hashing cost by implementing password reset rate limits.
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
// Prepare statement to update password hash in Users table
$update = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
// Bind parameters to update statement
$update->bind_param("si", $newHash, $userId);
// Execute update query statement
$update->execute();
// Evaluate update success by checking affected rows count
$response["success"] = $update->affected_rows > 0;
// Set feedback message based on update success status
$response["message"] = $response["success"] ? "Password updated successfully." : "Failed to update password.";
// Close active update statement handle
$update->close();

// Log audit trail event if password change succeeded
if ($response["success"]) {
    // Retrieve active username from session context
    $username = $_SESSION["username"] ?? "admin";
    // Capture remote client IP address
    $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
    // Prepare insert statement to log password change event to AuditTrail table
    $auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, 'Changed Password', ?, NOW())");
    // Bind audit details to prepared insert statement
    $auditStmt->bind_param("iss", $userId, $username, $ipAddressVal);
    // Execute audit trail query statement
    $auditStmt->execute();
    // Close prepared audit statement handle
    $auditStmt->close();
}

// Close active database link connection
$conn->close();

// Output response payload encoded in JSON format
echo json_encode($response);
?>
