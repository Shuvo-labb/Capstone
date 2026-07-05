<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : get_activity_logs.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$user = trim($_GET["user"] ?? "");
$action = trim($_GET["action"] ?? "");

$sql = "SELECT username, action, ip_address, created_at FROM AuditTrail";
$where = [];
$params = [];
$types = "";

if ($user !== "") {
    $where[] = "username LIKE ?";
    $params[] = "%" . $user . "%";
    $types .= "s";
}

if ($action !== "") {
    $where[] = "action LIKE ?";
    $params[] = "%" . $action . "%";
    $types .= "s";
}

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC LIMIT 100";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = [
        "when" => $row["created_at"],
        "user" => $row["username"],
        "action" => $row["action"],
        "ip" => $row["ip_address"]
    ];
}
$stmt->close();
$conn->close();

echo json_encode(["logs" => $logs]);
?>
