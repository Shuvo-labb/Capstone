<?php
// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : weekly_report.php
// Description     : Dashboard page
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Wednesday,24-Jun-2026
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'weekly_report.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Weekly Report â€” Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Weekly Report</h2>
            <p class="muted" style="margin:0">Summary of security threats over the last 7 days.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="export_reports.php">Export</a>
          </div>
        </div>

        <section class="stats">
          <div class="stat-card">
            <div class="muted">Threats This Week</div>
            <div id="threatsWeek" class="stat-value">â€”</div>
          </div>
          <div class="stat-card">
            <div class="muted">Critical This Week</div>
            <div id="criticalWeek" class="stat-value">â€”</div>
          </div>
          <div class="stat-card">
            <div class="muted">Unresolved This Week</div>
            <div id="unresolvedWeek" class="stat-value">â€”</div>
          </div>
          <div class="stat-card">
            <div class="muted">Avg Per Day</div>
            <div id="avgPerDay" class="stat-value">â€”</div>
          </div>
        </section>

        <section class="section">
          <h3 style="margin-top:0">Weekly Threat Summary</h3>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr><th>Date</th><th>Total Threats</th><th>Critical</th><th>High</th><th>Medium</th><th>Low</th></tr>
              </thead>
              <tbody id="weekTable">
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
        const res = await fetch('api/get_weekly_summary.php');
        const data = await res.json();
        const tbody = document.getElementById('weekTable');
        
        if (!data.summary || data.summary.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" class="muted">No threats detected this week.</td></tr>';
          document.getElementById('threatsWeek').textContent = '0';
          document.getElementById('criticalWeek').textContent = '0';
          document.getElementById('unresolvedWeek').textContent = '0';
          document.getElementById('avgPerDay').textContent = '0';
          return;
        }

        let totalWeek = 0, criticalWeek = 0, unresolvedWeek = 0;
        tbody.innerHTML = '';
        
        data.summary.forEach(day => {
          totalWeek += day.total;
          criticalWeek += day.critical;
          unresolvedWeek += day.unresolved;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${escapeHtml(day.date)}</td>
            <td>${day.total}</td>
            <td>${day.critical}</td>
            <td>${day.high}</td>
            <td>${day.medium}</td>
            <td>${day.low}</td>
          `;
          tbody.appendChild(tr);
        });

        document.getElementById('threatsWeek').textContent = totalWeek;
        document.getElementById('criticalWeek').textContent = criticalWeek;
        document.getElementById('unresolvedWeek').textContent = unresolvedWeek;
        document.getElementById('avgPerDay').textContent = (totalWeek / 7).toFixed(1);
      } catch (err) {
        console.error('Failed to load weekly report', err);
        document.getElementById('weekTable').innerHTML = '<tr><td colspan="6" class="muted">Unable to load data.</td></tr>';
      }
    });

    function escapeHtml(str) {
      if (typeof str !== 'string') return str;
      return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }
  </script>
</body>
</html>
