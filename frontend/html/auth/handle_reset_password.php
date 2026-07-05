<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : handle_reset_password.php
// Description     : Authentication page or handler
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Start PHP code block
// Include database connection file
require_once __DIR__ . "/../../../database/db_connect.php";

// Set response content type header to JSON
header("Content-Type: application/json");
// Initialize default response payload array
$response = ["success" => false, "message" => ""];

// Confirm request method is HTTP POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input reset token value
    $token = trim($_POST["token"] ?? "");
    // Extract input password value
    $password = $_POST["password"] ?? "";
    // Extract input confirm password value
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Check if token value is missing
    if ($token === "") {
        // Set error message for missing token
        $response["message"] = "Reset token is missing or invalid.";
    // Check if password fields are empty
    } elseif ($password === "" || $confirmPassword === "") {
        // Set error message for empty password inputs
        $response["message"] = "Please enter and confirm your new password.";
    // Verify that password matches confirm password value
    } elseif ($password !== $confirmPassword) {
        // Set error message for mismatching passwords
        $response["message"] = "Passwords do not match.";
    // Verify password length is at least 6 characters
    } elseif (strlen($password) < 6) {
        // Set error message for short password length
        $response["message"] = "Password must be at least 6 characters long.";
    // Process reset sequence if validation checks pass
    } else {
        // Prepare statement to select active user with matching unused reset token
        $stmt = $conn->prepare("SELECT prt.user_id FROM PasswordResetTokens prt INNER JOIN Users u ON u.user_id = prt.user_id WHERE prt.token = ? AND prt.used_at IS NULL AND prt.expires_at > NOW() AND u.is_active = 1");
        // Bind token value to prepared select statement
        $stmt->bind_param("s", $token);
        // Execute SELECT query statement
        $stmt->execute();
        // Fetch matching row associative array
        $resetRequest = $stmt->get_result()->fetch_assoc();
        // Close prepared statement handle
        $stmt->close();

        // Check if token request is invalid or expired
        if (!$resetRequest) {
            // Set error message for invalid token
            $response["message"] = "Reset token is missing, expired, or already used.";
        // Reset password if token matches active request
        } else {
            // Cast matching user_id to integer
            $userId = (int) $resetRequest["user_id"];
            // Hash the password value using default algorithm settings
            // Ponytail: password_hash uses Bcrypt which is blocking and CPU intensive.
            // Upgrade path: implement rate limiting on password reset completion.
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Initiate transaction on database link
            $conn->begin_transaction();

            // Try-catch block for transaction operations
            try {
                // Prepare statement to update user password hash value
                $updateUser = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
                // Bind new password hash and user id parameters to statement
                $updateUser->bind_param("si", $passwordHash, $userId);
                // Execute update query statement
                $updateUser->execute();
                // Close active update statement handle
                $updateUser->close();

                // Prepare statement to mark token as used
                $markUsed = $conn->prepare("UPDATE PasswordResetTokens SET used_at = NOW() WHERE token = ?");
                // Bind token string value to update statement
                $markUsed->bind_param("s", $token);
                // Execute mark used update query statement
                $markUsed->execute();
                // Close active update statement handle
                $markUsed->close();

                // Commit database transaction changes
                $conn->commit();

                // Set success state to true
                $response["success"] = true;
                // Set password reset success message
                $response["message"] = "Your password has been reset successfully. You can now log in.";
            // Catch database transaction failures
            } catch (Throwable $error) {
                // Rollback database transaction changes
                $conn->rollback();
                // Set transaction failed message
                $response["message"] = "Failed to reset password. Please try again later.";
            }
        }
    }
// Handle non-POST request routes
} else {
    // Set invalid request method error message
    $response["message"] = "Invalid request method.";
}

// Close active database link connection
$conn->close();
// Output response payload encoded in JSON format
echo json_encode($response);
?>
