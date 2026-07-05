<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : get_failed_logins.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$threshold = isset($_GET["threshold"]) ? (int)$_GET["threshold"] : 1;

$sql = "SELECT ip_address, username, COUNT(*) as attempts, MAX(attempted_at) as last_attempt
        FROM FailedLogins
        GROUP BY ip_address, username
        HAVING attempts >= ?
        ORDER BY last_attempt DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $threshold);
$stmt->execute();
$result = $stmt->get_result();

$attempts = [];
while ($row = $result->fetch_assoc()) {
    $attempts[] = [
        "ip" => $row["ip_address"],
        "user" => $row["username"],
        "attempts" => (int)$row["attempts"],
        "last" => $row["last_attempt"]
    ];
}
$stmt->close();
$conn->close();

echo json_encode(["attempts" => $attempts]);
?>
