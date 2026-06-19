<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$threatId = (int)($input["threat_id"] ?? 0);

$response = ["success" => false];

if ($threatId > 0) {
    $stmt = $conn->prepare("UPDATE Threats SET is_resolved = 1 WHERE threat_id = ?");
    $stmt->bind_param("i", $threatId);
    $stmt->execute();
    $response["success"] = $stmt->affected_rows > 0;
    $stmt->close();
}

$conn->close();
echo json_encode($response);

?>
