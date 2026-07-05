<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : get_monthly_summary.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$sql = "SELECT 
          CONCAT('Week ', WEEK(detected_at)) as week,
          COUNT(*) as total,
          SUM(CASE WHEN severity = 'Critical' THEN 1 ELSE 0 END) as critical,
          SUM(CASE WHEN severity = 'High' THEN 1 ELSE 0 END) as high,
          SUM(CASE WHEN severity = 'Medium' THEN 1 ELSE 0 END) as medium,
          SUM(CASE WHEN severity = 'Low' THEN 1 ELSE 0 END) as low,
          SUM(CASE WHEN is_resolved = 0 THEN 1 ELSE 0 END) as unresolved
        FROM Threats
        WHERE detected_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY WEEK(detected_at)
        ORDER BY week DESC";

$result = $conn->query($sql);
$summary = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $summary[] = $row;
    }
}

$conn->close();
echo json_encode(["summary" => $summary]);

?>
