<?php
// Main Dashboard
// Notes:
// - This is a simple, clean starter dashboard page.
// - Authentication checks and backend APIs should be implemented in `backend/api`.
// - Keep UI minimal; JS will fetch real data when APIs exist.
// - Place this file under frontend/html/dashboard/index.php

// Start session if using PHP sessions for auth (placeholder)
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../auth/login.php');
//     exit;
// }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard — Main</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .dashboard-shell{padding:28px;}
    .topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:20px}
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:20px}
    .stat-card{background:rgba(255,255,255,0.03);padding:16px;border-radius:12px;border:1px solid rgba(118,153,207,0.06)}
    .stat-value{font-size:1.6rem;font-weight:700}
    .section{background:linear-gradient(180deg, rgba(16,27,46,0.95), rgba(10,18,33,0.98));padding:16px;border-radius:12px;border:1px solid rgba(118,153,207,0.06)}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px;border-bottom:1px solid rgba(118,153,207,0.04);text-align:left}
    .actions{display:flex;gap:8px}
    .small-btn{padding:8px 12px;border-radius:10px;background:rgba(76,201,240,0.12);border:1px solid rgba(76,201,240,0.18);cursor:pointer}
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
          <a href="../auth/login.php">Login</a>
          <a href="../auth/logout.php">Logout</a>
          <a href="../auth/forgot_password.php">Forgot Password</a>
          <a href="../auth/reset_password.php">Reset Password</a>

          <hr>
          <a href="index.php">Main Dashboard</a>
          <a href="#">Threat Overview</a>
          <a href="#">Real-Time Alerts</a>
          <a href="#">Statistics</a>
          <a href="#">Activity Logs</a>
          <a href="#">Suspicious IPs</a>
          <a href="#">Failed Login Attempts</a>

          <hr>
          <a href="#">Log Upload</a>
          <a href="#">Parser Results</a>
          <a href="#">Threat Analysis</a>
          <a href="#">Detailed Threat Report</a>
          <a href="#">SQL Injection Attempts</a>
          <a href="#">XSS Attempts</a>
          <a href="#">Malware Upload Attempts</a>

          <hr>
          <a href="#">Export Reports</a>
          <a href="#">Daily Report</a>
          <a href="#">Weekly Report</a>
          <a href="#">Monthly Report</a>
          <a href="#">Custom Report Builder</a>

          <hr>
          <a href="#">System Settings</a>
          <a href="#">Profile</a>
          <a href="#">Change Password</a>
          <a href="#">Audit Trail</a>
        </nav>
      </aside>

      <div class="main-content">
        <div class="topbar">
      <div>
        <h2 style="margin:0 0 6px 0">Main Dashboard</h2>
        <p class="muted" style="margin:0">Overview of threats, alerts, and system status.</p>
      </div>
      <div class="actions">
        <a class="small-btn" href="../../auth/logout.php">Logout</a>
        <a class="small-btn" href="../../log-handling/upload.php">Upload Log</a>
        <a class="small-btn" href="../../reporting/export_reports.php">Export Report</a>
      </div>
    </div>

    <section class="stats">
      <div class="stat-card">
        <div class="muted">Total Threats</div>
        <div id="totalThreats" class="stat-value">—</div>
      </div>
      <div class="stat-card">
        <div class="muted">Open Alerts</div>
        <div id="openAlerts" class="stat-value">—</div>
      </div>
      <div class="stat-card">
        <div class="muted">High Severity</div>
        <div id="highSeverity" class="stat-value">—</div>
      </div>
      <div class="stat-card">
        <div class="muted">Last Upload</div>
        <div id="lastUpload" class="stat-value">—</div>
      </div>
    </section>

    <section class="section charts-grid">
      <h3 style="margin-top:0">Overview Charts</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-top:12px">
        <div class="chart-card"><canvas id="threatsLineChart" height="140"></canvas></div>
        <div class="chart-card"><canvas id="severityDoughnut" height="140"></canvas></div>
        <div class="chart-card"><canvas id="topIpBarChart" height="140"></canvas></div>
      </div>
    </section>

    <section class="section">
      <h3 style="margin-top:0">Recent Threats</h3>
      <div style="overflow:auto">
        <table aria-describedby="recentThreats">
          <thead>
            <tr><th>Detected At</th><th>Type</th><th>Severity</th><th>IP Address</th><th>Action</th></tr>
          </thead>
          <tbody id="threatsTable">
            <tr><td colspan="5" class="muted">Loading…</td></tr>
          </tbody>
        </table>
      </div>
    </section>
      </div> <!-- /.main-content -->
    </div> <!-- /.dashboard-layout -->
  </main>

  <!-- Chart.js (simple CDN include) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../../js/dashboard/main.js" defer></script>
</body>
</html>
