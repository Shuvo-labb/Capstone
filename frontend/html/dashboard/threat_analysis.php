// Programmer Name : TAN YONG SENG
// Program Name    : threat_analysis.php
// Description     : Dashboard page for threat analysis
// First Commit Date: Friday, 19-Jun-2026 08:25:00 AM
// Last Commit Date : Monday, 06-Jul-2026 07:09:42 AM


<?php
require_once __DIR__ . "/../auth/require_login.php";
require_once __DIR__ . "/../auth/global_security.php";
$active_page = 'threat_analysis.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Threat Analysis — Security Threat Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .badge{padding:6px 8px;border-radius:10px;font-weight:700;display:inline-block;min-width:72px;text-align:center}
    .badge.critical{background:rgba(255,71,87,0.12);color:#ff4757}
    .badge.high{background:rgba(255,107,107,0.12);color:#ff6b6b}
    .badge.medium{background:rgba(246,200,95,0.08);color:#f6c85f}
    .badge.low{background:rgba(158,240,255,0.06);color:#9ef0ff}
    .controls{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-bottom:14px}
    .controls input,.controls select{width:100%;border:1px solid rgba(118,153,207,.25);border-radius:12px;padding:10px 12px;background:rgba(7,17,31,.72);color:var(--text);outline:none}
    .danger-btn{background:#ff4757!important;color:white!important}
    .charts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-bottom:20px}
    .chart-card{
      background: linear-gradient(180deg, rgba(16,27,46,.95), rgba(10,18,33,.98));
      border:1px solid rgba(118,153,207,.08);
      border-radius:12px;
      padding:16px;

      position:relative;
      height:320px;
      overflow:hidden;
    }

    .chart-card canvas{
      width:100% !important;
      height:250px !important;
    }
    .chart-card h3{margin:0 0 10px 0}
    .insight-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:20px}
    .insight-card{background:rgba(255,255,255,.03);border:1px solid rgba(118,153,207,.08);border-radius:12px;padding:14px}
    .insight-title{color:var(--muted);font-size:.9rem;margin-bottom:6px}
    .insight-value{font-weight:700;font-size:1.05rem;line-height:1.5}
    .table-footer{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;margin-top:12px;color:var(--muted)}
  </style>
</head>
<body>
  <main class="dashboard-shell">
    <div class="dashboard-layout">
      <?php require_once __DIR__ . "/sidebar.php"; ?>

      <div class="main-content">
        <div class="topbar">
          <div>
            <h2 style="margin:0 0 6px 0">Threat Analysis</h2>
            <p class="muted" style="margin:0">Analyze detected threats by severity, IP address, type, status, and date.</p>
          </div>
          <div class="actions">
            <a class="small-btn" href="../auth/logout.php">Logout</a>
            <a class="small-btn" href="upload_log.php">Upload Log</a>
          </div>
        </div>

        <section class="stats">
          <div class="stat-card"><div class="muted">Total Threats</div><div id="totalThreats" class="stat-value">—</div></div>
          <div class="stat-card"><div class="muted">Critical Severity</div><div id="criticalCount" class="stat-value">—</div></div>
          <div class="stat-card"><div class="muted">Unresolved</div><div id="unresolvedCount" class="stat-value">—</div></div>
          <div class="stat-card"><div class="muted">Unique IPs</div><div id="uniqueIps" class="stat-value">—</div></div>
        </section>

        <section class="charts-grid">
          <div class="chart-card">
            <h3>Severity Distribution</h3>
            <canvas id="severityChart"></canvas>
          </div>
          <div class="chart-card">
            <h3>Threat Type Distribution</h3>
            <canvas id="typeChart"></canvas>
          </div>
        </section>

        <section class="insight-grid">
          <div class="insight-card"><div class="insight-title">Most Common Threat</div><div id="commonThreat" class="insight-value">—</div></div>
          <div class="insight-card"><div class="insight-title">Most Active IP</div><div id="activeIp" class="insight-value">—</div></div>
          <div class="insight-card"><div class="insight-title">Latest Detection</div><div id="latestDetection" class="insight-value">—</div></div>
          <div class="insight-card"><div class="insight-title">Resolution Rate</div><div id="resolutionRate" class="insight-value">—</div></div>
        </section>

        <section class="section">
          <h3 style="margin-top:0">Threat Analysis Table</h3>

          <div class="controls">
            <input id="searchInput" type="search" placeholder="Search type, IP, action...">
            <select id="severityFilter">
              <option value="">All Severities</option>
              <option>Critical</option><option>High</option><option>Medium</option><option>Low</option>
            </select>
            <select id="statusFilter">
              <option value="">All Status</option>
              <option value="open">Open</option>
              <option value="resolved">Resolved</option>
            </select>
            <input id="fromDate" type="date">
            <input id="toDate" type="date">
            <button id="resetFilters" class="small-btn">Reset Filters</button>
            <button id="deleteResolved" class="small-btn danger-btn">Delete All Resolved</button>
          </div>

          <div style="overflow:auto">
            <table>
              <thead>
                <tr>
                  <th>Type</th><th>Severity</th><th>IP Address</th><th>Detected</th><th>Action</th><th>Status</th><th>Actions</th>
                </tr>
              </thead>
              <tbody id="threatsTable">
                <tr><td colspan="7" class="muted">Loading...</td></tr>
              </tbody>
            </table>
          </div>
          <div class="table-footer">
            <span id="resultCount">Showing 0 threat(s)</span>
            <span>Tip: filter by Critical + Open to prioritize urgent response.</span>
          </div>
        </section>
      </div>
    </div>
  </main>

  <script>
    let allThreats = [];
    let severityChart = null;
    let typeChart = null;

    document.addEventListener('DOMContentLoaded', () => {
      loadThreats();
      ['searchInput','severityFilter','statusFilter','fromDate','toDate'].forEach(id => {
        document.getElementById(id).addEventListener('input', renderThreats);
      });
      document.getElementById('resetFilters').addEventListener('click', resetFilters);
      document.getElementById('deleteResolved').addEventListener('click', deleteResolvedThreats);
    });

    async function loadThreats() {
      const tbody = document.getElementById('threatsTable');
      try {
        const res = await fetch('api/get_threats.php', {headers:{'Accept':'application/json'}});
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        allThreats = Array.isArray(data.threats) ? data.threats : [];
        renderThreats();
      } catch (err) {
        console.error('Failed to load threat analysis', err);
        tbody.innerHTML = '<tr><td colspan="7" class="muted">Unable to load data.</td></tr>';
      }
    }

    function renderThreats() {
      const tbody = document.getElementById('threatsTable');
      const threats = getFilteredThreats();

      updateSummary(threats);
      updateInsights(threats);
      updateCharts(threats);
      document.getElementById('resultCount').textContent = `Showing ${threats.length} threat(s)`;

      if (threats.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="muted">No threats match the current filter.</td></tr>';
        return;
      }

      tbody.innerHTML = threats.map(t => {
        const id = Number(t.threat_id || t.attack_id || 0);
        const source = escapeAttribute(t.source || 'threats');
        const severity = normalizeSeverity(t.severity);
        const resolved = Boolean(Number(t.is_resolved));
        return `
          <tr>
            <td>${escapeHtml(t.threat_type || t.attack_type || 'Unknown')}</td>
            <td><span class="badge ${severity.toLowerCase()}">${escapeHtml(severity)}</span></td>
            <td>${escapeHtml(t.ip_address || t.source_ip || 'Unknown')}</td>
            <td>${escapeHtml(t.detected_at || t.created_at || '-')}</td>
            <td>${escapeHtml(t.action_taken || 'Flagged')}</td>
            <td>${resolved ? '<span style="color:#4ade80">Resolved</span>' : '<span style="color:#ff6b6b">Open</span>'}</td>
            <td><button class="small-btn danger-btn" onclick="deleteThreat(${id}, '${source}')">Delete</button></td>
          </tr>`;
      }).join('');
    }

    function getFilteredThreats() {
      const q = document.getElementById('searchInput').value.trim().toLowerCase();
      const severity = document.getElementById('severityFilter').value;
      const status = document.getElementById('statusFilter').value;
      const fromDate = document.getElementById('fromDate').value;
      const toDate = document.getElementById('toDate').value;

      return allThreats.filter(t => {
        const type = String(t.threat_type || t.attack_type || '').toLowerCase();
        const ip = String(t.ip_address || t.source_ip || '').toLowerCase();
        const action = String(t.action_taken || '').toLowerCase();
        const sev = normalizeSeverity(t.severity);
        const resolved = Boolean(Number(t.is_resolved));
        const dateText = String(t.detected_at || t.created_at || '').slice(0, 10);

        if (q && !(`${type} ${ip} ${action}`.includes(q))) return false;
        if (severity && sev !== severity) return false;
        if (status === 'open' && resolved) return false;
        if (status === 'resolved' && !resolved) return false;
        if (fromDate && dateText && dateText < fromDate) return false;
        if (toDate && dateText && dateText > toDate) return false;
        return true;
      });
    }

    function updateSummary(threats) {
      const uniqueIps = new Set();
      let critical = 0;
      let unresolved = 0;

      threats.forEach(t => {
        if (normalizeSeverity(t.severity) === 'Critical') critical++;
        if (!Boolean(Number(t.is_resolved))) unresolved++;
        uniqueIps.add(t.ip_address || t.source_ip || 'Unknown');
      });

      document.getElementById('totalThreats').textContent = threats.length;
      document.getElementById('criticalCount').textContent = critical;
      document.getElementById('unresolvedCount').textContent = unresolved;
      document.getElementById('uniqueIps').textContent = uniqueIps.size;
    }

    function updateInsights(threats) {
      const typeCounts = countBy(threats, t => t.threat_type || t.attack_type || 'Unknown');
      const ipCounts = countBy(threats, t => t.ip_address || t.source_ip || 'Unknown');
      const latest = [...threats].sort((a,b) => String(b.detected_at || b.created_at || '').localeCompare(String(a.detected_at || a.created_at || '')))[0];
      const resolved = threats.filter(t => Boolean(Number(t.is_resolved))).length;

      document.getElementById('commonThreat').textContent = topCountText(typeCounts);
      document.getElementById('activeIp').textContent = topCountText(ipCounts);
      document.getElementById('latestDetection').textContent = latest ? (latest.detected_at || latest.created_at || '-') : '—';
      document.getElementById('resolutionRate').textContent = threats.length ? `${Math.round((resolved / threats.length) * 100)}% resolved` : '—';
    }

    function updateCharts(threats) {
      const severityCounts = {
        Critical: threats.filter(t => normalizeSeverity(t.severity) === 'Critical').length,
        High: threats.filter(t => normalizeSeverity(t.severity) === 'High').length,
        Medium: threats.filter(t => normalizeSeverity(t.severity) === 'Medium').length,
        Low: threats.filter(t => normalizeSeverity(t.severity) === 'Low').length
      };
      const typeCounts = countBy(threats, t => t.threat_type || t.attack_type || 'Unknown');

      severityChart = drawChart(severityChart, 'severityChart', 'doughnut', Object.keys(severityCounts), Object.values(severityCounts));
      typeChart = drawChart(typeChart, 'typeChart', 'bar', Object.keys(typeCounts), Object.values(typeCounts));
    }

    function drawChart(existingChart, canvasId, type, labels, values) {
      if (existingChart) existingChart.destroy();
      return new Chart(document.getElementById(canvasId), {
        type,
        data: { labels, datasets: [{ data: values }] },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { labels: { color: '#eef4ff' } } },
          scales: type === 'bar' ? {
            x: { ticks: { color: '#eef4ff' }, grid: { color: 'rgba(255,255,255,.08)' } },
            y: { ticks: { color: '#eef4ff', precision:0 }, grid: { color: 'rgba(255,255,255,.08)' }, beginAtZero:true }
          } : {}
        }
      });
    }

    async function deleteThreat(threatId, source) {
      if (!threatId || !confirm('Are you sure you want to delete this threat?')) return;
      try {
        const res = await fetch('api/delete_threat.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({threat_id: threatId, source})
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Delete failed');
        await loadThreats();
      } catch (err) {
        alert(err.message || 'Failed to delete threat');
      }
    }

    async function deleteResolvedThreats() {
      if (!confirm('Are you sure you want to delete all resolved threats? This action cannot be undone.')) return;
      try {
        const res = await fetch('api/delete_resolved.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({source: 'all'})
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Bulk delete failed');
        alert(`Deleted ${data.deleted_count || 0} resolved threat(s)`);
        await loadThreats();
      } catch (err) {
        alert(err.message || 'Failed to delete resolved threats');
      }
    }

    function resetFilters() {
      ['searchInput','severityFilter','statusFilter','fromDate','toDate'].forEach(id => document.getElementById(id).value = '');
      renderThreats();
    }

    function normalizeSeverity(value) {
      const text = String(value || 'Low').toLowerCase();
      if (text === 'critical') return 'Critical';
      if (text === 'high') return 'High';
      if (text === 'medium') return 'Medium';
      return 'Low';
    }

    function countBy(items, selector) {
      return items.reduce((acc, item) => {
        const key = selector(item);
        acc[key] = (acc[key] || 0) + 1;
        return acc;
      }, {});
    }

    function topCountText(counts) {
      const top = Object.entries(counts).sort((a,b) => b[1] - a[1])[0];
      return top ? `${top[0]} (${top[1]})` : '—';
    }

    function escapeHtml(value) {
      return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }

    function escapeAttribute(value) {
      return escapeHtml(value).replace(/`/g, '&#096;');
    }
  </script>
</body>
</html>
