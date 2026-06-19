<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$sql = "SELECT threat_id, threat_type, severity, ip_address, detected_at, is_resolved
        FROM Threats
        ORDER BY detected_at DESC LIMIT 50";

$result = $conn->query($sql);
$alerts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $alerts[] = [
            "id" => (int)$row["threat_id"],
            "detected_at" => $row["detected_at"],
            "type" => $row["threat_type"],
            "severity" => $row["severity"],
            "ip" => $row["ip_address"],
            "resolved" => (int)$row["is_resolved"]
        ];
    }
}
$conn->close();

echo json_encode(["alerts" => $alerts]);
?>
