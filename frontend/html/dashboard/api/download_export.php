<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    http_response_code(400);
    die("Invalid request: missing report ID.");
}

$stmt = $conn->prepare("SELECT file_path, file_format FROM Reports WHERE report_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filePath, $fileFormat);

if (!$stmt->fetch()) {
    $stmt->close();
    http_response_code(404);
    die("Report entry not found in database.");
}
$stmt->close();
$conn->close();

// Protect against directory traversal by isolating the base file name
$fileName = basename($filePath);
$fullPath = realpath(__DIR__ . "/../../../../exports") . DIRECTORY_SEPARATOR . $fileName;

if (!file_exists($fullPath)) {
    http_response_code(404);
    die("The requested report file does not exist on disk.");
}

header("Content-Description: File Transfer");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($fullPath));

if (strtoupper($fileFormat) === "PDF") {
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
} else {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
}

readfile($fullPath);
exit;
?>
