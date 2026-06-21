<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$source = $input["source"] ?? "all"; // 'threats', 'attacks', or 'all'

$response = ["success" => false, "message" => "", "deleted_count" => 0];

if ($source === "threats" || $source === "all") {
    // Delete resolved threats from Threats table
    $stmt = $conn->prepare("DELETE FROM Threats WHERE is_resolved = 1");
    $stmt->execute();
    $response["deleted_count"] += $stmt->affected_rows;
    $stmt->close();
}

if ($source === "attacks" || $source === "all") {
    // Delete all attacks from AttackEvents table (they're all considered resolved/blocked)
    $stmt = $conn->prepare("DELETE FROM AttackEvents");
    $stmt->execute();
    $response["deleted_count"] += $stmt->affected_rows;
    $stmt->close();
}

$response["success"] = $response["deleted_count"] > 0;
$response["message"] = $response["success"] ? "Deleted {$response['deleted_count']} resolved threat(s)." : "No resolved threats to delete.";

$conn->close();
echo json_encode($response);

?>
