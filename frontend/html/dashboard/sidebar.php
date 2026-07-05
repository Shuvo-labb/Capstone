<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : sidebar.php
// Description     : Dashboard page
// First Commit Date: Wednesday,24-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
// Sidebar navigation - reusable component for all dashboard pages
// Active page is set via $active_page variable before including this file
$active_page = $active_page ?? 'index.php';
?>

<aside class="sidebar" aria-label="Primary navigation">
  <div class="sidebar-brand"><p class="eyebrow">Security Threat Dashboard</p></div>
  <nav class="nav-links">
    <a href="../auth/logout.php">Logout</a>

    <hr>
    <a href="index.php" class="<?php echo $active_page === 'index.php' ? 'active' : ''; ?>">Main Dashboard</a>
    <a href="threat_overview.php" class="<?php echo $active_page === 'threat_overview.php' ? 'active' : ''; ?>">Threat Overview</a>
    <a href="real_time_alerts.php" class="<?php echo $active_page === 'real_time_alerts.php' ? 'active' : ''; ?>">Real-Time Alerts</a>
    <a href="statistics.php" class="<?php echo $active_page === 'statistics.php' ? 'active' : ''; ?>">Statistics</a>
    <a href="activity_logs.php" class="<?php echo $active_page === 'activity_logs.php' ? 'active' : ''; ?>">Activity Logs</a>
    <a href="suspicious_ips.php" class="<?php echo $active_page === 'suspicious_ips.php' ? 'active' : ''; ?>">Suspicious IPs</a>
    <a href="failed_logins.php" class="<?php echo $active_page === 'failed_logins.php' ? 'active' : ''; ?>">Failed Login Attempts</a>

    <hr>
    <a href="upload_log.php" class="<?php echo $active_page === 'upload_log.php' ? 'active' : ''; ?>">Log Upload</a>
    <a href="parser_results.php" class="<?php echo $active_page === 'parser_results.php' ? 'active' : ''; ?>">Parser Results</a>
    <a href="threat_analysis.php" class="<?php echo $active_page === 'threat_analysis.php' ? 'active' : ''; ?>">Threat Analysis</a>
    <a href="detailed_threat_report.php" class="<?php echo $active_page === 'detailed_threat_report.php' ? 'active' : ''; ?>">Detailed Threat Report</a>
    <a href="sql_injection_attempts.php" class="<?php echo $active_page === 'sql_injection_attempts.php' ? 'active' : ''; ?>">SQL Injection Attempts</a>
    <a href="xss_attempts.php" class="<?php echo $active_page === 'xss_attempts.php' ? 'active' : ''; ?>">XSS Attempts</a>
    <a href="malware_attempts.php" class="<?php echo $active_page === 'malware_attempts.php' ? 'active' : ''; ?>">Malware Upload Attempts</a>

    <hr>
    <a href="export_reports.php" class="<?php echo $active_page === 'export_reports.php' ? 'active' : ''; ?>">Export Reports</a>
    <a href="daily_report.php" class="<?php echo $active_page === 'daily_report.php' ? 'active' : ''; ?>">Daily Report</a>
    <a href="weekly_report.php" class="<?php echo $active_page === 'weekly_report.php' ? 'active' : ''; ?>">Weekly Report</a>
    <a href="monthly_report.php" class="<?php echo $active_page === 'monthly_report.php' ? 'active' : ''; ?>">Monthly Report</a>
    <a href="custom_report_builder.php" class="<?php echo $active_page === 'custom_report_builder.php' ? 'active' : ''; ?>">Custom Report Builder</a>

    <hr>
    <a href="system_settings.php" class="<?php echo $active_page === 'system_settings.php' ? 'active' : ''; ?>">System Settings</a>
    <a href="profile.php" class="<?php echo $active_page === 'profile.php' ? 'active' : ''; ?>">Profile</a>
    <a href="change_password.php" class="<?php echo $active_page === 'change_password.php' ? 'active' : ''; ?>">Change Password</a>
    <a href="audit_trail.php" class="<?php echo $active_page === 'audit_trail.php' ? 'active' : ''; ?>">Audit Trail</a>
  </nav>
</aside>

<?php require_once __DIR__ . '/../includes/chatbot.php'; ?>
