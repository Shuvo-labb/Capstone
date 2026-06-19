<?php
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Activity Logs — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .logs-table{width:100%;border-collapse:collapse}
    .logs-table th, .logs-table td{padding:10px;border-bottom:1px solid rgba(118,153,207,0.04);text-align:left}
    .controls{display:flex;gap:8px;align-items:center}
    .log-row-meta{color:var(--muted);font-size:0.9rem}
    .logs-list { max-height:520px; overflow:auto; }
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
          <a href="activity_logs.php" class="active">Activity Logs</a>
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
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Activity Logs</h2>
            <p class="muted" style="margin:0">Chronological list of system and user activity.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
          </div>
        </div>

        <section class="section">
          <div class="controls">
            <label class="field"><span>Filter by user</span><input type="text" id="filterUser" placeholder="username"></label>
            <label class="field"><span>Action</span><input type="text" id="filterAction" placeholder="e.g. login"></label>
            <button id="applyFilters" class="primary-btn">Apply</button>
          </div>

          <div style="margin-top:12px" class="logs-list">
            <table class="logs-table" id="logsTable">
              <thead><tr><th>When</th><th>User</th><th>Action</th><th>Source IP</th></tr></thead>
              <tbody>
                <!-- filled by JS -->
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script src="../../js/dashboard/activity_logs.js" defer></script>
</body>
</html>
