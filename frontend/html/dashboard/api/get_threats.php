<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : get_threats.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Sunday,21-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$type = trim($_GET["type"] ?? "");

$sql = "SELECT threat_id, log_id, threat_type, severity, ip_address, action_taken, detected_at, is_resolved
        FROM Threats";
$params = [];
$types = "";

if ($type !== "") {
    $sql .= " WHERE threat_type = ?";
    $params[] = $type;
    $types = "s";
}

$sql .= " ORDER BY detected_at DESC LIMIT 100";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$threats = [];
while ($row = $result->fetch_assoc()) {
    $row["source"] = "threats";
    $threats[] = $row;
}

$stmt->close();
$conn->close();
echo json_encode(["threats" => $threats]);

?>
