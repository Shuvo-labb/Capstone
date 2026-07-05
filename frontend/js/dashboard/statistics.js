// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : statistics.js
// Description     : Dashboard client script
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
document.addEventListener('DOMContentLoaded', () => {
  initStatistics();
});

let timeSeriesChart, typePieChart, topIpsChart;

async function initStatistics() {
  try {
    const res = await fetch('api/get_statistics.php');
    const stats = await res.json();

    document.getElementById('totalThreatsStat').textContent = stats.totalThreats;
    document.getElementById('uniqueIps').textContent = stats.uniqueIps;
    document.getElementById('avgPerDay').textContent = stats.avgPerDay;

    renderTimeSeries(stats.timeseries || []);
    renderTypePie(stats.types || {});
    renderTopIps(stats.topIps || {});
  } catch (err) {
    console.error('Failed to load statistics', err);
  }

  const applyBtn = document.getElementById('applyFilters');
  if (applyBtn) {
    applyBtn.addEventListener('click', () => {
      initStatistics();
    });
  }
}

function renderTimeSeries(points) {
  if (typeof Chart === 'undefined') return;
  const labels = points.map(p => p.date);
  const data = points.map(p => p.count);
  const ctx = document.getElementById('timeSeriesChart');
  if (!ctx) return;
  if (timeSeriesChart) timeSeriesChart.destroy();
  timeSeriesChart = new Chart(ctx.getContext('2d'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Threats',
        data,
        borderColor: '#4cc9f0',
        backgroundColor: 'rgba(76,201,240,0.08)',
        fill: true,
        tension: 0.2
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
  });
}

function renderTypePie(types) {
  if (typeof Chart === 'undefined') return;
  const labels = Object.keys(types);
  const data = labels.map(l => types[l]);
  const ctx = document.getElementById('typePie');
  if (!ctx) return;
  if (typePieChart) typePieChart.destroy();
  typePieChart = new Chart(ctx.getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: labels.length ? labels : ['No Data'],
      datasets: [{
        data: data.length ? data : [1],
        backgroundColor: ['#ff6b6b', '#f6c85f', '#1fa2d6', '#9ef0ff', '#b8f5ff']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
  });
}

function renderTopIps(topIps) {
  if (typeof Chart === 'undefined') return;
  const labels = Object.keys(topIps);
  const data = labels.map(l => topIps[l]);
  const ctx = document.getElementById('topIpsChart');
  if (!ctx) return;
  if (topIpsChart) topIpsChart.destroy();
  topIpsChart = new Chart(ctx.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Events',
        data,
        backgroundColor: '#1fa2d6'
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
  });
}
