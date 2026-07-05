<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : generate_export.php
// Description     : Dashboard API endpoint
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
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
$dateFrom = $_POST["date_from"] ?? "";
$dateTo = $_POST["date_to"] ?? "";
$ipAddress = trim($_POST["ip_address"] ?? "");
$userId = (int) $_SESSION["user_id"];
$username = $_SESSION["username"] ?? "admin";

if (empty($dateFrom)) {
    $dateFrom = date("Y-m-d", strtotime("-30 days"));
}
if (empty($dateTo)) {
    $dateTo = date("Y-m-d");
}

$where = ["detected_at BETWEEN ? AND ?"];
$params = [$dateFrom . " 00:00:00", $dateTo . " 23:59:59"];
$types = "ss";

if ($ipAddress !== "") {
    $where[] = "ip_address = ?";
    $params[] = $ipAddress;
    $types .= "s";
}

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

$fileName = "export_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $format;
$filePath = "/exports/" . $fileName;
$exportsDir = __DIR__ . "/../../../../exports";

if (!is_dir($exportsDir)) {
    mkdir($exportsDir, 0777, true);
}

if ($format === "csv") {
    $csvFile = fopen($exportsDir . "/" . $fileName, "w");
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
} elseif ($format === "pdf") {
    require_once __DIR__ . "/SimplePDF.php";
    $pdf = new SimplePDF();
    $pdf->AddPage();
    
    // Header Info
    $pdf->SetFont('Bold', 16);
    $pdf->Cell(495, 25, "Security Threat Report", 0, 1);
    $pdf->SetFont('Normal', 10);
    $pdf->Cell(495, 15, "Generated: " . date("Y-m-d H:i:s") . " | Timezone: Asia/Kuala_Lumpur", 0, 1);
    $pdf->Cell(495, 15, "Filter: Type=" . ucfirst($reportType) . " | Date Range=" . $dateFrom . " to " . $dateTo . ($ipAddress !== "" ? " | IP=" . $ipAddress : ""), 0, 1);
    $pdf->Ln(15);
    
    // Table Headers
    $pdf->SetFont('Bold', 9);
    $pdf->Cell(35, 18, "ID", 1, 0);
    $pdf->Cell(110, 18, "Threat Type", 1, 0);
    $pdf->Cell(80, 18, "Severity", 1, 0);
    $pdf->Cell(100, 18, "IP Address", 1, 0);
    $pdf->Cell(110, 18, "Detected At", 1, 0);
    $pdf->Cell(60, 18, "Resolved", 1, 1);
    
    // Table Body
    $pdf->SetFont('Normal', 9);
    foreach ($threats as $threat) {
        $pdf->Cell(35, 16, $threat["threat_id"], 1, 0);
        $pdf->Cell(110, 16, $threat["threat_type"], 1, 0);
        $pdf->Cell(80, 16, $threat["severity"], 1, 0);
        $pdf->Cell(100, 16, $threat["ip_address"], 1, 0);
        $pdf->Cell(110, 16, substr($threat["detected_at"], 0, 16), 1, 0);
        $pdf->Cell(60, 16, $threat["is_resolved"] ? "Yes" : "No", 1, 1);
    }
    
    $pdfData = $pdf->Output();
    file_put_contents($exportsDir . "/" . $fileName, $pdfData);
} else {
    $response["message"] = "Invalid format specified.";
    echo json_encode($response);
    exit;
}

// Log to Audit Trail
$auditAction = "Generated Export (" . strtoupper($format) . " - " . ucfirst($reportType) . ")";
$remoteIp = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
$auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
$auditStmt->bind_param("isss", $userId, $username, $auditAction, $remoteIp);
$auditStmt->execute();
$auditStmt->close();

// Insert Report into database
$insert = $conn->prepare(
    "INSERT INTO Reports (report_type, report_date, date_from, date_to, file_format, generated_by, file_path)
     VALUES (?, CURDATE(), ?, ?, ?, ?, ?)"
);
$upperFormat = strtoupper($format);
$insert->bind_param("ssssis", $reportType, $dateFrom, $dateTo, $upperFormat, $userId, $filePath);
$insert->execute();
$insert->close();
$conn->close();

$response["success"] = true;
$response["message"] = "Export generated successfully.";
$response["file_path"] = $filePath;
echo json_encode($response);
?>
