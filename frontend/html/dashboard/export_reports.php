<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'export_reports.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Export Reports — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Export Reports</h2>
            <p class="muted" style="margin:0">Export threat data in CSV or PDF format.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="index.php">Back to Dashboard</a>
          </div>
        </div>

        <section class="section" style="max-width:520px">
          <h3 style="margin-top:0">Export Options</h3>
          <form id="exportForm">
            <label class="field">
              <span>Report Type</span>
              <select name="report_type" id="reportType" required>
                <option value="all">All Threats</option>
                <option value="unresolved">Unresolved Only</option>
                <option value="critical">Critical Severity</option>
                <option value="sqli">SQL Injection</option>
                <option value="xss">XSS</option>
                <option value="malware">Malware</option>
              </select>
            </label>

            <label class="field">
              <span>Format</span>
              <select name="format" id="format" required>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
              </select>
            </label>

            <label class="field">
              <span>Date From</span>
              <input type="date" name="date_from" id="dateFrom">
            </label>

            <label class="field">
              <span>Date To</span>
              <input type="date" name="date_to" id="dateTo">
            </label>

            <label class="field">
              <span>IP Address</span>
              <input type="text" name="ip_address" id="ipAddress" placeholder="e.g. 192.168.1.45">
            </label>

            <button type="submit" class="primary-btn">Generate Export</button>
            <p id="exportMessage" class="message" aria-live="polite"></p>
          </form>
        </section>

        <section class="section" style="margin-top:20px">
          <h3 style="margin-top:0">Recent Exports</h3>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Report Type</th><th>Format</th><th>Date Range</th><th>Generated</th><th>Action</th></tr>
              </thead>
              <tbody id="exportsTable">
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
      loadExports();
    });

    document.getElementById('exportForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const msg = document.getElementById('exportMessage');
      msg.textContent = 'Generating export...';
      msg.style.color = '';

      try {
        const formData = new FormData(e.target);
        const res = await fetch('api/generate_export.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        if (data.success) {
          msg.textContent = 'Export generated successfully!';
          msg.style.color = 'green';
          loadExports();
        } else {
          msg.textContent = data.message || 'Export failed';
          msg.style.color = 'red';
        }
      } catch (err) {
        console.error('Export failed', err);
        msg.textContent = 'Export failed. Please try again.';
        msg.style.color = 'red';
      }
    });

    async function loadExports() {
      try {
        const res = await fetch('api/get_exports.php');
        const data = await res.json();
        const tbody = document.getElementById('exportsTable');
        
        if (!data.exports || data.exports.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="muted">No exports generated yet.</td></tr>';
          return;
        }

        tbody.innerHTML = '';
        data.exports.forEach(exp => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(exp.report_type)}</td>
            <td>${escapeHtml(exp.file_format)}</td>
            <td>${escapeHtml(exp.date_from)} to ${escapeHtml(exp.date_to)}</td>
            <td>${escapeHtml(exp.report_date)}</td>
            <td><a href="api/download_export.php?id=${exp.report_id}" class="small-btn">Download</a></td>
          `;
          tbody.appendChild(tr);
        });
      } catch (err) {
        console.error('Failed to load exports', err);
        document.getElementById('exportsTable').innerHTML = '<tr><td colspan="5" class="muted">Unable to load data.</td></tr>';
      }
    }

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }
  </script>
</body>
</html>
