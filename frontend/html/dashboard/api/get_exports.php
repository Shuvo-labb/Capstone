<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$sql = "SELECT report_id, report_type, report_date, date_from, date_to, file_format, file_path
        FROM Reports
        ORDER BY report_date DESC LIMIT 20";

$result = $conn->query($sql);
$exports = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $exports[] = $row;
    }
}

$conn->close();
echo json_encode(["exports" => $exports]);

?>
