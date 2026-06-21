<?php
require_once __DIR__ . "/db_connect.php";

// Create AttackEvents table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS AttackEvents (
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

if ($conn->query($sql)) {
    echo "AttackEvents table created successfully or already exists.\n";
} else {
    echo "Error creating AttackEvents table: " . $conn->error . "\n";
}

$conn->close();
?>
