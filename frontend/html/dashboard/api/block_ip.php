<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    $input = $_POST;
}

$ip = trim($input["ip"] ?? "");
$reason = trim($input["reason"] ?? "Manually flagged via dashboard");
$blocked = isset($input["blocked"]) ? (int)$input["blocked"] : 1;
$userId = (int)$_SESSION["user_id"];
$username = $_SESSION["username"] ?? "admin";

$response = ["success" => false, "message" => ""];

if ($ip === "") {
    $response["message"] = "IP address is required.";
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("SELECT ip_id FROM SuspiciousIPs WHERE ip_address = ?");
$stmt->bind_param("s", $ip);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

$now = date("Y-m-d H:i:s");

if ($exists) {
    $stmt = $conn->prepare("UPDATE SuspiciousIPs SET reason = ?, last_seen = ?, is_blocked = ? WHERE ip_address = ?");
    $stmt->bind_param("ssis", $reason, $now, $blocked, $ip);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO SuspiciousIPs (ip_address, reason, first_seen, last_seen, is_blocked) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $ip, $reason, $now, $now, $blocked);
    $stmt->execute();
    $stmt->close();
}

$action = $blocked ? "Blocked IP: $ip" : "Added IP to Watchlist: $ip";
$remoteIp = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
$auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
$auditStmt->bind_param("isss", $userId, $username, $action, $remoteIp);
$auditStmt->execute();
$auditStmt->close();

$conn->close();

$response["success"] = true;
$response["message"] = $blocked ? "IP blocked successfully." : "IP added to watchlist.";
echo json_encode($response);
?>
