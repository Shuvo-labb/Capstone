<?php
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Failed Login Attempts — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .attempt-row{display:flex;justify-content:space-between;padding:10px;border-radius:10px;background:rgba(255,255,255,0.02);margin-bottom:8px}
    .attempt-row .meta{color:var(--muted);font-size:0.9rem}
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
          <a href="suspicious_ips.php">Suspicious IPs</a>
          <a href="failed_logins.php" class="active">Failed Login Attempts</a>

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
            <h2 style="margin:0 0 6px 0">Failed Login Attempts</h2>
            <p class="muted" style="margin:0">List of recent failed logins and quick actions for blocking or investigating.</p>
          </div>
          <div class="actions"><a class="small-btn" href="../auth/logout.php">Logout</a></div>
        </div>

        <section class="section">
          <div style="margin-bottom:12px" class="controls">
            <label class="field"><span>Threshold</span><input id="threshold" type="number" value="5"></label>
            <button id="applyThreshold" class="primary-btn">Apply</button>
          </div>

          <div id="attemptsList">
            <!-- rows inserted by JS -->
          </div>
        </section>
      </div>
    </div>
  </main>

  <script src="../../js/dashboard/failed_logins.js" defer></script>
</body>
</html>
