<?php
// Start PHP code block
// Set the database host name
$servername = "localhost";
// Set the database user name
$username = "root";
// Set the database user password
$password = "";
// Set the database name
$dbname = "security_threat_dashboard";
// Create a new mysqli connection object
$conn = new mysqli($servername, $username, $password, $dbname);
// Check if connection attempt failed
if ($conn->connect_error) {
    // Terminate script execution and print connection error
    die("Connection failed: " . $conn->connect_error);
}
// Set connection character encoding to utf8mb4 for full unicode support
$conn->set_charset("utf8mb4");
// Set PHP default timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');
// Set MySQL session time zone to Kuala Lumpur offset
$conn->query("SET time_zone = '+08:00'");
// Define constant APP_DEV_MODE as true for development mode
define("APP_DEV_MODE", true);
// Declare getAuthBaseUrl function that returns a string URL
function getAuthBaseUrl(): string
{
    // Determine connection scheme, check if HTTPS is enabled
    $scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
    // Get host name from SERVER variable or default to localhost
    $host = $_SERVER["HTTP_HOST"] ?? "localhost";
    // Get script folder path replacing backslashes with forward slashes
    $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "/"));
    // Combine scheme, host, and script directory, trimming trailing slash
    return rtrim($scheme . "://" . $host . $scriptDir, "/");
}
?>
