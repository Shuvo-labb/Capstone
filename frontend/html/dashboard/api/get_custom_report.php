<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : get_custom_report.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$where = ["1=1"];
$params = [];
$types = "";

$dateFrom = $_GET["date_from"] ?? "";
$dateTo = $_GET["date_to"] ?? "";
$threatType = $_GET["threat_type"] ?? "";
$severity = $_GET["severity"] ?? "";
$ipAddress = $_GET["ip_address"] ?? "";
$status = $_GET["status"] ?? "";

if ($dateFrom) {
    $where[] = "detected_at >= ?";
    $params[] = $dateFrom;
    $types .= "s";
}

if ($dateTo) {
    $where[] = "detected_at <= ?";
    $params[] = $dateTo . " 23:59:59";
    $types .= "s";
}

if ($threatType) {
    $where[] = "threat_type = ?";
    $params[] = $threatType;
    $types .= "s";
}

if ($severity) {
    $where[] = "severity = ?";
    $params[] = $severity;
    $types .= "s";
}

if ($ipAddress) {
    $where[] = "ip_address = ?";
    $params[] = $ipAddress;
    $types .= "s";
}

if ($status !== "") {
    $where[] = "is_resolved = ?";
    $params[] = (int)$status;
    $types .= "i";
}

$whereClause = implode(" AND ", $where);
$sql = "SELECT threat_id, threat_type, severity, ip_address, action_taken, detected_at, is_resolved
        FROM Threats WHERE $whereClause ORDER BY detected_at DESC LIMIT 500";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
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
