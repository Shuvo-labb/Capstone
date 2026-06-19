<?php
require_once __DIR__ . "/../../../database/db_connect.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = trim($_POST["token"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($token === "") {
        $response["message"] = "Reset token is missing or invalid.";
    } elseif ($password === "" || $confirmPassword === "") {
        $response["message"] = "Please enter and confirm your new password.";
    } elseif ($password !== $confirmPassword) {
        $response["message"] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $response["message"] = "Password must be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare(
            "SELECT prt.user_id
             FROM PasswordResetTokens prt
             INNER JOIN Users u ON u.user_id = prt.user_id
             WHERE prt.token = ?
               AND prt.used_at IS NULL
               AND prt.expires_at > NOW()
               AND u.is_active = 1"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $resetRequest = $result->fetch_assoc();
        $stmt->close();

        if (!$resetRequest) {
            $response["message"] = "Reset token is missing, expired, or already used.";
        } else {
            $userId = (int) $resetRequest["user_id"];
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $conn->begin_transaction();

            try {
                $updateUser = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
                $updateUser->bind_param("si", $passwordHash, $userId);
                $updateUser->execute();
                $updateUser->close();

                $markUsed = $conn->prepare(
                    "UPDATE PasswordResetTokens SET used_at = NOW() WHERE token = ?"
                );
                $markUsed->bind_param("s", $token);
                $markUsed->execute();
                $markUsed->close();

                $conn->commit();

                $response["success"] = true;
                $response["message"] = "Your password has been reset successfully. You can now log in.";
            } catch (Throwable $error) {
                $conn->rollback();
                $response["message"] = "Failed to reset password. Please try again later.";
            }
        }
    }
} else {
    $response["message"] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);

?>
