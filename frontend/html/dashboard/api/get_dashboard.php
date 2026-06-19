<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$data = [
    "totalThreats" => 0,
    "openAlerts" => 0,
    "highSeverity" => 0,
    "lastUpload" => "—",
    "recent" => [],
    "timeline" => [],
];

$total = $conn->query("SELECT COUNT(*) AS c FROM Threats");
if ($total) {
    $data["totalThreats"] = (int) $total->fetch_assoc()["c"];
}

$open = $conn->query("SELECT COUNT(*) AS c FROM Threats WHERE is_resolved = 0");
if ($open) {
    $data["openAlerts"] = (int) $open->fetch_assoc()["c"];
}

$high = $conn->query("SELECT COUNT(*) AS c FROM Threats WHERE severity IN ('High','Critical')");
if ($high) {
    $data["highSeverity"] = (int) $high->fetch_assoc()["c"];
}

$last = $conn->query("SELECT upload_timestamp FROM Logs ORDER BY upload_timestamp DESC LIMIT 1");
if ($last && ($row = $last->fetch_assoc())) {
    $data["lastUpload"] = $row["upload_timestamp"];
}

$recent = $conn->query(
    "SELECT detected_at, threat_type AS type, severity, ip_address AS ip, action_taken
     FROM Threats ORDER BY detected_at DESC LIMIT 10"
);
if ($recent) {
    while ($row = $recent->fetch_assoc()) {
        $data["recent"][] = $row;
    }
}

$timeline = $conn->query(
    "SELECT DATE(detected_at) AS day, COUNT(*) AS count
     FROM Threats GROUP BY DATE(detected_at) ORDER BY day DESC LIMIT 7"
);
if ($timeline) {
    $rows = [];
    while ($row = $timeline->fetch_assoc()) {
        $rows[] = $row;
    }
    $data["timeline"] = array_reverse($rows);
}

$conn->close();
echo json_encode($data);

?>
