<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : suspicious_ips.php
// Description     : Dashboard page
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Suspicious IPs Ã¢â‚¬â€ Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .ip-row{display:flex;justify-content:space-between;padding:10px;border-radius:10px;background:rgba(255,255,255,0.02);margin-bottom:8px}
    .ip-row .meta{color:var(--muted);font-size:0.9rem}
  </style>
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <aside class="sidebar" aria-label="Primary navigation">
        <div class="sidebar-brand"><p class="eyebrow">Security Threat Dashboard</p></div>
        <nav class="nav-links">
          <a href="../auth/logout.php">Logout</a>

          <hr>
          <a href="index.php">Main Dashboard</a>
          <a href="threat_overview.php">Threat Overview</a>
          <a href="real_time_alerts.php">Real-Time Alerts</a>
          <a href="statistics.php">Statistics</a>
          <a href="activity_logs.php">Activity Logs</a>
          <a href="suspicious_ips.php" class="active">Suspicious IPs</a>
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
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Suspicious IPs</h2>
            <p class="muted" style="margin:0">IPs flagged by the system for suspicious activity.</p>
          </div>
          <div class="actions"><a class="small-btn" href="../auth/logout.php">Logout</a></div>
        </div>

        <section class="section">
          <div style="margin-bottom:12px">
            <label class="field"><span>Search IP</span><input id="searchIp" placeholder="192.0.2.10"></label>
            <button id="searchBtn" class="primary-btn">Search</button>
          </div>

          <div id="ipsList">
            <!-- rows inserted by JS -->
          </div>
        </section>
      </div>
    </div>
  </main>

  <script src="../../js/dashboard/suspicious_ips.js" defer></script>
</body>
</html>
