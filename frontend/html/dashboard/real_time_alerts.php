<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : real_time_alerts.php
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
  <title>Real-Time Alerts Ã¢â‚¬â€ Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .alerts-grid{display:grid;grid-template-columns:1fr 300px;gap:18px;align-items:start}
    .alert-row{padding:12px;border-radius:10px;background:rgba(255,255,255,0.02);margin-bottom:10px;display:flex;justify-content:space-between;gap:12px}
    .alert-row .meta{color:var(--muted);font-size:0.9rem}
    .badge{padding:6px 8px;border-radius:10px;font-weight:700}
    .badge.high{background:rgba(255,107,107,0.12);color:#ff6b6b}
    .badge.medium{background:rgba(246,200,95,0.08);color:#f6c85f}
    .badge.low{background:rgba(158,240,255,0.06);color:#9ef0ff}
    .alerts-list { max-height:420px; overflow:auto; }
  </style>
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
          <a href="real_time_alerts.php" class="active">Real-Time Alerts</a>
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
          <a href="change_password.php">Change Password</a>
          <a href="audit_trail.php">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">RealÃ¢â‚¬â€˜Time Alerts</h2>
            <p class="muted" style="margin:0">Incoming alerts stream Ã¢â‚¬â€ mock realÃ¢â‚¬â€˜time view until backend is ready.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="upload_log.php">Upload Log</a>
            <a class="small-btn" href="export_reports.php">Export Report</a>
          </div>
        </div>

        <section class="section">
          <div class="alerts-grid">
            <div>
              <div style="display:flex;gap:12px">
                <div class="stat-card">
                  <div class="muted">Alerts (last 5m)</div>
                  <div id="alertsCount" class="stat-value">0</div>
                </div>
                <div class="stat-card">
                  <div class="muted">Unresolved</div>
                  <div id="unresolvedCount" class="stat-value">0</div>
                </div>
              </div>

              <div style="margin-top:14px" class="section">
                <h3 style="margin-top:0">Alerts Stream</h3>
                <div class="alerts-list" id="alertsList">
                  <!-- alerts inserted here -->
                </div>
              </div>
            </div>

            <aside>
              <div class="section">
                <h3 style="margin-top:0">Alerts Rate</h3>
                <div style="height:160px"><canvas id="rateChart"></canvas></div>
              </div>

              <div class="section" style="margin-top:12px">
                <h3 style="margin-top:0">Quick Actions</h3>
                <div style="display:grid;gap:8px;margin-top:8px">
                  <button class="primary-btn" id="ackAll">Acknowledge All</button>
                  <button class="primary-btn" id="clearResolved">Clear Resolved</button>
                </div>
              </div>
            </aside>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../../js/dashboard/real_time_alerts.js" defer></script>
</body>
</html>
