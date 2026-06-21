<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SQL Injection Attempts — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .badge{padding:6px 8px;border-radius:10px;font-weight:700}
    .badge.critical{background:rgba(255,71,87,0.12);color:#ff4757}
    .badge.high{background:rgba(255,107,107,0.12);color:#ff6b6b}
    .badge.medium{background:rgba(246,200,95,0.08);color:#f6c85f}
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
          <a href="failed_logins.php">Failed Login Attempts</a>

          <hr>
          <a href="upload_log.php">Log Upload</a>
          <a href="parser_results.php">Parser Results</a>
          <a href="threat_analysis.php">Threat Analysis</a>
          <a href="detailed_threat_report.php">Detailed Threat Report</a>
          <a href="sql_injection_attempts.php" class="active">SQL Injection Attempts</a>
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
            <h2 style="margin:0 0 6px 0">SQL Injection Attempts</h2>
            <p class="muted" style="margin:0">Dedicated view for SQL injection payloads caught by the system.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="upload_log.php">Upload Log</a>
          </div>
        </div>

        <section class="stats">
          <div class="stat-card">
            <div class="muted">Total SQLi Attempts</div>
            <div id="totalSqli" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Critical Severity</div>
            <div id="criticalSqli" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unresolved</div>
            <div id="unresolvedSqli" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unique IPs</div>
            <div id="uniqueIps" class="stat-value">—</div>
          </div>
        </section>

        <section class="section">
          <h3 style="margin-top:0">SQL Injection Attempts</h3>
          <div style="margin-bottom:12px" class="controls">
            <button id="deleteResolvedSqli" class="primary-btn" style="background:#ff4757">Delete All Resolved</button>
          </div>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Severity</th><th>IP Address</th><th>Detected</th><th>Action</th><th>Status</th><th>Actions</th></tr>
              </thead>
              <tbody id="sqliTable">
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
        const res = await fetch('api/get_threats.php?type=SQL+Injection');
        const data = await res.json();
        const tbody = document.getElementById('sqliTable');
        
        if (!data.threats || data.threats.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="muted">No SQL injection attempts found.</td></tr>';
          return;
        }

        let total = 0, critical = 0, unresolved = 0, ips = new Set();
        tbody.innerHTML = '';
        
        data.threats.forEach(t => {
          total++;
          if (t.severity === 'Critical') critical++;
          if (!t.is_resolved) unresolved++;
          ips.add(t.ip_address);

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td><span class="badge ${t.severity.toLowerCase()}">${escapeHtml(t.severity)}</span></td>
            <td>${escapeHtml(t.ip_address)}</td>
            <td>${escapeHtml(t.detected_at)}</td>
            <td>${escapeHtml(t.action_taken || 'Flagged')}</td>
            <td>${t.is_resolved ? '<span style="color:#4ade80">Resolved</span>' : '<span style="color:#ff6b6b">Open</span>'}</td>
            <td>
              <button class="small-btn" onclick="deleteThreat(${t.threat_id}, '${t.source || 'threats'}')" style="background:#ff4757;color:white;padding:4px 8px;font-size:0.8rem">Delete</button>
            </td>
          `;
          tbody.appendChild(tr);
        });

        document.getElementById('totalSqli').textContent = total;
        document.getElementById('criticalSqli').textContent = critical;
        document.getElementById('unresolvedSqli').textContent = unresolved;
        document.getElementById('uniqueIps').textContent = ips.size;
      } catch (err) {
        console.error('Failed to load SQLi attempts', err);
        document.getElementById('sqliTable').innerHTML = '<tr><td colspan="5" class="muted">Unable to load data.</td></tr>';
      }
    });

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }

    // Delete individual threat
    async function deleteThreat(threatId, source) {
      if (!confirm('Are you sure you want to delete this threat?')) return;
      
      try {
        const res = await fetch('api/delete_threat.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({threat_id: threatId, source: source})
        });
        const data = await res.json();
        if (data.success) {
          alert('Threat deleted successfully');
          location.reload();
        } else {
          alert('Failed to delete threat: ' + data.message);
        }
      } catch (err) {
        console.error('Delete failed', err);
        alert('Failed to delete threat');
      }
    }

    // Delete all resolved threats
    document.getElementById('deleteResolvedSqli').addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete all resolved SQL injection threats? This action cannot be undone.')) return;
      
      try {
        const res = await fetch('api/delete_resolved.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({source: 'threats'})
        });
        const data = await res.json();
        if (data.success) {
          alert(`Deleted ${data.deleted_count} resolved threat(s)`);
          location.reload();
        } else {
          alert(data.message);
        }
      } catch (err) {
        console.error('Bulk delete failed', err);
        alert('Failed to delete resolved threats');
      }
    });
  </script>
</body>
</html>
