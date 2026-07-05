<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : handle_upload.php
// Description     : Dashboard page
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../../../database/db_connect.php";
require_once __DIR__ . "/../auth/require_login.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response["message"] = "Invalid request.";
    echo json_encode($response);
    exit;
}

if (!isset($_FILES["log_file"]) || $_FILES["log_file"]["error"] !== UPLOAD_ERR_OK) {
    $response["message"] = "Please choose a log file to upload.";
    echo json_encode($response);
    exit;
}

$file = $_FILES["log_file"];
$name = basename($file["name"]);
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

if (!in_array($ext, ["txt", "log"], true)) {
    $response["message"] = "Only .txt or .log files are allowed.";
    echo json_encode($response);
    exit;
}

$uploadDir = realpath(__DIR__ . "/../../../uploads");
if ($uploadDir === false) {
    mkdir(__DIR__ . "/../../../uploads", 0777, true);
    $uploadDir = realpath(__DIR__ . "/../../../uploads");
}

$safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $name);
$savePath = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

if (!move_uploaded_file($file["tmp_name"], $savePath)) {
    $response["message"] = "Could not save uploaded file.";
    echo json_encode($response);
    exit;
}

$userId = (int) $_SESSION["user_id"];
$fileSize = (int) filesize($savePath);
$now = date("Y-m-d H:i:s");

$stmt = $conn->prepare(
    "INSERT INTO Logs (log_file_name, file_format, file_size, upload_timestamp, uploaded_by, parse_status)
     VALUES (?, 'TXT', ?, ?, ?, 'Pending')"
);
$stmt->bind_param("sisi", $name, $fileSize, $now, $userId);
$stmt->execute();
$logId = $stmt->insert_id;
$stmt->close();

$parserPath = realpath(__DIR__ . "/../../../log_parser.py");
$python = "python";
$cmd = escapeshellarg($python) . " " . escapeshellarg($parserPath) . " " . escapeshellarg($savePath) . " 2>&1";
$output = shell_exec($cmd);

$threats = json_decode(trim($output ?? ""), true);

if (!is_array($threats)) {
    $fail = $conn->prepare("UPDATE Logs SET parse_status = 'Failed' WHERE log_id = ?");
    $fail->bind_param("i", $logId);
    $fail->execute();
    $fail->close();
    $conn->close();
    $response["message"] = "Parser failed. Make sure Python is installed. Output: " . substr($output ?? "", 0, 200);
    echo json_encode($response);
    exit;
}

$insert = $conn->prepare(
    "INSERT INTO Threats (log_id, threat_type, severity, ip_address, action_taken, detected_at)
     VALUES (?, ?, ?, ?, 'Flagged', ?)"
);

$count = 0;
foreach ($threats as $t) {
    $type = $t["threat_type"] ?? "Unknown";
    $severity = $t["severity"] ?? "Medium";
    $ip = $t["ip_address"] ?? "0.0.0.0";
    $detected = $t["detected_at"] ?? $now;

    if (!in_array($severity, ["Low", "Medium", "High", "Critical"], true)) {
        $severity = "Medium";
    }

    $insert->bind_param("issss", $logId, $type, $severity, $ip, $detected);
    $insert->execute();
    $count++;
}
$insert->close();

$done = $conn->prepare("UPDATE Logs SET parse_status = 'Completed' WHERE log_id = ?");
$done->bind_param("i", $logId);
$done->execute();
$done->close();

$conn->close();

$response["success"] = true;
$response["message"] = "Log parsed successfully. Found $count threat(s).";
$response["threats_found"] = $count;
echo json_encode($response);

?>
