<?php
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daily Report — Security Threat Dashboard</title>
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
          <a href="daily_report.php" class="active">Daily Report</a>
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
            <h2 style="margin:0 0 6px 0">Daily Report</h2>
            <p class="muted" style="margin:0">Summary of today's security threats and activities.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="export_reports.php">Export</a>
          </div>
        </div>

        <section class="stats">
          <div class="stat-card">
            <div class="muted">Threats Today</div>
            <div id="threatsToday" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Critical Today</div>
            <div id="criticalToday" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unresolved Today</div>
            <div id="unresolvedToday" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Logs Uploaded</div>
            <div id="logsToday" class="stat-value">—</div>
          </div>
        </section>

        <section class="section">
          <h3 style="margin-top:0">Today's Threats</h3>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Type</th><th>Severity</th><th>IP Address</th><th>Detected</th><th>Status</th></tr>
              </thead>
              <tbody id="todayTable">
                <tr><td colspan="5" class="muted">Loading...</td></tr>
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
        const today = new Date().toISOString().split('T')[0];
        const res = await fetch(`api/get_threats_by_date.php?date=${today}`);
        const data = await res.json();
        const tbody = document.getElementById('todayTable');
        
        if (!data.threats || data.threats.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="muted">No threats detected today.</td></tr>';
          document.getElementById('threatsToday').textContent = '0';
          document.getElementById('criticalToday').textContent = '0';
          document.getElementById('unresolvedToday').textContent = '0';
          return;
        }

        let total = 0, critical = 0, unresolved = 0;
        tbody.innerHTML = '';
        
        data.threats.forEach(t => {
          total++;
          if (t.severity === 'Critical') critical++;
          if (!t.is_resolved) unresolved++;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(t.threat_type)}</td>
            <td><span style="color:${t.severity === 'Critical' ? '#ff4757' : t.severity === 'High' ? '#ff6b6b' : '#f6c85f'}">${escapeHtml(t.severity)}</span></td>
            <td>${escapeHtml(t.ip_address)}</td>
            <td>${escapeHtml(t.detected_at)}</td>
            <td>${t.is_resolved ? '<span style="color:#4ade80">Resolved</span>' : '<span style="color:#ff6b6b">Open</span>'}</td>
          `;
          tbody.appendChild(tr);
        });

        document.getElementById('threatsToday').textContent = total;
        document.getElementById('criticalToday').textContent = critical;
        document.getElementById('unresolvedToday').textContent = unresolved;
      } catch (err) {
        console.error('Failed to load daily report', err);
        document.getElementById('todayTable').innerHTML = '<tr><td colspan="5" class="muted">Unable to load data.</td></tr>';
      }
    });

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }
  </script>
</body>
</html>
