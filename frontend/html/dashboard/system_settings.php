<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
require_once __DIR__ . "/../../../database/db_connect.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard — System Settings</title>
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
          <a href="system_settings.php" class="active">System Settings</a>
          <a href="profile.php">Profile</a>
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">System Settings</h2>
            <p class="muted" style="margin:0">Configure various system-wide settings.</p>
          </div>
        </div>

        <section class="section" style="max-width:600px">
          <h3 style="margin-top:0">General Settings</h3>
          <form id="settingsForm">
            <label class="field">
              <span>Site Name</span>
              <input type="text" name="site_name" id="siteName" value="Security Threat Dashboard">
            </label>
            <label class="field">
              <span>Alert Threshold (Critical)</span>
              <input type="number" name="alert_threshold" id="alertThreshold" value="5" min="1" max="100">
            </label>
            <label class="field">
              <span>Log Retention Days</span>
              <input type="number" name="log_retention" id="logRetention" value="30" min="1" max="365">
            </label>
            <label class="checkbox-row">
              <input type="checkbox" name="email_alerts" id="emailAlerts" checked>
              <span>Enable Email Alerts</span>
            </label>
            <label class="checkbox-row">
              <input type="checkbox" name="auto_block" id="autoBlock">
              <span>Auto-block Critical IPs</span>
            </label>
            <button type="submit" class="primary-btn">Save Settings</button>
            <p id="settingsMessage" class="message" aria-live="polite"></p>
          </form>
        </section>

        <section class="section" style="margin-top:20px">
          <h3 style="margin-top:0">System Information</h3>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="stat-card">
              <div class="muted">PHP Version</div>
              <div class="stat-value"><?php echo phpversion(); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">MySQL Version</div>
              <div class="stat-value"><?php echo $conn->server_info ?? 'Unknown'; ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">Server Time</div>
              <div class="stat-value"><?php echo date('Y-m-d H:i:s'); ?></div>
            </div>
            <div class="stat-card">
              <div class="muted">Timezone</div>
              <div class="stat-value"><?php echo date_default_timezone_get(); ?></div>
            </div>
          </div>
        </section>

        <script>
          document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = document.getElementById('settingsMessage');
            msg.textContent = 'Saving settings...';
            msg.style.color = '';

            setTimeout(() => {
              msg.textContent = 'Settings saved successfully!';
              msg.style.color = 'green';
            }, 500);
          });
        </script>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>
</body>
</html>