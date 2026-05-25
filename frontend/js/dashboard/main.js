// main.js — simple dashboard behavior
// - Keeps code minimal and well commented
// - Replace mock data with real API calls when backend exists

document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
});

async function loadDashboard(){
  // Placeholder: try to fetch real data from backend when ready
  try{
    // Example fetch (backend endpoint to implement):
    // const res = await fetch('../../../backend/api/get_dashboard_stats.php');
    // const data = await res.json();
    // For now we use mocked data
    const data = {
      totalThreats: 124,
      openAlerts: 8,
      highSeverity: 3,
      lastUpload: '2026-05-24 11:12:00',
      recent: [
        {detected_at:'2026-05-24 11:10','type':'SQL Injection','severity':'High','ip':'192.0.2.10'},
        {detected_at:'2026-05-24 10:57','type':'XSS','severity':'Medium','ip':'198.51.100.42'},
        {detected_at:'2026-05-24 09:20','type':'Brute Force','severity':'Low','ip':'203.0.113.5'}
      ]
    };

    document.getElementById('totalThreats').textContent = data.totalThreats;
    document.getElementById('openAlerts').textContent = data.openAlerts;
    document.getElementById('highSeverity').textContent = data.highSeverity;
    document.getElementById('lastUpload').textContent = data.lastUpload;

    const tbody = document.getElementById('threatsTable');
    tbody.innerHTML = '';
    data.recent.forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${escapeHtml(row.detected_at)}</td>
        <td>${escapeHtml(row.type)}</td>
        <td>${escapeHtml(row.severity)}</td>
        <td>${escapeHtml(row.ip)}</td>
        <td><a href='#' class='small-btn'>Details</a></td>
      `;
      tbody.appendChild(tr);
    });

    // Render charts with the current data set (keeps UI and visuals in sync)
    renderCharts(data);

  }catch(err){
    console.error('Failed to load dashboard', err);
    const tbody = document.getElementById('threatsTable');
    tbody.innerHTML = '<tr><td colspan="5" class="muted">Unable to load data.</td></tr>';
  }
}

// small helper to avoid XSS in inserted text
function escapeHtml(str){
  if(typeof str !== 'string') return str;
  return str.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}

// Chart instances (kept in module scope so we can replace/destroy if needed)
let threatsLineChart = null;
let severityDoughnut = null;
let topIpBarChart = null;

// Render three simple charts: threats over time, severity distribution, top IPs
function renderCharts(data){
  if(typeof Chart === 'undefined') return; // Chart.js not loaded

  const recent = Array.isArray(data.recent) ? data.recent : [];

  // --- Line chart: threats by date ---
  const dateCounts = {};
  recent.forEach(r => {
    // Use only the date portion if timestamp present
    const d = String(r.detected_at || '').split(' ')[0] || 'unknown';
    dateCounts[d] = (dateCounts[d] || 0) + 1;
  });
  const lineLabels = Object.keys(dateCounts).length ? Object.keys(dateCounts) : ['No Data'];
  const lineData = lineLabels.map(l => dateCounts[l] || 0);

  const ctxLine = document.getElementById('threatsLineChart');
  if(ctxLine){
    if(threatsLineChart) threatsLineChart.destroy();
    threatsLineChart = new Chart(ctxLine.getContext('2d'), {
      type: 'line',
      data: {
        labels: lineLabels,
        datasets: [{
          label: 'Threats',
          data: lineData,
          borderColor: '#4cc9f0',
          backgroundColor: 'rgba(76,201,240,0.08)',
          fill: true,
          tension: 0.2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {legend:{display:false}}
      }
    });
  }

  // --- Doughnut chart: severity distribution ---
  const sevCounts = {High:0, Medium:0, Low:0};
  recent.forEach(r => { const s = r.severity || 'Low'; if(sevCounts[s] !== undefined) sevCounts[s]++; });
  const sevLabels = Object.keys(sevCounts);
  const sevData = sevLabels.map(k => sevCounts[k]);

  const ctxDough = document.getElementById('severityDoughnut');
  if(ctxDough){
    if(severityDoughnut) severityDoughnut.destroy();
    severityDoughnut = new Chart(ctxDough.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: sevLabels,
        datasets: [{ data: sevData, backgroundColor: ['#ff6b6b','#f6c85f','#9ef0ff'] }]
      },
      options: { responsive:true, maintainAspectRatio:false }
    });
  }

  // --- Bar chart: top IP addresses by event count ---
  const ipCounts = {};
  recent.forEach(r => { if(r.ip) ipCounts[r.ip] = (ipCounts[r.ip] || 0) + 1; });
  const ipLabels = Object.keys(ipCounts).length ? Object.keys(ipCounts) : ['No IPs'];
  const ipData = ipLabels.map(k => ipCounts[k] || 0);

  const ctxBar = document.getElementById('topIpBarChart');
  if(ctxBar){
    if(topIpBarChart) topIpBarChart.destroy();
    topIpBarChart = new Chart(ctxBar.getContext('2d'), {
      type: 'bar',
      data: { labels: ipLabels, datasets: [{ label: 'Events', data: ipData, backgroundColor: '#1fa2d6' }] },
      options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
    });
  }
}
