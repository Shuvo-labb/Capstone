<?php
// Programmer Name : VISHVAN VARMA A/L SIVA KUMAR
// Program Name    : handle_forgot_password.php
// Description     : Authentication page or handler
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Start PHP code block
// Include database connection file
require_once __DIR__ . "/../../../database/db_connect.php";
// Include email dispatcher helper file
require_once __DIR__ . "/../includes/EmailHelper.php";

// Set response content type header to JSON
header("Content-Type: application/json");
// Initialize default response payload array
$response = ["success" => false, "message" => ""];

// Confirm request method is HTTP POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input email address value
    $email = trim($_POST["email"] ?? "");
    // Define generic feedback response string to prevent user account enumeration
    $genericMessage = "If an account with that email exists, a password reset link has been sent.";

    // Check if email field is empty
    if ($email === "") {
        // Set error message for empty email field
        $response["message"] = "Please enter your email address.";
    // Validate format correctness of input email
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set error message for invalid email format
        $response["message"] = "Invalid email format.";
    // Process reset sequence if validation checks pass
    } else {
        // Prepare query to search active user by email
        $stmt = $conn->prepare("SELECT user_id, username FROM Users WHERE email = ? AND is_active = 1");
        // Bind input email value to prepared SELECT query statement
        $stmt->bind_param("s", $email);
        // Execute SELECT query statement
        $stmt->execute();
        // Fetch matching user row associative array
        $user = $stmt->get_result()->fetch_assoc();
        // Close prepared query statement handle
        $stmt->close();

        // Check if matching active user record exists
        if ($user) {
            // Generate cryptographically secure random bytes converted to hex string for token
            $token = bin2hex(random_bytes(32));
            // Set token expiration timestamp 1 hour in future
            $expiresAt = date("Y-m-d H:i:s", time() + 3600);

            // Prepare delete statement to clear existing tokens for this user ID
            $deleteStmt = $conn->prepare("DELETE FROM PasswordResetTokens WHERE user_id = ?");
            // Bind user_id parameter to delete statement
            $deleteStmt->bind_param("i", $user["user_id"]);
            // Execute delete query statement
            $deleteStmt->execute();
            // Close active delete statement handle
            $deleteStmt->close();

            // Prepare insert statement to log new reset token
            $insertStmt = $conn->prepare("INSERT INTO PasswordResetTokens (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())");
            // Bind user details and token variables to insert statement
            $insertStmt->bind_param("iss", $user["user_id"], $token, $expiresAt);
            // Execute insert query statement
            $insertStmt->execute();
            // Close active insert statement handle
            $insertStmt->close();

            // Instantiate a new EmailHelper class object
            $emailHelper = new EmailHelper();
            // Dispatch password reset email via EmailHelper sendPasswordReset method
            // Ponytail: email sending is synchronous and blocks the user response loop.
            // Upgrade path: implement asynchronous queueing of emails.
            $emailSent = $emailHelper->sendPasswordReset($email, $user["username"], $token);

            // Log error internally if email dispatch function fails
            if (!$emailSent) {
                // Log failed email details to system error log
                error_log("Failed to send password reset email to: $email");
            }

            // Check if application is running in local development mode
            if (defined("APP_DEV_MODE") && APP_DEV_MODE) {
                // Inject reset link into JSON response to ease local manual testing
                $response["reset_link"] = "http://" . $_SERVER['HTTP_HOST'] . "/frontend/html/auth/reset_password.php?token=" . urlencode($token);
            }
        }

        // Set response success state to true
        $response["success"] = true;
        // Set generic response message to output
        $response["message"] = $genericMessage;
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
