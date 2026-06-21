<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Threat Dashboard — Change Password</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
<main class="dashboard-shell">
    <div class="dashboard-layout">

        <aside class="sidebar" aria-label="Primary navigation">
            <div class="sidebar-brand">
                <p class="eyebrow">Security Threat Dashboard</p>
            </div>

            <nav class="nav-links">
                <a href="../auth/logout.php">Logout</a>
                <hr>

                <a href="index.php">Main Dashboard</a>
                <a href="threat_overview.php">Threat Overview</a>
                <a href="real_time_alerts.php">Real-Time Alerts</a>
                <a href="statistics.php">Statistics</a>
                <a href="activity_logs.php">Activity Logs</a>
                <a href="suspicious_ips.php">Suspicious IPs</a>
                <a href="failed_logins.php">Failed Login Attempts</a>

                <hr>

                <a href="upload_log.php">Log Upload</a>
                <a href="parser_results.php">Parser Results</a>
                <a href="threat_analysis.php">Threat Analysis</a>
                <a href="detailed_threat_report.php">Detailed Threat Report</a>
                <a href="sql_injection_attempts.php">SQL Injection Attempts</a>
                <a href="xss_attempts.php">XSS Attempts</a>
                <a href="malware_attempts.php">Malware Upload Attempts</a>

                <hr>

                <a href="export_reports.php">Export Reports</a>
                <a href="daily_report.php">Daily Report</a>
                <a href="weekly_report.php">Weekly Report</a>
                <a href="monthly_report.php">Monthly Report</a>
                <a href="custom_report_builder.php">Custom Report Builder</a>

                <hr>

                <a href="system_settings.php">System Settings</a>
                <a href="profile.php">Profile</a>
                <a href="change_password.php" class="active">Change Password</a>
                <a href="audit_trail.php">Audit Trail</a>
            </nav>
        </aside>

        <div class="main-content">

            <div class="topbar">
                <div>
                    <h2 style="margin:0 0 6px 0">Change Password</h2>
                    <p class="muted" style="margin:0">Update your account password.</p>
                </div>
            </div>

            <section class="section" style="max-width:520px">
                <h3 style="margin-top:0">Change Password</h3>

                <form id="passwordForm" class="auth-form">

                    <label class="field">
                        <span>Current Password</span>
                        <input type="password" name="current_password" id="currentPassword" required>
                    </label>

                    <label class="field">
                        <span>New Password</span>
                        <input type="password" name="new_password" id="newPassword" required minlength="8">
                    </label>

                    <label class="field">
                        <span>Confirm New Password</span>
                        <input type="password" name="confirm_password" id="confirmPassword" required minlength="8">
                    </label>

                    <button type="submit" class="primary-btn">Update Password</button>

                    <p id="passwordMessage" class="message" aria-live="polite"></p>
                </form>
            </section>

        </div>
    </div>
</main>

<script>
document.getElementById("passwordForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const msg = document.getElementById("passwordMessage");
    const currentPassword = document.getElementById("currentPassword").value.trim();
    const newPassword = document.getElementById("newPassword").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();

    msg.className = "message";
    msg.textContent = "";

    if (currentPassword === "" || newPassword === "" || confirmPassword === "") {
        msg.className = "message error";
        msg.textContent = "Please fill in all fields.";
        return;
    }

    if (newPassword !== confirmPassword) {
        msg.className = "message error";
        msg.textContent = "Passwords do not match.";
        return;
    }

    msg.textContent = "Updating password...";

    try {
        const response = await fetch("api/change_password.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        });

        const data = await response.json();

        msg.className = data.success ? "message success" : "message error";
        msg.textContent = data.message;

        if (data.success) {
            document.getElementById("passwordForm").reset();
        }

    } catch (error) {
        console.error(error);
        msg.className = "message error";
        msg.textContent = "Failed to update password. Please try again.";
    }
});
</script>

</body>
</html>