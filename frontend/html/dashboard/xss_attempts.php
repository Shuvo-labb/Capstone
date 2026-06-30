<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'xss_attempts.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>XSS Attempts — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .badge{padding:6px 8px;border-radius:10px;font-weight:700}
    .badge.high{background:rgba(255,107,107,0.12);color:#ff6b6b}
    .badge.medium{background:rgba(246,200,95,0.08);color:#f6c85f}
    .badge.low{background:rgba(158,240,255,0.06);color:#9ef0ff}
  </style>
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">XSS Attempts</h2>
            <p class="muted" style="margin:0">Dedicated view for Cross-Site Scripting blocks.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="upload_log.php">Upload Log</a>
          </div>
        </div>

        <section class="stats">
          <div class="stat-card">
            <div class="muted">Total XSS Attempts</div>
            <div id="totalXss" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">High Severity</div>
            <div id="highXss" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unresolved</div>
            <div id="unresolvedXss" class="stat-value">—</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unique IPs</div>
            <div id="uniqueIps" class="stat-value">—</div>
          </div>
        </section>

        <section class="section">
          <h3 style="margin-top:0">XSS Attempts</h3>
          <div style="margin-bottom:12px" class="controls">
            <button id="deleteResolvedXss" class="primary-btn" style="background:#ff4757">Delete All Resolved</button>
          </div>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Severity</th><th>IP Address</th><th>Detected</th><th>Action</th><th>Status</th><th>Actions</th></tr>
              </thead>
              <tbody id="xssTable">
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
        const res = await fetch('api/get_threats.php?type=XSS');
        const data = await res.json();
        const tbody = document.getElementById('xssTable');
        
        if (!data.threats || data.threats.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="muted">No XSS attempts found.</td></tr>';
          return;
        }

        let total = 0, high = 0, unresolved = 0, ips = new Set();
        tbody.innerHTML = '';
        
        data.threats.forEach(t => {
          total++;
          if (t.severity === 'High') high++;
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

        document.getElementById('totalXss').textContent = total;
        document.getElementById('highXss').textContent = high;
        document.getElementById('unresolvedXss').textContent = unresolved;
        document.getElementById('uniqueIps').textContent = ips.size;
      } catch (err) {
        console.error('Failed to load XSS attempts', err);
        document.getElementById('xssTable').innerHTML = '<tr><td colspan="5" class="muted">Unable to load data.</td></tr>';
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
    document.getElementById('deleteResolvedXss').addEventListener('click', async () => {
      if (!confirm('Are you sure you want to delete all resolved XSS threats? This action cannot be undone.')) return;
      
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
