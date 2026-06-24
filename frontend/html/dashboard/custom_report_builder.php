<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'custom_report_builder.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Custom Report Builder — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Custom Report Builder</h2>
            <p class="muted" style="margin:0">Filter threats by date, severity, and type to create custom reports.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="export_reports.php">Export</a>
          </div>
        </div>

        <section class="section" style="max-width:600px">
          <h3 style="margin-top:0">Filter Options</h3>
          <form id="customReportForm">
            <label class="field">
              <span>Date From</span>
              <input type="date" name="date_from" id="dateFrom">
            </label>

            <label class="field">
              <span>Date To</span>
              <input type="date" name="date_to" id="dateTo">
            </label>

            <label class="field">
              <span>Threat Type</span>
              <select name="threat_type" id="threatType">
                <option value="">All Types</option>
                <option value="SQL Injection">SQL Injection</option>
                <option value="XSS">XSS</option>
                <option value="Brute Force">Brute Force</option>
                <option value="Malware">Malware</option>
              </select>
            </label>

            <label class="field">
              <span>Severity</span>
              <select name="severity" id="severity">
                <option value="">All Severities</option>
                <option value="Critical">Critical</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
              </select>
            </label>

            <label class="field">
              <span>IP Address (optional)</span>
              <input type="text" name="ip_address" id="ipAddress" placeholder="192.168.1.1">
            </label>

            <label class="field">
              <span>Status</span>
              <select name="status" id="status">
                <option value="">All Status</option>
                <option value="0">Unresolved</option>
                <option value="1">Resolved</option>
              </select>
            </label>

            <button type="submit" class="primary-btn">Generate Report</button>
          </form>
        </section>

        <section class="section" style="margin-top:20px">
          <h3 style="margin-top:0">Report Results</h3>
          <div style="margin-bottom:12px">
            <span class="muted">Total Results: </span><span id="resultCount">0</span>
          </div>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Type</th><th>Severity</th><th>IP Address</th><th>Detected</th><th>Action</th><th>Status</th></tr>
              </thead>
              <tbody id="customResultsTable">
                <tr><td colspan="6" class="muted">Use the filters above to generate a custom report.</td></tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script>
    document.getElementById('customReportForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const params = new URLSearchParams(formData);
      
      try {
        const res = await fetch(`api/get_custom_report.php?${params}`);
        const data = await res.json();
        const tbody = document.getElementById('customResultsTable');
        
        if (!data.threats || data.threats.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" class="muted">No threats found matching your criteria.</td></tr>';
          document.getElementById('resultCount').textContent = '0';
          return;
        }

        tbody.innerHTML = '';
        data.threats.forEach(t => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(t.threat_type)}</td>
            <td><span style="color:${t.severity === 'Critical' ? '#ff4757' : t.severity === 'High' ? '#ff6b6b' : '#f6c85f'}">${escapeHtml(t.severity)}</span></td>
            <td>${escapeHtml(t.ip_address)}</td>
            <td>${escapeHtml(t.detected_at)}</td>
            <td>${escapeHtml(t.action_taken || 'Flagged')}</td>
            <td>${t.is_resolved ? '<span style="color:#4ade80">Resolved</span>' : '<span style="color:#ff6b6b">Open</span>'}</td>
          `;
          tbody.appendChild(tr);
        });

        document.getElementById('resultCount').textContent = data.threats.length;
      } catch (err) {
        console.error('Failed to generate custom report', err);
        document.getElementById('customResultsTable').innerHTML = '<tr><td colspan="6" class="muted">Unable to load data.</td></tr>';
      }
    });

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }
  </script>
</body>
</html>
