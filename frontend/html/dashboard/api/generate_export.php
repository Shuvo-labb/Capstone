<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response["message"] = "Invalid request method.";
    echo json_encode($response);
    exit;
}

$reportType = $_POST["report_type"] ?? "all";
$format = $_POST["format"] ?? "csv";
$dateFrom = $_POST["date_from"] ?? date("Y-m-d", strtotime("-30 days"));
$dateTo = $_POST["date_to"] ?? date("Y-m-d");
$userId = (int) $_SESSION["user_id"];

$where = ["detected_at BETWEEN ? AND ?"];
$params = [$dateFrom, $dateTo];
$types = "ss";

switch ($reportType) {
    case "unresolved":
        $where[] = "is_resolved = 0";
        break;
    case "critical":
        $where[] = "severity = 'Critical'";
        break;
    case "sqli":
        $where[] = "threat_type = 'SQL Injection'";
        break;
    case "xss":
        $where[] = "threat_type = 'XSS'";
        break;
    case "malware":
        $where[] = "threat_type = 'Malware'";
        break;
}

$whereClause = implode(" AND ", $where);
$sql = "SELECT threat_id, threat_type, severity, ip_address, action_taken, detected_at, is_resolved
        FROM Threats WHERE $whereClause ORDER BY detected_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$threats = [];
while ($row = $result->fetch_assoc()) {
    $threats[] = $row;
}
$stmt->close();

if (empty($threats)) {
    $response["message"] = "No threats found for the selected criteria.";
    echo json_encode($response);
    exit;
}

$fileName = "export_" . time() . "." . $format;
$filePath = "/exports/" . $fileName;

if ($format === "csv") {
    $csvFile = fopen(__DIR__ . "/../../../../exports/" . $fileName, "w");
    fputcsv($csvFile, ["ID", "Type", "Severity", "IP Address", "Action", "Detected", "Resolved"]);
    foreach ($threats as $threat) {
        fputcsv($csvFile, [
            $threat["threat_id"],
            $threat["threat_type"],
            $threat["severity"],
            $threat["ip_address"],
            $threat["action_taken"],
            $threat["detected_at"],
            $threat["is_resolved"] ? "Yes" : "No"
        ]);
    }
    fclose($csvFile);
} else {
    $response["message"] = "PDF export not implemented yet. Please use CSV format.";
    echo json_encode($response);
    exit;
}

$insert = $conn->prepare(
    "INSERT INTO Reports (report_type, report_date, date_from, date_to, file_format, generated_by, file_path)
     VALUES (?, CURDATE(), ?, ?, ?, ?, ?)"
);
$insert->bind_param("ssssis", $reportType, $dateFrom, $dateTo, strtoupper($format), $userId, $filePath);
$insert->execute();
$insert->close();
$conn->close();

$response["success"] = true;
$response["message"] = "Export generated successfully.";
$response["file_path"] = $filePath;
echo json_encode($response);

?>
