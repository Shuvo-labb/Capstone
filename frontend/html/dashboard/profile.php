<?php
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard — User Profile</title>
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
          <a href="profile.php" class="active">Profile</a>
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">User Profile</h2>
            <p class="muted" style="margin:0">View and manage your profile information.</p>
          </div>
        </div>

        <section class="section">
          <h3 style="margin-top:0">Profile Details</h3>
          <div style="display:grid;gap:16px;max-width:520px">
            <div class="stat-card">
              <div class="muted">Username</div>
              <div class="stat-value"><?php echo htmlspecialchars($_SESSION["username"] ?? "Unknown"); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">User ID</div>
              <div class="stat-value"><?php echo htmlspecialchars($_SESSION["user_id"] ?? "Unknown"); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">Account Status</div>
              <div class="stat-value" style="color:#4ade80">Active</div>
            </div>
          </div>
          <div style="margin-top:16px">
            <a href="change_password.php" class="primary-btn">Change Password</a>
          </div>
        </section>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
</body>
</html>