<?php
require_once __DIR__ . "/../../../database/db_connect.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $genericMessage = "If an account with that email exists, a password reset link has been sent.";

    if ($email === "") {
        $response["message"] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ? AND is_active = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date("Y-m-d H:i:s", time() + 3600);

            $deleteStmt = $conn->prepare("DELETE FROM PasswordResetTokens WHERE user_id = ?");
            $deleteStmt->bind_param("i", $user["user_id"]);
            $deleteStmt->execute();
            $deleteStmt->close();

            $insertStmt = $conn->prepare(
                "INSERT INTO PasswordResetTokens (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())"
            );
            $insertStmt->bind_param("iss", $user["user_id"], $token, $expiresAt);
            $insertStmt->execute();
            $insertStmt->close();

            if (defined("APP_DEV_MODE") && APP_DEV_MODE) {
                $response["reset_link"] = getAuthBaseUrl() . "/reset_password.php?token=" . urlencode($token);
            }
        }

        $response["success"] = true;
        $response["message"] = $genericMessage;
    }
} else {
    $response["message"] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);

?>
