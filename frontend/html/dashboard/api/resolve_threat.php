<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$threatId = (int)($input["threat_id"] ?? 0);

$response = ["success" => false];

if ($threatId > 0) {
    $stmt = $conn->prepare("UPDATE Threats SET is_resolved = 1 WHERE threat_id = ?");
    $stmt->bind_param("i", $threatId);
    $stmt->execute();
    $response["success"] = $stmt->affected_rows > 0;
    $stmt->close();

    if ($response["success"]) {
        $userId = (int)($_SESSION["user_id"] ?? 0);
        $username = $_SESSION["username"] ?? "admin";
        $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
        $auditAction = "Resolved Threat ID: " . $threatId;
        
        $auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $auditStmt->bind_param("isss", $userId, $username, $auditAction, $ipAddressVal);
        $auditStmt->execute();
        $auditStmt->close();
    }
}

$conn->close();
echo json_encode($response);

?>
