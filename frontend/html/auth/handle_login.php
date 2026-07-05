<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : handle_login.php
// Description     : Authentication page or handler
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Start PHP code block
// Include database connection file
require_once __DIR__ . "/../../../database/db_connect.php";
// Include security filtering functions
require_once __DIR__ . "/security_filter.php";

// Set response content type header to JSON
header("Content-Type: application/json");
// Initialize default response payload array
$response = ["success" => false, "message" => ""];

// Confirm request method is HTTP POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input login field
    $loginInput = trim($_POST["username"] ?? "");
    // Extract input password value
    $password = $_POST["password"] ?? "";

    // Check if either username or password input is empty
    if ($loginInput === "" || $password === "") {
        // Set error message for missing values
        $response["message"] = "Please enter both username and password.";
    } else {
        // Run attack detection on username string
        $usernameAttack = detect_and_log_attack($loginInput, "login.php", $loginInput);
        // Run attack detection on password string
        $passwordAttack = detect_and_log_attack($password, "login.php", $loginInput);
        
        // Block execution if an attack pattern was identified in either input
        if ($usernameAttack || $passwordAttack) {
            // Set access denied message
            $response["message"] = "Access Denied: Malicious activity flagged.";
            // Add attack detected flag to response
            $response["attack_detected"] = true;
            // Capture matched attack type string
            $response["attack_type"] = $usernameAttack ? $usernameAttack['attack_type'] : $passwordAttack['attack_type'];
            // Output JSON-encoded response
            echo json_encode($response);
            // Terminate script execution
            exit;
        }
        
        // Prepare select statement to retrieve user record
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, is_active FROM Users WHERE username = ? OR email = ?");
        // Bind input credentials to prepared select statement
        $stmt->bind_param("ss", $loginInput, $loginInput);
        // Execute SELECT query statement
        $stmt->execute();
        // Fetch matching user row associative array
        $user = $stmt->get_result()->fetch_assoc();
        // Close SELECT prepared statement object
        $stmt->close();

        // Flag to track login validation outcome
        $failed = false;
        // Verify user match credentials
        // Ponytail: password_verify uses CPU-bound Bcrypt hashing.
        // Upgrade path: implement rate limiting at web server level (e.g. Nginx limit_req) to protect CPU.
        if ($user && password_verify($password, $user["password_hash"])) {
            // Check if matching user account status is not active
            if ((int) $user["is_active"] !== 1) {
                // Set message for inactive accounts
                $response["message"] = "This account is inactive. Contact an administrator.";
                // Set failure tracking state to true
                $failed = true;
            }
        // Handle case where credentials did not match database records
        } else {
            // Set message for invalid credentials
            $response["message"] = "Invalid username/email or password.";
            // Set failure tracking state to true
            $failed = true;
        }

        // Process logging if login attempt failed
        if ($failed) {
            // Capture remote IP address or default
            $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
            // Prepare statement to insert failed login record
            $failedStmt = $conn->prepare("INSERT INTO FailedLogins (ip_address, username, attempted_at) VALUES (?, ?, NOW())");
            // Bind parameters to failed login insert statement
            $failedStmt->bind_param("ss", $ipAddressVal, $loginInput);
            // Execute failed login log statement
            $failedStmt->execute();
            // Close prepared statement handle
            $failedStmt->close();
            // Run check to determine if brute force threshold is met
            if (detect_brute_force($ipAddressVal, 5)) {
                // Log IP to brute force list
                log_brute_force($ipAddressVal, $loginInput);
            }
        // Process login session creation if credentials are valid
        } else {
            // Start PHP session
            session_start();
            // Regenerate session id value to prevent session fixation attacks
            session_regenerate_id(true);
            // Set session user id parameter
            $_SESSION["user_id"] = (int) $user["user_id"];
            // Set session username parameter
            $_SESSION["username"] = $user["username"];

            // Prepare query to update user last login time
            $updateStmt = $conn->prepare("UPDATE Users SET last_login = NOW() WHERE user_id = ?");
            // Bind active user_id value to statement
            $updateStmt->bind_param("i", $user["user_id"]);
            // Execute update query
            $updateStmt->execute();
            // Close active update statement handle
            $updateStmt->close();

            // Capture remote client IP address
            $ipAddressVal = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
            // Prepare query statement to log audit trail event
            $auditStmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, 'Login', ?, NOW())");
            // Bind active user details to prepared audit trail statement
            $auditStmt->bind_param("iss", $user["user_id"], $user["username"], $ipAddressVal);
            // Execute audit trail query statement
            $auditStmt->execute();
            // Close prepared audit statement handle
            $auditStmt->close();

            // Set login success state to true
            $response["success"] = true;
            // Set success feedback message
            $response["message"] = "Login successful!";
        }
    }
// Handle non-POST request routes
} else {
    // Set invalid request error message
    $response["message"] = "Invalid request method.";
}

// Close active database link connection
$conn->close();
// Output response payload encoded in JSON format
echo json_encode($response);
?>
