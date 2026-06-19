<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$date = $_GET["date"] ?? date("Y-m-d");

$stmt = $conn->prepare(
    "SELECT threat_id, threat_type, severity, ip_address, action_taken, detected_at, is_resolved
     FROM Threats WHERE DATE(detected_at) = ? ORDER BY detected_at DESC"
);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$threats = [];
while ($row = $result->fetch_assoc()) {
    $threats[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(["threats" => $threats]);

?>
