<?php
/**
 * Global Security Filter
 * 
 * This file should be included at the top of all dashboard pages
 * to detect and block URL-based attacks (GET parameters)
 */

require_once __DIR__ . "/security_filter.php";

// Check all GET and POST parameters for malicious input
$attack = check_all_parameters($_SERVER['REQUEST_URI']);

if ($attack) {
    // Attack detected - redirect to warning page
    $attack_type = urlencode($attack['attack_type']);
    $payload = urlencode($attack['payload']);
    
    // Determine correct path based on current script location
    $script_path = $_SERVER['SCRIPT_NAME'];
    
    if (strpos($script_path, '/dashboard/') !== false) {
        // From dashboard pages: go up one level, then to auth
        $warning_path = '../auth/attack_warning.php';
    } elseif (strpos($script_path, '/auth/') !== false) {
        // From auth pages: same directory
        $warning_path = 'attack_warning.php';
    } else {
        // From landing page or other: go to auth folder
        $warning_path = 'auth/attack_warning.php';
    }
    
    header("Location: $warning_path?type=$attack_type&payload=$payload");
    exit;
}

?>
