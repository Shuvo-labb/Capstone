<?php
// Start PHP code block
// Include security_filter.php to access parameter scanning functions
require_once __DIR__ . "/security_filter.php";
// Scan all GET and POST parameters for threat patterns using the requested URI as context
$attack = check_all_parameters($_SERVER['REQUEST_URI']);
// Check if an attack was detected
if ($attack) {
    // URL-encode the attack type for redirection query
    $attack_type = urlencode($attack['attack_type']);
    // URL-encode the attack payload for redirection query
    $payload = urlencode($attack['payload']);
    // Extract script name path of current request execution context
    $script_path = $_SERVER['SCRIPT_NAME'];
    // Determine warning page path based on whether request was routed inside dashboard
    $warning_path = (strpos($script_path, '/dashboard/') !== false) ? '../auth/attack_warning.php' : ((strpos($script_path, '/auth/') !== false) ? 'attack_warning.php' : 'auth/attack_warning.php');
    // Set response header to redirect client to warning path with details
    header("Location: $warning_path?type=$attack_type&payload=$payload");
    // Terminate script execution immediately
    exit;
}
?>
