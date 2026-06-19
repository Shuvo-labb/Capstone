document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
});

async function loadDashboard(){
  try{
    const res = await fetch('api/get_dashboard.php');
    const data = await res.json();

    document.getElementById('totalThreats').textContent = data.totalThreats;
    document.getElementById('openAlerts').textContent = data.openAlerts;
    document.getElementById('highSeverity').textContent = data.highSeverity;
    document.getElementById('lastUpload').textContent = data.lastUpload;

    const tbody = document.getElementById('threatsTable');
    tbody.innerHTML = '';

    if (!data.recent || data.recent.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="muted">No threats yet. Upload a log file to scan.</td></tr>';
    } else {
      data.recent.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${escapeHtml(row.detected_at)}</td>
          <td>${escapeHtml(row.type)}</td>
          <td>${escapeHtml(row.severity)}</td>
          <td>${escapeHtml(row.ip)}</td>
          <td>${escapeHtml(row.action_taken || 'Flagged')}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    renderCharts(data);
  }catch(err){
    console.error('Failed to load dashboard', err);
    document.getElementById('threatsTable').innerHTML =
      '<tr><td colspan="5" class="muted">Unable to load data.</td></tr>';
  }
}

function escapeHtml(str){
  if(typeof str !== 'string') return str;
  return str.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}

let threatsLineChart = null;
let severityDoughnut = null;
let topIpBarChart = null;

function renderCharts(data){
  if(typeof Chart === 'undefined') return;

  const recent = Array.isArray(data.recent) ? data.recent : [];

  const dateCounts = {};
  if (data.timeline && data.timeline.length) {
    data.timeline.forEach(p => { dateCounts[p.day] = Number(p.count); });
  } else {
    recent.forEach(r => {
      const d = String(r.detected_at || '').split(' ')[0] || 'unknown';
      dateCounts[d] = (dateCounts[d] || 0) + 1;
    });
  }
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
      options: { responsive: true, maintainAspectRatio: false, plugins: {legend:{display:false}} }
    });
  }

  const sevCounts = {Critical:0, High:0, Medium:0, Low:0};
  recent.forEach(r => {
    const s = r.severity || 'Low';
    if(sevCounts[s] !== undefined) sevCounts[s]++;
  });
  const sevLabels = Object.keys(sevCounts).filter(k => sevCounts[k] > 0);
  const sevData = sevLabels.map(k => sevCounts[k]);

  const ctxDough = document.getElementById('severityDoughnut');
  if(ctxDough){
    if(severityDoughnut) severityDoughnut.destroy();
    severityDoughnut = new Chart(ctxDough.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: sevLabels.length ? sevLabels : ['No Data'],
        datasets: [{ data: sevData.length ? sevData : [1], backgroundColor: ['#ff4757','#ff6b6b','#f6c85f','#9ef0ff'] }]
      },
      options: { responsive:true, maintainAspectRatio:false }
    });
  }

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
