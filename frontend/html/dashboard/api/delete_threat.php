<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : delete_threat.php
// Description     : Dashboard API endpoint
// First Commit Date: Sunday,21-Jun-2026
// Last Commit Date : Sunday,21-Jun-2026
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$threatId = (int)($input["threat_id"] ?? 0);
$source = $input["source"] ?? "threats"; // 'threats' or 'attacks'

$response = ["success" => false, "message" => ""];

if ($threatId <= 0) {
    $response["message"] = "Invalid threat ID.";
    echo json_encode($response);
    exit;
}

if ($source === "attacks") {
    // Delete from AttackEvents table
    $stmt = $conn->prepare("DELETE FROM AttackEvents WHERE attack_id = ?");
    $stmt->bind_param("i", $threatId);
    $stmt->execute();
    $response["success"] = $stmt->affected_rows > 0;
    $response["message"] = $response["success"] ? "Attack deleted successfully." : "Attack not found.";
    $stmt->close();
} else {
    // Delete from Threats table
    $stmt = $conn->prepare("DELETE FROM Threats WHERE threat_id = ?");
    $stmt->bind_param("i", $threatId);
    $stmt->execute();
    $response["success"] = $stmt->affected_rows > 0;
    $response["message"] = $response["success"] ? "Threat deleted successfully." : "Threat not found.";
    $stmt->close();
}

$conn->close();
echo json_encode($response);

?>
