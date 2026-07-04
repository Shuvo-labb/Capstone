<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'parser_results.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parser Results — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/parserstyle.css">

</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Parser Results</h2>
            <p class="muted" style="margin:0">View results from parsed log files.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="upload_log.php">Upload Log</a>
          </div>
        </div>

        <section class="section">
          <h3 style="margin-top:0">Parsed Logs</h3>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Log File</th><th>Format</th><th>Size</th><th>Uploaded</th><th>Status</th><th>Threats Found</th></tr>
              </thead>
              <tbody id="logsTable">
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
        const res = await fetch('api/get_logs.php');
        const data = await res.json();
        const tbody = document.getElementById('logsTable');
        
        if (!data.logs || data.logs.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" class="muted">No logs parsed yet. Upload a log file to get started.</td></tr>';
          return;
        }

        tbody.innerHTML = '';
        data.logs.forEach(log => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(log.log_file_name)}</td>
            <td>${escapeHtml(log.file_format)}</td>
            <td>${formatBytes(log.file_size)}</td>
            <td>${escapeHtml(log.upload_timestamp)}</td>
            <td><span class="badge ${log.parse_status === 'Completed' ? 'low' : log.parse_status === 'Failed' ? 'high' : 'medium'}">${escapeHtml(log.parse_status)}</span></td>
            <td>${log.threat_count || 0}</td>
          `;
          tbody.appendChild(tr);
        });
      } catch (err) {
        console.error('Failed to load parser results', err);
        document.getElementById('logsTable').innerHTML = '<tr><td colspan="6" class="muted">Unable to load data.</td></tr>';
      }
    });

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }

    function formatBytes(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
  </script>
</body>
</html>
