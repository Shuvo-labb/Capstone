<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : handle_register.php
// Description     : Authentication page or handler
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Start PHP code block
// Include database connection setup file
require_once __DIR__ . "/../../../database/db_connect.php";

// Set response content type header to JSON
header("Content-Type: application/json");
// Initialize default response payload array
$response = ["success" => false, "message" => ""];

// Confirm request method is HTTP POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input username field
    $username = trim($_POST["username"] ?? "");
    // Sanitize input email field
    $email = trim($_POST["email"] ?? "");
    // Extract input password value
    $password = $_POST["password"] ?? "";
    // Extract input confirm password value
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Check if any of the required fields are empty
    if ($username === "" || $email === "" || $password === "" || $confirmPassword === "") {
        // Set error message for missing values
        $response["message"] = "All fields are required.";
    // Validate format correctness of email input
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set error message for invalid email format
        $response["message"] = "Invalid email format.";
    // Verify that username length is at least 3 characters
    } elseif (strlen($username) < 3) {
        // Set error message for short username length
        $response["message"] = "Username must be at least 3 characters long.";
    // Verify that password matches confirm password value
    } elseif ($password !== $confirmPassword) {
        // Set error message for mismatching passwords
        $response["message"] = "Passwords do not match.";
    // Verify password length is at least 6 characters
    } elseif (strlen($password) < 6) {
        // Set error message for short password length
        $response["message"] = "Password must be at least 6 characters long.";
    // Handle processing registration logic when checks pass
    } else {
        // Prepare statement to query users for duplicate username or email values
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        // Bind credentials to duplicate checks select query statement
        $stmt->bind_param("ss", $username, $email);
        // Execute duplicate select query statement
        $stmt->execute();
        // Store resulting rows internally for count checks
        $stmt->store_result();

        // Check if matching username or email already exists in DB
        if ($stmt->num_rows > 0) {
            // Set message for duplicate credentials
            $response["message"] = "Username or email already exists.";
            // Close prepared statement handle
            $stmt->close();
        // Process insertion if credentials are unique
        } else {
            // Close active prepared statement handle
            $stmt->close();
            // Hash the password value using default algorithm settings
            // Ponytail: password_hash uses Bcrypt which is blocking and CPU intensive.
            // Upgrade path: offload hashing cost by implementing registration rate-limits.
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            // Get current timestamp for account creation date
            $createdAt = date("Y-m-d H:i:s");

            // Prepare insert statement to create user record
            $insertStmt = $conn->prepare("INSERT INTO Users (username, password_hash, email, created_at, is_active) VALUES (?, ?, ?, ?, 1)");
            // Bind registration parameters to insert statement
            $insertStmt->bind_param("ssss", $username, $passwordHash, $email, $createdAt);

            // Execute insert query statement and verify success status
            if ($insertStmt->execute()) {
                // Set success flag to true
                $response["success"] = true;
                // Set registration success feedback message
                $response["message"] = "Registration successful. You can now log in.";
            // Handle DB execution failure cases
            } else {
                // Set registration failed message
                $response["message"] = "Registration failed. Please try again later.";
            }
            // Close active insert statement handle
            $insertStmt->close();
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
