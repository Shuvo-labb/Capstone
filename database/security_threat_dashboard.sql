CREATE DATABASE IF NOT EXISTS security_threat_dashboard;

USE security_threat_dashboard;

CREATE TABLE Users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL,
    last_login DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE PasswordResetTokens (
    token_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Logs (
    log_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    log_file_name VARCHAR(255) NOT NULL,
    file_format ENUM('TXT','CSV','JSON') NOT NULL,
    file_size INT(11) NOT NULL,
    upload_timestamp DATETIME NOT NULL,
    uploaded_by INT(11) NOT NULL,
    parse_status ENUM('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (uploaded_by) REFERENCES Users(user_id)
);

CREATE TABLE Threats (
    threat_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    log_id INT(11) NOT NULL,
    threat_type VARCHAR(100) NOT NULL,
    severity ENUM('Low','Medium','High','Critical') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    action_taken VARCHAR(255),
    detected_at DATETIME NOT NULL,
    is_resolved TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (log_id) REFERENCES Logs(log_id)
);

CREATE TABLE Reports (
    report_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    report_type VARCHAR(50) NOT NULL,
    report_date DATE NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    file_format ENUM('PDF','CSV') NOT NULL,
    generated_by INT(11) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (generated_by) REFERENCES Users(user_id)
);

-- Default password for seeded users: password123
INSERT INTO Users(username,password_hash,email,created_at,last_login,is_active)
VALUES
('admin1','$2y$10$LJnjT735T4yEXz1I/gD5SuwkqjK1bjW320un09/t0Mfg9PW.TCqUG','admin1@security.com','2026-04-01 09:00:00','2026-04-20 10:15:00',1),
('securityadmin','$2y$10$LJnjT735T4yEXz1I/gD5SuwkqjK1bjW320un09/t0Mfg9PW.TCqUG','security@dashboard.com','2026-04-03 14:30:00','2026-04-21 08:40:00',1),
('testadmin','$2y$10$LJnjT735T4yEXz1I/gD5SuwkqjK1bjW320un09/t0Mfg9PW.TCqUG','test@security.com','2026-04-05 11:20:00',NULL,1);

INSERT INTO Logs(log_file_name,file_format,file_size,upload_timestamp,uploaded_by,parse_status)
VALUES
('apache_log_01.txt','TXT',4200,'2026-04-20 11:00:00',1,'Completed'),
('firewall_data.csv','CSV',8500,'2026-04-20 13:10:00',2,'Completed'),
('web_access.json','JSON',7200,'2026-04-21 09:45:00',1,'Pending');

INSERT INTO Threats(log_id,threat_type,severity,ip_address,action_taken,detected_at,is_resolved)
VALUES
(1,'Brute Force','High','192.168.1.45','Blocked','2026-04-20 11:30:00',1),
(2,'SQL Injection','Critical','103.52.44.18','Alerted','2026-04-20 14:15:00',0),
(3,'XSS','Medium','172.16.5.23','Flagged','2026-04-21 10:00:00',0),
(1,'Malware','Critical','45.10.22.88','Blocked','2026-04-20 12:00:00',1);

INSERT INTO Reports(report_type,report_date,date_from,date_to,file_format,generated_by,file_path)
VALUES
('Daily','2026-04-20','2026-04-20','2026-04-20','PDF',1,'/reports/daily_20_apr.pdf'),
('Weekly','2026-04-21','2026-04-14','2026-04-21','CSV',2,'/reports/weekly_apr.csv'),
('Custom','2026-04-21','2026-04-01','2026-04-21','PDF',1,'/reports/custom_security.pdf');
