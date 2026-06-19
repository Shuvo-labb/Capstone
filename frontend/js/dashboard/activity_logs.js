document.addEventListener('DOMContentLoaded', () => {
  loadActivityLogs();
  document.getElementById('applyFilters').addEventListener('click', applyFilters);
});

async function loadActivityLogs() {
  try {
    const res = await fetch('api/get_activity_logs.php');
    const data = await res.json();
    renderLogs(data.logs || []);
  } catch (err) {
    console.error('Failed to load activity logs', err);
    document.querySelector('#logsTable tbody').innerHTML = '<tr><td colspan="4" class="muted">Unable to load data.</td></tr>';
  }
}

async function applyFilters() {
  const user = document.getElementById('filterUser').value.trim();
  const action = document.getElementById('filterAction').value.trim();
  try {
    const res = await fetch(`api/get_activity_logs.php?user=${encodeURIComponent(user)}&action=${encodeURIComponent(action)}`);
    const data = await res.json();
    renderLogs(data.logs || []);
  } catch (err) {
    console.error('Failed to filter activity logs', err);
  }
}

function renderLogs(rows) {
  const tbody = document.querySelector('#logsTable tbody');
  tbody.innerHTML = '';
  if (rows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4" class="muted">No activity logs found.</td></tr>';
    return;
  }
  rows.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeHtml(r.when)}</td><td>${escapeHtml(r.user)}</td><td>${escapeHtml(r.action)}</td><td>${escapeHtml(r.ip)}</td>`;
    tbody.appendChild(tr);
  });
}

function escapeHtml(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}
