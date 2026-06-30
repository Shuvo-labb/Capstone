<?php
// Start PHP code block
// Include the database connection file
require_once __DIR__ . "/../../../database/db_connect.php";
// Include the email helper class file
require_once __DIR__ . "/../includes/EmailHelper.php";

// Define combined regular expression patterns for SQL Injection checks
$SQLI_HIGH_PAT = "/['\"][\\s]*OR[\\s]*(?:'?\\d+'?[\\s]*=[\\s]*'?\\d+|true[\\s]*--|1[\\s]*=[\\s]*1|\\d+[\\s]*=[\\s]*\\d+)|['\"][\\s]*;[\\s]*DROP|['\"][\\s]*UNION[\\s]+SELECT|\\bUNION[\\s]+ALL[\\s]+SELECT|['\"][\\s]*AND[\\s]*\\d+[\\s]*=[\\s]*\\d+|['\"][\\s]*(?:--|#|\\/\\*)/i";
// Define medium-severity SQL injection keywords check pattern
$SQLI_MED_PAT = "/\\b(?:SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|EXEC|EXECUTE)\\b|\\b(?:WHERE|HAVING|GROUP BY|ORDER BY)\\s+\\d+|\\b(?:OR|AND)\\s+(?:\\d+|['\"]\\w+['\"])\\s*[=<>!]|\\b(?:OR|AND)\\s+\\w+\\s+(?:LIKE|IN|BETWEEN|IS|REGEXP|RLIKE)/i";

// Define high-severity XSS regex pattern matching script blocks or event triggers
$XSS_HIGH_PAT = "/<script[^>]*>.*?<\\/script>|<script[^>]*>|javascript:|on\\w+\\s*=/is";
// Define medium-severity XSS regex pattern matching dangerous tags or function calls
$XSS_MED_PAT = "/<(?:iframe|object|embed|img|body|input|form|a|div|svg)\\b|eval\\s*\\(|document\\.(?:write|cookie)|window\\.(?:location|open)|(?:alert|confirm|prompt|setTimeout|setInterval|Function|atob|btoa)\\s*\\(|String\\.fromCharCode|innerHTML|outerHTML/i";

// Define Directory Traversal regex pattern matching system paths and dot-slash sequences
$DIR_TRAVERSAL_PAT = "/\\.\\.\\/|\\.\\.\\\\|etc\\/(?:passwd|shadow)|windows\\/system32|boot\\.ini|win\\.ini|proc\\/self|usr\\/s?bin|var\\/log|etc\\/hosts|root\\/\\.ssh/i";

// Declare function to detect input attack and record to log
function detect_and_log_attack($input_string, $source_context, $attempted_username = null) {
    // Import global variables into local function scope
    global $SQLI_HIGH_PAT, $SQLI_MED_PAT, $XSS_HIGH_PAT, $XSS_MED_PAT, $DIR_TRAVERSAL_PAT;
    // Set default value of attack type as null
    $attack_type = null;
    // Set default attack severity as Medium
    $severity = 'Medium';
    // Run SQL Injection high severity check
    if (preg_match($SQLI_HIGH_PAT, $input_string)) {
        // Set threat type to SQL Injection
        $attack_type = 'SQL Injection';
        // Upgrade severity level to High
        $severity = 'High';
    // Run SQL Injection medium severity check
    } elseif (preg_match($SQLI_MED_PAT, $input_string)) {
        // Set threat type to SQL Injection
        $attack_type = 'SQL Injection';
    // Run XSS high severity check
    } elseif (preg_match($XSS_HIGH_PAT, $input_string)) {
        // Set threat type to XSS
        $attack_type = 'XSS';
        // Upgrade severity level to High
        $severity = 'High';
    // Run XSS medium severity check
    } elseif (preg_match($XSS_MED_PAT, $input_string)) {
        // Set threat type to XSS
        $attack_type = 'XSS';
    // Run Directory Traversal check
    } elseif (preg_match($DIR_TRAVERSAL_PAT, $input_string)) {
        // Set threat type to Directory Traversal
        $attack_type = 'Directory Traversal';
        // Upgrade severity level to High
        $severity = 'High';
    }
    // Check if any attack pattern was matched
    if ($attack_type) {
        // Record the event details into database
        log_attack_event($attack_type, $input_string, $source_context, $attempted_username);
        // Check if attack severity is high
        if ($severity === 'High') {
            // Instantiate a new EmailHelper object
            $emailHelper = new EmailHelper();
            // Obtain client IP address or use default
            $source_ip = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
            // Dispatch notification alert email to administrator
            $emailHelper->sendAttackAlert($attack_type, $source_ip, $input_string, $source_context);
        }
        // Return structured details of intercepted attack
        return [
            'attack_type' => $attack_type,
            'severity' => $severity,
            'payload' => $input_string,
            'source_context' => $source_context
        ];
    }
    // Return false indicating input is clean
    return false;
}

// Check all GET and POST request parameters for attacks
function check_all_parameters($source_context) {
    // Loop through GET variables
    foreach ($_GET as $key => $value) {
        // Confirm variable contains a string
        if (is_string($value)) {
            // Perform attack analysis on GET value
            $attack = detect_and_log_attack($value, $source_context . " (GET: $key)");
            // Check if attack was identified
            if ($attack) {
                // Return first detected attack details
                return $attack;
            }
        }
    }
    // Loop through POST variables
    foreach ($_POST as $key => $value) {
        // Confirm variable contains a string
        if (is_string($value)) {
            // Perform attack analysis on POST value
            $attack = detect_and_log_attack($value, $source_context . " (POST: $key)");
            // Check if attack was identified
            if ($attack) {
                // Return first detected attack details
                return $attack;
            }
        }
    }
    // Return false indicating clean input
    return false;
}

// Save attack record into database
function log_attack_event($attack_type, $payload, $target_endpoint, $attempted_username = null) {
    // Import database connection object
    global $conn;
    // Get client remote IP or set default
    $source_ip = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
    // Get browser user agent or set default
    $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "Unknown";
    // Construct database table creation query if missing
    $create_table = "CREATE TABLE IF NOT EXISTS AttackEvents (
        attack_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        source_ip VARCHAR(45) NOT NULL,
        attack_type ENUM('SQL Injection','XSS','Directory Traversal','Brute Force','Other') NOT NULL,
        payload TEXT NOT NULL,
        target_endpoint VARCHAR(255) NOT NULL,
        attempted_username VARCHAR(100),
        user_agent VARCHAR(500),
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_attack_type (attack_type),
        INDEX idx_source_ip (source_ip),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    // Execute table creation query
    $conn->query($create_table);
    // Prepare SQL insert statement for logging attack event
    $stmt = $conn->prepare("INSERT INTO AttackEvents (source_ip, attack_type, payload, target_endpoint, attempted_username, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    // Bind input values safely to sql statement parameters
    $stmt->bind_param("ssssss", $source_ip, $attack_type, $payload, $target_endpoint, $attempted_username, $user_agent);
    // Execute prepared SQL statement
    $stmt->execute();
    // Close active prepared statement object
    $stmt->close();
}

// Check recent brute force occurrences count from IP
function detect_brute_force($ip_address, $threshold = 5) {
    // Import active database connection object
    global $conn;
    // Prepare statement to query failed login attempts within last 15 minutes
    $stmt = $conn->prepare("SELECT COUNT(*) as attempt_count FROM AttackEvents WHERE source_ip = ? AND attack_type = 'Brute Force' AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    // Bind current client IP value to statement
    $stmt->bind_param("s", $ip_address);
    // Execute query statement
    $stmt->execute();
    // Get database query result set
    $result = $stmt->get_result();
    // Fetch result row as associative array
    $row = $result->fetch_assoc();
    // Close prepared statement object
    $stmt->close();
    // Return true if counted attempts are greater than or equal to threshold
    return ($row['attempt_count'] >= $threshold);
}

// Record a new brute force attempt event
function log_brute_force($ip_address, $attempted_username = null) {
    // Log brute force type event via log_attack_event helper function
    log_attack_event('Brute Force', 'Multiple failed login attempts', 'login.php', $attempted_username);
}
?>
