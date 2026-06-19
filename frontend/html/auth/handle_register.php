<?php
require_once __DIR__ . "/../../../database/db_connect.php";

header("Content-Type: application/json");

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($username === "" || $email === "" || $password === "" || $confirmPassword === "") {
        $response["message"] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Invalid email format.";
    } elseif (strlen($username) < 3) {
        $response["message"] = "Username must be at least 3 characters long.";
    } elseif ($password !== $confirmPassword) {
        $response["message"] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $response["message"] = "Password must be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response["message"] = "Username or email already exists.";
            $stmt->close();
        } else {
            $stmt->close();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $createdAt = date("Y-m-d H:i:s");

            $insertStmt = $conn->prepare(
                "INSERT INTO Users (username, password_hash, email, created_at, is_active) VALUES (?, ?, ?, ?, 1)"
            );
            $insertStmt->bind_param("ssss", $username, $passwordHash, $email, $createdAt);

            if ($insertStmt->execute()) {
                $response["success"] = true;
                $response["message"] = "Registration successful. You can now log in.";
            } else {
                $response["message"] = "Registration failed. Please try again later.";
            }
            $insertStmt->close();
        }
    }
} else {
    $response["message"] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);

?>
