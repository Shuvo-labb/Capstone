<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$q = trim($_GET["q"] ?? "");

$sql = "SELECT ip_address, reason, first_seen, last_seen, is_blocked FROM SuspiciousIPs";
$params = [];
$types = "";

if ($q !== "") {
    $sql .= " WHERE ip_address LIKE ?";
    $params[] = "%" . $q . "%";
    $types = "s";
}

$sql .= " ORDER BY last_seen DESC";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$ips = [];
while ($row = $result->fetch_assoc()) {
    $ips[] = [
        "ip" => $row["ip_address"],
        "reason" => $row["reason"],
        "firstSeen" => $row["first_seen"],
        "lastSeen" => $row["last_seen"],
        "blocked" => (int)$row["is_blocked"]
    ];
}
$stmt->close();
$conn->close();

echo json_encode(["ips" => $ips]);
?>
