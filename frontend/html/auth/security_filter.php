<?php
/**
 * Security Filter - Real-Time Threat Interception and Automated Logging
 * 
 * This helper detects malicious input patterns (SQLi, XSS, Directory Traversal)
 * and logs them to the AttackEvents table before allowing further processing.
 */

require_once __DIR__ . "/../../../database/db_connect.php";

/**
 * SQL Injection Detection Patterns
 * High severity: Classic SQLi bypass techniques
 * Medium severity: Suspicious SQL keywords
 */
$SQLI_PATTERNS = [
    // High severity - Classic bypass patterns
    "/'\\s*OR\\s*'\\d+'\\s*=\\s*'\\d+/i",
    "/'\\s*OR\\s*true\\s*--/i",
    "/'\\s*OR\\s*1\\s*=\\s*1/i",
    '/"\\s*OR\\s*"\\d+"\\s*=\\s*"\\d+/i',
    '/"\\s*OR\\s*true\\s*--/i',
    '/"\\s*OR\\s*1\\s*=\\s*1/i',
    "/'\\s*;\\s*DROP/i",
    '/"\\s*;\\s*DROP/i',
    "/'\\s*UNION\\s+SELECT/i",
    '/"\\s*UNION\\s+SELECT/i',
    "/\\bUNION\\s+ALL\\s+SELECT\\b/i",
    "/'\\s*OR\\s*\\d+\\s*=\\s*\\d+/i",
    '/"\\s*OR\\s*\\d+\\s*=\\s*\\d+/i',
    "/'\\s*AND\\s*\\d+\\s*=\\s*\\d+/i",
    '/"\\s*AND\\s*\\d+\\s*=\\s*\\d+/i',
    "/'\\s*--/i",
    '/"\\s*--/i',
    "/'\\s*#/i",
    '/"\\s*#/i',
    "/'\\s*\\/\\*/i",
    '/"\\s*\\/\\*/i',
    // Medium severity - Suspicious SQL keywords
    "/\\b(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|EXEC|EXECUTE)\\b/i",
    "/\\b(WHERE|HAVING|GROUP BY|ORDER BY)\\s+\\d+/i",
    "/\\b(OR|AND)\\s+\\d+\\s*[=<>!]/i",
    "/\\b(OR|AND)\\s+['\"]\\w+['\"]\\s*[=<>!]/i",
    "/\\b(OR|AND)\\s+\\w+\\s*LIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*IN\\s*\\(/i",
    "/\\b(OR|AND)\\s+\\w+\\s*BETWEEN/i",
    "/\\b(OR|AND)\\s+\\w+\\s*IS\\s*(NOT\\s*)?(NULL|TRUE|FALSE)/i",
    "/\\b(OR|AND)\\s+\\w+\\s*REGEXP/i",
    "/\\b(OR|AND)\\s+\\w+\\s*RLIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*SOUNDS\\s*LIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*LIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*IN/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*BETWEEN/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*REGEXP/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*RLIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*SOUNDS\\s*LIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*IS/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*REGEXP/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*RLIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*SOUNDS\\s*LIKE/i",
    "/\\b(OR|AND)\\s+\\w+\\s*NOT\\s*IS/i"
];

/**
 * XSS Detection Patterns
 * High severity: Script execution attempts
 * Medium severity: Suspicious HTML/JavaScript patterns
 */
$XSS_PATTERNS = [
    // High severity - Script execution
    "/<script[^>]*>.*?<\\/script>/is",
    "/<script[^>]*>/i",
    "/javascript:/i",
    "/onerror\\s*=/i",
    "/onload\\s*=/i",
    "/onclick\\s*=/i",
    "/onmouseover\\s*=/i",
    "/onfocus\\s*=/i",
    "/onblur\\s*=/i",
    "/onchange\\s*=/i",
    "/onsubmit\\s*=/i",
    "/onreset\\s*=/i",
    "/onkeydown\\s*=/i",
    "/onkeyup\\s*=/i",
    "/onkeypress\\s*=/i",
    "/onmousedown\\s*=/i",
    "/onmouseup\\s*=/i",
    "/onmousemove\\s*=/i",
    "/onmouseout\\s*=/i",
    "/onmouseenter\\s*=/i",
    "/onmouseleave\\s*=/i",
    "/ondblclick\\s*=/i",
    "/oncontextmenu\\s*=/i",
    "/onwheel\\s*=/i",
    "/onscroll\\s*=/i",
    "/oncopy\\s*=/i",
    "/oncut\\s*=/i",
    "/onpaste\\s*=/i",
    "/onbeforeunload\\s*=/i",
    "/onunload\\s*=/i",
    "/onresize\\s*=/i",
    "/onhashchange\\s*=/i",
    "/onpopstate\\s*=/i",
    "/onpageshow\\s*=/i",
    "/onpagehide\\s*=/i",
    "/onmessage\\s*=/i",
    "/onerror\\s*=/i",
    "/onoffline\\s*=/i",
    "/ononline\\s*=/i",
    "/onstorage\\s*=/i",
    "/ontoggle\\s*=/i",
    "/onanimationend\\s*=/i",
    "/onanimationiteration\\s*=/i",
    "/onanimationstart\\s*=/i",
    "/ontransitionend\\s*=/i",
    "/onended\\s*=/i",
    "/onpause\\s*=/i",
    "/onplay\\s*=/i",
    "/onplaying\\s*=/i",
    "/onseeked\\s*=/i",
    "/onseeking\\s*=/i",
    "/onstalled\\s*=/i",
    "/onsuspend\\s*=/i",
    "/ontimeupdate\\s*=/i",
    "/onvolumechange\\s*=/i",
    "/onwaiting\\s*=/i",
    "/oncanplay\\s*=/i",
    "/oncanplaythrough\\s*=/i",
    "/ondurationchange\\s*=/i",
    "/onloadeddata\\s*=/i",
    "/onloadedmetadata\\s*=/i",
    "/onloadstart\\s*=/i",
    "/onprogress\\s*=/i",
    "/onratechange\\s*=/i",
    "/onseeked\\s*=/i",
    "/onseeking\\s*=/i",
    "/onstalled\\s*=/i",
    "/onsuspend\\s*=/i",
    "/ontimeupdate\\s*=/i",
    "/onvolumechange\\s*=/i",
    "/onwaiting\\s*=/i",
    // Medium severity - Suspicious HTML/JavaScript
    "/<iframe[^>]*>/i",
    "/<object[^>]*>/i",
    "/<embed[^>]*>/i",
    "/<img[^>]*onerror/i",
    "/<img[^>]*onload/i",
    "/<body[^>]*onload/i",
    "/<body[^>]*onerror/i",
    "/<input[^>]*onfocus/i",
    "/<input[^>]*onblur/i",
    "/<input[^>]*onchange/i",
    "/<input[^>]*onclick/i",
    "/<form[^>]*onsubmit/i",
    "/<a[^>]*onclick/i",
    "/<a[^>]*onmouseover/i",
    "/<div[^>]*onclick/i",
    "/<div[^>]*onmouseover/i",
    "/<svg[^>]*onload/i",
    "/<svg[^>]*onerror/i",
    "/eval\\s*\\(/i",
    "/document\\.write/i",
    "/document\\.cookie/i",
    "/window\\.location/i",
    "/window\\.open/i",
    "/alert\\s*\\(/i",
    "/confirm\\s*\\(/i",
    "/prompt\\s*\\(/i",
    "/setTimeout\\s*\\(/i",
    "/setInterval\\s*\\(/i",
    "/Function\\s*\\(/i",
    "/fromCharCode/i",
    "/String\\.fromCharCode/i",
    "/atob\\s*\\(/i",
    "/btoa\\s*\\(/i",
    "/innerHTML\\s*=/i",
    "/outerHTML\\s*=/i",
    "/insertAdjacentHTML/i",
    "/\\.html\\s*=/i",
    "/\\.text\\s*=/i",
    "/\\.textContent\\s*=/i"
];

/**
 * Directory Traversal Detection Patterns
 */
$DIR_TRAVERSAL_PATTERNS = [
    "/\\.\\.\\//",
    "/\\.\\.\\\\/",
    "/\\.\\.\\.\\//",
    "/\\.\\.\\.\\\\/",
    "/%2e%2e%2f/i",
    "/%2e%2e%5c/i",
    "/%252e%252e%252f/i",
    "/%252e%252e%255c/i",
    "/\\.\\.\\/%00/",
    "/\\.\\.\\\\%00/",
    "/etc\\/passwd/i",
    "/etc\\/shadow/i",
    "/windows\\/system32/i",
    "/boot\\.ini/i",
    "/win\\.ini/i",
    "/proc\\/self/i",
    "/usr\\/bin/i",
    "/usr\\/sbin/i",
    "/var\\/log/i",
    "/etc\\/hosts/i",
    "/root\\/.ssh/i",
    "/home\\/.ssh/i"
];

/**
 * Detect and log attack from input string
 * 
 * @param string $input_string The input string to analyze
 * @param string $source_context The context (e.g., 'login', 'search', 'comment')
 * @param string $attempted_username Optional username from the attempt
 * @return array|false Returns attack details if detected, false if clean
 */
function detect_and_log_attack($input_string, $source_context, $attempted_username = null) {
    global $SQLI_PATTERNS, $XSS_PATTERNS, $DIR_TRAVERSAL_PATTERNS;
    
    $attack_type = null;
    $severity = 'Medium';
    
    // Check for SQL Injection
    foreach ($SQLI_PATTERNS as $pattern) {
        if (preg_match($pattern, $input_string)) {
            $attack_type = 'SQL Injection';
            // Determine severity based on pattern complexity
            if (stripos($pattern, "UNION") !== false || 
                stripos($pattern, "DROP") !== false ||
                stripos($pattern, "OR") !== false) {
                $severity = 'High';
            }
            break;
        }
    }
    
    // Check for XSS
    if (!$attack_type) {
        foreach ($XSS_PATTERNS as $pattern) {
            if (preg_match($pattern, $input_string)) {
                $attack_type = 'XSS';
                // Determine severity based on pattern complexity
                if (stripos($pattern, "script") !== false || 
                    stripos($pattern, "javascript:") !== false ||
                    stripos($pattern, "onerror") !== false ||
                    stripos($pattern, "onload") !== false) {
                    $severity = 'High';
                }
                break;
            }
        }
    }
    
    // Check for Directory Traversal
    if (!$attack_type) {
        foreach ($DIR_TRAVERSAL_PATTERNS as $pattern) {
            if (preg_match($pattern, $input_string)) {
                $attack_type = 'Directory Traversal';
                $severity = 'High';
                break;
            }
        }
    }
    
    // If attack detected, log to database
    if ($attack_type) {
        log_attack_event($attack_type, $input_string, $source_context, $attempted_username);
        return [
            'attack_type' => $attack_type,
            'severity' => $severity,
            'payload' => $input_string,
            'source_context' => $source_context
        ];
    }
    
    return false;
}

/**
 * Check all GET and POST parameters for malicious input
 * 
 * @param string $source_context The context (e.g., 'dashboard', 'api')
 * @return array|false Returns attack details if detected, false if clean
 */
function check_all_parameters($source_context) {
    // Check GET parameters
    foreach ($_GET as $key => $value) {
        if (is_string($value)) {
            $attack = detect_and_log_attack($value, $source_context . " (GET: $key)");
            if ($attack) {
                return $attack;
            }
        }
    }
    
    // Check POST parameters
    foreach ($_POST as $key => $value) {
        if (is_string($value)) {
            $attack = detect_and_log_attack($value, $source_context . " (POST: $key)");
            if ($attack) {
                return $attack;
            }
        }
    }
    
    return false;
}

/**
 * Log attack event to database
 * 
 * @param string $attack_type Type of attack
 * @param string $payload The malicious payload
 * @param string $target_endpoint The endpoint being attacked
 * @param string $attempted_username Optional username from attempt
 */
function log_attack_event($attack_type, $payload, $target_endpoint, $attempted_username = null) {
    global $conn;
    
    $source_ip = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
    $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "Unknown";
    
    // Ensure AttackEvents table exists
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
    
    $conn->query($create_table);
    
    // Insert attack event
    $stmt = $conn->prepare(
        "INSERT INTO AttackEvents (source_ip, attack_type, payload, target_endpoint, attempted_username, user_agent)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->bind_param("ssssss", 
        $source_ip, 
        $attack_type, 
        $payload, 
        $target_endpoint, 
        $attempted_username, 
        $user_agent
    );
    
    $stmt->execute();
    $stmt->close();
}

/**
 * Check for brute force patterns (multiple failed attempts from same IP)
 * 
 * @param string $ip_address The IP address to check
 * @param int $threshold Number of attempts to consider as brute force
 * @return bool True if brute force detected
 */
function detect_brute_force($ip_address, $threshold = 5) {
    global $conn;
    
    // Check for recent failed attempts from this IP
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as attempt_count 
         FROM AttackEvents 
         WHERE source_ip = ? 
         AND attack_type = 'Brute Force' 
         AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
    );
    
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return ($row['attempt_count'] >= $threshold);
}

/**
 * Log brute force attempt
 * 
 * @param string $ip_address The IP address
 * @param string $attempted_username Optional username
 */
function log_brute_force($ip_address, $attempted_username = null) {
    log_attack_event('Brute Force', 'Multiple failed login attempts', 'login.php', $attempted_username);
}

?>
