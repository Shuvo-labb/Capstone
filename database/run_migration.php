<?php
require_once __DIR__ . "/db_connect.php";

echo "Starting database migration...\n";

// 1. PasswordResetTokens Table (if not exists)
$sqlPasswordReset = "CREATE TABLE IF NOT EXISTS PasswordResetTokens (
    token_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sqlPasswordReset)) {
    echo "Table 'PasswordResetTokens' checked/created.\n";
} else {
    echo "Error creating table PasswordResetTokens: " . $conn->error . "\n";
}

// 2. FailedLogins Table
$sqlFailedLogins = "CREATE TABLE IF NOT EXISTS FailedLogins (
    login_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50) NOT NULL,
    attempted_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sqlFailedLogins)) {
    echo "Table 'FailedLogins' checked/created.\n";
} else {
    echo "Error creating table FailedLogins: " . $conn->error . "\n";
}

// 3. SuspiciousIPs Table
$sqlSuspiciousIPs = "CREATE TABLE IF NOT EXISTS SuspiciousIPs (
    ip_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    reason VARCHAR(255) NOT NULL,
    first_seen DATETIME NOT NULL,
    last_seen DATETIME NOT NULL,
    is_blocked TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sqlSuspiciousIPs)) {
    echo "Table 'SuspiciousIPs' checked/created.\n";
} else {
    echo "Error creating table SuspiciousIPs: " . $conn->error . "\n";
}

// 4. AuditTrail Table
$sqlAuditTrail = "CREATE TABLE IF NOT EXISTS AuditTrail (
    audit_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NULL,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sqlAuditTrail)) {
    echo "Table 'AuditTrail' checked/created.\n";
} else {
    echo "Error creating table AuditTrail: " . $conn->error . "\n";
}

// Seed AuditTrail table if empty
$auditCountRes = $conn->query("SELECT COUNT(*) as c FROM AuditTrail");
$auditCount = $auditCountRes ? (int)$auditCountRes->fetch_assoc()["c"] : 0;
if ($auditCount === 0) {
    echo "Seeding AuditTrail table...\n";
    $auditSeeds = [
        [1, 'admin1', 'Login', '192.0.2.10', '2026-06-18 11:10:00'],
        [2, 'securityadmin', 'Viewed Report', '198.51.100.42', '2026-06-18 11:12:00'],
        [3, 'testadmin', 'Upload Log', '203.0.113.5', '2026-06-17 09:20:00'],
        [1, 'admin1', 'Resolved Threat', '192.0.2.10', '2026-06-16 14:50:00'],
        [2, 'securityadmin', 'Changed Settings', '198.51.100.42', '2026-06-15 08:30:00'],
    ];
    $stmt = $conn->prepare("INSERT INTO AuditTrail (user_id, username, action, ip_address, created_at) VALUES (?, ?, ?, ?, ?)");
    foreach ($auditSeeds as $seed) {
        $stmt->bind_param("issss", $seed[0], $seed[1], $seed[2], $seed[3], $seed[4]);
        $stmt->execute();
    }
    $stmt->close();
}

// Seed FailedLogins table if empty
$failedCountRes = $conn->query("SELECT COUNT(*) as c FROM FailedLogins");
$failedCount = $failedCountRes ? (int)$failedCountRes->fetch_assoc()["c"] : 0;
if ($failedCount === 0) {
    echo "Seeding FailedLogins table...\n";
    // 12 attempts from 192.0.2.10 for unknown user
    for ($i = 0; $i < 12; $i++) {
        $conn->query("INSERT INTO FailedLogins (ip_address, username, attempted_at) VALUES ('192.0.2.10', 'unknown', '2026-06-18 11:00:00' - INTERVAL $i MINUTE)");
    }
    // 4 attempts from 203.0.113.5 for testuser
    for ($i = 0; $i < 4; $i++) {
        $conn->query("INSERT INTO FailedLogins (ip_address, username, attempted_at) VALUES ('203.0.113.5', 'testuser', '2026-06-17 09:10:00' - INTERVAL $i MINUTE)");
    }
    // 7 attempts from 198.51.100.42 for admin1
    for ($i = 0; $i < 7; $i++) {
        $conn->query("INSERT INTO FailedLogins (ip_address, username, attempted_at) VALUES ('198.51.100.42', 'admin1', '2026-06-18 10:40:00' - INTERVAL $i MINUTE)");
    }
}

// Seed SuspiciousIPs table if empty
$suspiciousCountRes = $conn->query("SELECT COUNT(*) as c FROM SuspiciousIPs");
$suspiciousCount = $suspiciousCountRes ? (int)$suspiciousCountRes->fetch_assoc()["c"] : 0;
if ($suspiciousCount === 0) {
    echo "Seeding SuspiciousIPs table...\n";
    $ipSeeds = [
        ['192.0.2.10', 'Multiple SQLi attempts', '2026-06-10 09:00:00', '2026-06-18 11:05:00', 1],
        ['198.51.100.42', 'XSS payloads', '2026-06-12 10:15:00', '2026-06-18 10:50:00', 0],
        ['203.0.113.5', 'Brute force', '2026-06-11 11:20:00', '2026-06-17 09:20:00', 0],
    ];
    $stmt = $conn->prepare("INSERT INTO SuspiciousIPs (ip_address, reason, first_seen, last_seen, is_blocked) VALUES (?, ?, ?, ?, ?)");
    foreach ($ipSeeds as $seed) {
        $stmt->bind_param("ssssi", $seed[0], $seed[1], $seed[2], $seed[3], $seed[4]);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();
echo "Migration completed successfully!\n";
?>
