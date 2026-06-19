<?php
require_once __DIR__ . "/../../../../database/db_connect.php";
require_once __DIR__ . "/../../auth/require_login.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$currentPassword = $input["current_password"] ?? "";
$newPassword = $input["new_password"] ?? "";

$response = ["success" => false, "message" => ""];

if ($currentPassword === "" || $newPassword === "") {
    $response["message"] = "Please fill in all fields.";
    echo json_encode($response);
    exit;
}

if (strlen($newPassword) < 8) {
    $response["message"] = "New password must be at least 8 characters.";
    echo json_encode($response);
    exit;
}

$userId = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT password_hash FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($currentPassword, $user["password_hash"])) {
    $response["message"] = "Current password is incorrect.";
    echo json_encode($response);
    exit;
}

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
$update->bind_param("si", $newHash, $userId);
$update->execute();
$response["success"] = $update->affected_rows > 0;
$response["message"] = $response["success"] ? "Password updated successfully." : "Failed to update password.";
$update->close();
$conn->close();

echo json_encode($response);

?>
