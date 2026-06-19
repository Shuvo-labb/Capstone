<?php
require_once __DIR__ . "/../../../database/db_connect.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginInput = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($loginInput === "" || $password === "") {
        $response["message"] = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id, username, password_hash, is_active FROM Users WHERE username = ? OR email = ?"
        );
        $stmt->bind_param("ss", $loginInput, $loginInput);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($password, $user["password_hash"])) {
            $response["message"] = "Invalid username/email or password.";
            $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
            $failedStmt = $conn->prepare("INSERT INTO FailedLogins (ip_address, username, attempted_at) VALUES (?, ?, NOW())");
            $failedStmt->bind_param("ss", $ipAddressVal, $loginInput);
            $failedStmt->execute();
            $failedStmt->close();
        } elseif ((int) $user["is_active"] !== 1) {
            $response["message"] = "This account is inactive. Contact an administrator.";
            $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
            $failedStmt = $conn->prepare("INSERT INTO FailedLogins (ip_address, username, attempted_at) VALUES (?, ?, NOW())");
            $failedStmt->bind_param("ss", $ipAddressVal, $loginInput);
            $failedStmt->execute();
            $failedStmt->close();
        } else {
            session_start();
            session_regenerate_id(true);
            $_SESSION["user_id"] = (int) $user["user_id"];
            $_SESSION["username"] = $user["username"];

            $updateStmt = $conn->prepare("UPDATE Users SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->bind_param("i", $user["user_id"]);
            $updateStmt->execute();
            $updateStmt->close();

            $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
            $auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, 'Login', ?, NOW())");
            $auditStmt->bind_param("iss", $user["user_id"], $user["username"], $ipAddressVal);
            $auditStmt->execute();
            $auditStmt->close();

            $response["success"] = true;
            $response["message"] = "Login successful!";
        }
    }
} else {
    $response["message"] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);

?>
