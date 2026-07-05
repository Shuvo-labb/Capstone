<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : get_threat_details.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$threatId = (int)($_GET["id"] ?? 0);

if ($threatId <= 0) {
    echo json_encode(["threat" => null]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT threat_id, log_id, threat_type, severity, ip_address, action_taken, detected_at, is_resolved
     FROM Threats WHERE threat_id = ?"
);
$stmt->bind_param("i", $threatId);
$stmt->execute();
$result = $stmt->get_result();
$threat = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode(["threat" => $threat]);

?>
