// activity_logs.js — populates the Activity Logs page with mock entries and simple filtering.
document.addEventListener('DOMContentLoaded', () => {
  loadActivityLogs();
});

const mockLogs = [
  {when:'2026-05-26 11:10', user:'admin1', action:'Login', ip:'192.0.2.10'},
  {when:'2026-05-26 11:12', user:'securityadmin', action:'Viewed Report', ip:'198.51.100.42'},
  {when:'2026-05-25 09:20', user:'testadmin', action:'Upload Log', ip:'203.0.113.5'},
  {when:'2026-05-24 14:50', user:'admin1', action:'Resolved Threat', ip:'192.0.2.10'},
  {when:'2026-05-23 08:30', user:'securityadmin', action:'Changed Settings', ip:'198.51.100.42'}
];

function loadActivityLogs(){
  renderLogs(mockLogs);
  document.getElementById('applyFilters').addEventListener('click', applyFilters);
}

function renderLogs(rows){
  const tbody = document.querySelector('#logsTable tbody');
  tbody.innerHTML = '';
  rows.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeHtml(r.when)}</td><td>${escapeHtml(r.user)}</td><td>${escapeHtml(r.action)}</td><td>${escapeHtml(r.ip)}</td>`;
    tbody.appendChild(tr);
  });
}

function applyFilters(){
  const user = document.getElementById('filterUser').value.trim().toLowerCase();
  const action = document.getElementById('filterAction').value.trim().toLowerCase();
  const filtered = mockLogs.filter(r => {
    if(user && !r.user.toLowerCase().includes(user)) return false;
    if(action && !r.action.toLowerCase().includes(action)) return false;
    return true;
  });
  renderLogs(filtered);
}

function escapeHtml(str){ if(typeof str !== 'string') return str; return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
