<?php
require_once __DIR__ . "/../auth/require_login.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detailed Threat Report — Security Threat Dashboard</title>
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
          <a href="detailed_threat_report.php" class="active">Detailed Threat Report</a>
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
            <h2 style="margin:0 0 6px 0">Detailed Threat Report</h2>
            <p class="muted" style="margin:0">In-depth analysis of a specific threat incident.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="threat_analysis.php">Back to Analysis</a>
          </div>
        </div>

        <section class="section">
          <h3 style="margin-top:0">Select a Threat</h3>
          <div style="margin-bottom:12px">
            <label class="field"><span>Threat ID</span><input type="number" id="threatId" placeholder="Enter threat ID"></label>
            <button id="loadThreat" class="primary-btn">Load Report</button>
          </div>

          <div id="threatDetails" style="display:none">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
              <div class="stat-card">
                <div class="muted">Threat Type</div>
                <div id="detailType" class="stat-value">—</div>
              </div>
              <div class="stat-card">
                <div class="muted">Severity</div>
                <div id="detailSeverity" class="stat-value">—</div>
              </div>
              <div class="stat-card">
                <div class="muted">IP Address</div>
                <div id="detailIp" class="stat-value">—</div>
              </div>
              <div class="stat-card">
                <div class="muted">Detected At</div>
                <div id="detailDetected" class="stat-value">—</div>
              </div>
            </div>

            <div class="section">
              <h4>Action Taken</h4>
              <p id="detailAction" class="muted">—</p>
            </div>

            <div class="section" style="margin-top:12px">
              <h4>Status</h4>
              <p id="detailStatus" class="muted">—</p>
            </div>

            <div style="margin-top:12px">
              <button id="markResolved" class="primary-btn">Mark as Resolved</button>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script>
    document.getElementById('loadThreat').addEventListener('click', async () => {
      const threatId = document.getElementById('threatId').value;
      if (!threatId) return;

      try {
        const res = await fetch(`api/get_threat_details.php?id=${threatId}`);
        const data = await res.json();

        if (!data.threat) {
          alert('Threat not found');
          return;
        }

        document.getElementById('threatDetails').style.display = 'block';
        document.getElementById('detailType').textContent = data.threat.threat_type;
        document.getElementById('detailSeverity').textContent = data.threat.severity;
        document.getElementById('detailIp').textContent = data.threat.ip_address;
        document.getElementById('detailDetected').textContent = data.threat.detected_at;
        document.getElementById('detailAction').textContent = data.threat.action_taken || 'No action taken';
        document.getElementById('detailStatus').textContent = data.threat.is_resolved ? 'Resolved' : 'Unresolved';
      } catch (err) {
        console.error('Failed to load threat details', err);
        alert('Failed to load threat details');
      }
    });

    document.getElementById('markResolved').addEventListener('click', async () => {
      const threatId = document.getElementById('threatId').value;
      if (!threatId) return;

      try {
        const res = await fetch('api/resolve_threat.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({threat_id: threatId})
        });
        const data = await res.json();
        if (data.success) {
          alert('Threat marked as resolved');
          document.getElementById('detailStatus').textContent = 'Resolved';
        } else {
          alert('Failed to resolve threat');
        }
      } catch (err) {
        console.error('Failed to resolve threat', err);
        alert('Failed to resolve threat');
      }
    });
  </script>
</body>
</html>
