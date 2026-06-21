<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monthly Report — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
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
          <a href="monthly_report.php" class="active">Monthly Report</a>
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
            <h2 style="margin:0 0 6px 0">Monthly Report</h2>
            <p class="muted" style="margin:0">Summary of security threats over the last 30 days.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="export_reports.php">Export</a>
          </div>
        </div>

        <section class="stats">
          <div class="stat-card">
            <div class="muted">Threats This Month</div>
            <div id="threatsMonth" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Critical This Month</div>
            <div id="criticalMonth" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unresolved This Month</div>
            <div id="unresolvedMonth" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Avg Per Day</div>
            <div id="avgPerDay" class="stat-value">—</div>
          </div>
        </section>

        <section class="section">
          <h3 style="margin-top:0">Monthly Threat Summary by Week</h3>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Week</th><th>Total Threats</th><th>Critical</th><th>High</th><th>Medium</th><th>Low</th></tr>
              </thead>
              <tbody id="monthTable">
                <tr><td colspan="6" class="muted">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      try {
        const res = await fetch('api/get_monthly_summary.php');
        const data = await res.json();
        const tbody = document.getElementById('monthTable');
        
        if (!data.summary || data.summary.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" class="muted">No threats detected this month.</td></tr>';
          document.getElementById('threatsMonth').textContent = '0';
          document.getElementById('criticalMonth').textContent = '0';
          document.getElementById('unresolvedMonth').textContent = '0';
          document.getElementById('avgPerDay').textContent = '0';
          return;
        }

        let totalMonth = 0, criticalMonth = 0, unresolvedMonth = 0;
        tbody.innerHTML = '';
        
        data.summary.forEach(week => {
          totalMonth += week.total;
          criticalMonth += week.critical;
          unresolvedMonth += week.unresolved;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(week.week)}</td>
            <td>${week.total}</td>
            <td>${week.critical}</td>
            <td>${week.high}</td>
            <td>${week.medium}</td>
            <td>${week.low}</td>
          `;
          tbody.appendChild(tr);
        });

        document.getElementById('threatsMonth').textContent = totalMonth;
        document.getElementById('criticalMonth').textContent = criticalMonth;
        document.getElementById('unresolvedMonth').textContent = unresolvedMonth;
        document.getElementById('avgPerDay').textContent = (totalMonth / 30).toFixed(1);
      } catch (err) {
        console.error('Failed to load monthly report', err);
        document.getElementById('monthTable').innerHTML = '<tr><td colspan="6" class="muted">Unable to load data.</td></tr>';
      }
    });

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }
  </script>
</body>
</html>
