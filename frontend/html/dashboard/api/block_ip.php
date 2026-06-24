<?php
// Start PHP code block
// Include database connection file
require_once __DIR__ . "/../../../../database/db_connect.php";
// Enforce session check and require active user login
require_once __DIR__ . "/../../auth/require_login.php";

// Set response content type header to JSON
header("Content-Type: application/json");

// Read and decode incoming raw JSON payload as associative array
$input = json_decode(file_get_contents("php://input"), true);
// Fallback to standard POST parameters if JSON payload was empty
if (!$input) {
    // Set input array from $_POST superglobal
    $input = $_POST;
}

// Retrieve and sanitize target IP address parameter
$ip = trim($input["ip"] ?? "");
// Retrieve and sanitize block reason parameter
$reason = trim($input["reason"] ?? "Manually flagged via dashboard");
// Determine target block state boolean flag defaulting to 1 (blocked)
$blocked = isset($input["blocked"]) ? (int)$input["blocked"] : 1;
// Retrieve active user ID from session context
$userId = (int)$_SESSION["user_id"];
// Retrieve active username from session context
$username = $_SESSION["username"] ?? "admin";

// Initialize default response payload array
$response = ["success" => false, "message" => ""];

// Validate IP field is not empty
if ($ip === "") {
    // Set error message for missing IP
    $response["message"] = "IP address is required.";
    // Output JSON-encoded response
    echo json_encode($response);
    // Terminate script execution
    exit;
}

// Get current date timestamp
$now = date("Y-m-d H:i:s");

// Prepare unified INSERT ... ON DUPLICATE KEY UPDATE statement to save database roundtrips
// Ponytail: ON DUPLICATE KEY UPDATE relies on the unique constraint on ip_address column.
// Upgrade path: if unique index is removed, fallback to SELECT checking before INSERT.
$stmt = $conn->prepare("INSERT INTO SuspiciousIPs (ip_address, reason, first_seen, last_seen, is_blocked) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE reason = ?, last_seen = ?, is_blocked = ?");
// Bind input values to parameters safely
$stmt->bind_param("sssssssi", $ip, $reason, $now, $now, $blocked, $reason, $now, $blocked);
// Execute prepared database statement query
$stmt->execute();
// Close prepared database statement handle
$stmt->close();

// Build action description text for audit log trail
$action = $blocked ? "Blocked IP: $ip" : "Added IP to Watchlist: $ip";
// Capture remote client IP address
$remoteIp = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
// Prepare insert statement to log activity to AuditTrail table
$auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
// Bind audit details to insert prepared statement
$auditStmt->bind_param("isss", $userId, $username, $action, $remoteIp);
// Execute prepared audit log insert query
$auditStmt->execute();
// Close prepared audit statement handle
$auditStmt->close();

// Close active database link connection
$conn->close();

// Set success status to true
$response["success"] = true;
// Set success feedback message
$response["message"] = $blocked ? "IP blocked successfully." : "IP added to watchlist.";
// Output response payload encoded in JSON format
echo json_encode($response);
?>
