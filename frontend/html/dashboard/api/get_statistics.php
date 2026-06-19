<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

// Total Threats
$totalThreats = 0;
$res = $conn->query("SELECT COUNT(*) as c FROM Threats");
if ($res) {
    $totalThreats = (int)$res->fetch_assoc()["c"];
}

// Unique IPs
$uniqueIps = 0;
$res = $conn->query("SELECT COUNT(DISTINCT ip_address) as c FROM Threats");
if ($res) {
    $uniqueIps = (int)$res->fetch_assoc()["c"];
}

// Avg per day
$avgPerDay = 0.0;
$res = $conn->query("SELECT COUNT(*)/COUNT(DISTINCT DATE(detected_at)) as avg_val FROM Threats");
if ($res) {
    $row = $res->fetch_assoc();
    $avgPerDay = $row["avg_val"] ? round((float)$row["avg_val"], 1) : 0.0;
}

// Timeseries (last 7 days grouped by date)
$timeseries = [];
$res = $conn->query("SELECT DATE(detected_at) as d, COUNT(*) as c FROM Threats GROUP BY DATE(detected_at) ORDER BY d DESC LIMIT 7");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $timeseries[] = [
            "date" => $row["d"],
            "count" => (int)$row["c"]
        ];
    }
    $timeseries = array_reverse($timeseries);
}

// Threat types
$types = [];
$res = $conn->query("SELECT threat_type, COUNT(*) as c FROM Threats GROUP BY threat_type");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $types[$row["threat_type"]] = (int)$row["c"];
    }
}

// Top IPs
$topIps = [];
$res = $conn->query("SELECT ip_address, COUNT(*) as c FROM Threats GROUP BY ip_address ORDER BY c DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $topIps[$row["ip_address"]] = (int)$row["c"];
    }
}

$conn->close();

echo json_encode([
    "totalThreats" => $totalThreats,
    "uniqueIps" => $uniqueIps,
    "avgPerDay" => $avgPerDay,
    "timeseries" => $timeseries,
    "types" => $types,
    "topIps" => $topIps
]);
?>
