USE security_threat_dashboard;

CREATE TABLE IF NOT EXISTS PasswordResetTokens (
    token_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Default password for seeded users: password123
UPDATE Users
SET password_hash = '$2y$10$LJnjT735T4yEXz1I/gD5SuwkqjK1bjW320un09/t0Mfg9PW.TCqUG'
WHERE username IN ('admin1', 'securityadmin', 'testadmin');
