<?php
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statistics — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .stats-grid{display:grid;grid-template-columns:1fr 360px;gap:18px;align-items:start}
    .small-legend{display:flex;gap:8px;align-items:center}
    .legend-item{display:flex;gap:6px;align-items:center}
    .legend-swatch{width:12px;height:12px;border-radius:3px}
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
          <a href="real_time_alerts.php">Real-Time Alerts</a>
          <a href="statistics.php" class="active">Statistics</a>
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
            <h2 style="margin:0 0 6px 0">Statistics</h2>
            <p class="muted" style="margin:0">Charts and summary metrics for threat statistics.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="upload_log.php">Upload Log</a>
            <a class="small-btn" href="export_reports.php">Export Report</a>
          </div>
        </div>

        <section class="section">
          <div class="stats-grid">
            <div>
              <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                <div class="stat-card"><div class="muted">Total Threats</div><div id="totalThreatsStat" class="stat-value">—</div></div>
                <div class="stat-card"><div class="muted">Unique IPs</div><div id="uniqueIps" class="stat-value">—</div></div>
                <div class="stat-card"><div class="muted">Avg per day</div><div id="avgPerDay" class="stat-value">—</div></div>
              </div>

              <div style="margin-top:14px" class="section">
                <h3 style="margin-top:0">Threats Over Time</h3>
                <div style="height:260px"><canvas id="timeSeriesChart"></canvas></div>
              </div>

              <div style="margin-top:14px" class="section">
                <h3 style="margin-top:0">Threat Types</h3>
                <div style="height:220px"><canvas id="typePie"></canvas></div>
              </div>
            </div>

            <aside>
              <div class="section">
                <h3 style="margin-top:0">Top Source IPs</h3>
                <div style="height:300px"><canvas id="topIpsChart"></canvas></div>
              </div>

              <div class="section" style="margin-top:12px">
                <h3 style="margin-top:0">Filters</h3>
                <label class="field"><span>From</span><input type="date" id="fromDate"></label>
                <label class="field"><span>To</span><input type="date" id="toDate"></label>
                <button id="applyFilters" class="primary-btn">Apply</button>
              </div>
            </aside>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../../js/dashboard/statistics.js" defer></script>
</body>
</html>
