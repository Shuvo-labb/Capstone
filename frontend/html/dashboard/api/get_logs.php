<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$sql = "SELECT l.log_id, l.log_file_name, l.file_format, l.file_size, l.upload_timestamp, l.parse_status,
               (SELECT COUNT(*) FROM Threats t WHERE t.log_id = l.log_id) as threat_count
        FROM Logs l
        ORDER BY l.upload_timestamp DESC";

$result = $conn->query($sql);
$logs = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

$conn->close();
echo json_encode(["logs" => $logs]);

?>
