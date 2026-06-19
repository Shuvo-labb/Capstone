<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "security_threat_dashboard";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Set timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');
$conn->query("SET time_zone = '+08:00'");

// Local development: show reset links in API responses when email is not configured.
define("APP_DEV_MODE", true);

function getAuthBaseUrl(): string
{
    $scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
    $host = $_SERVER["HTTP_HOST"] ?? "localhost";
    $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "/"));
    return rtrim($scheme . "://" . $host . $scriptDir, "/");
}

?>
