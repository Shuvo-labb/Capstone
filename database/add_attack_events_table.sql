-- Add AttackEvents table for direct input attack logging
-- This table stores attacks detected from direct user inputs (login forms, etc.)
-- Separate from Threats table which is used for log file parsing results

USE security_threat_dashboard;

CREATE TABLE IF NOT EXISTS AttackEvents (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
