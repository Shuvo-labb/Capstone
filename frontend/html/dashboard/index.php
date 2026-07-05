<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : index.php
// Description     : Dashboard page
// First Commit Date: Monday,25-May-2026
// Last Commit Date : Wednesday,24-Jun-2026
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'index.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Threat Dashboard â€” Main</title>
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
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
      <div>
        <h2 style="margin:0 0 6px 0">Main Dashboard</h2>
        <p class="muted" style="margin:0">Overview of threats, alerts, and system status.</p>
      </div>
      <div class="actions">
        <a class="small-btn" href="../auth/logout.php">Logout</a>
        <a class="small-btn" href="upload_log.php">Upload Log</a>
        <a class="small-btn" href="export_reports.php">Export Report</a>
      </div>
    </div>

    <section class="stats">
      <div class="stat-card">
        <div class="muted">Total Threats</div>
        <div id="totalThreats" class="stat-value">â€”</div>
      </div>
      <div class="stat-card">
        <div class="muted">Open Alerts</div>
        <div id="openAlerts" class="stat-value">â€”</div>
      </div>
      <div class="stat-card">
        <div class="muted">High Severity</div>
        <div id="highSeverity" class="stat-value">â€”</div>
      </div>
      <div class="stat-card">
        <div class="muted">Last Upload</div>
        <div id="lastUpload" class="stat-value">â€”</div>
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
            <tr><td colspan="5" class="muted">Loadingâ€¦</td></tr>
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
