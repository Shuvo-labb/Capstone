// failed_logins.js — shows recent failed login attempts and allows threshold-based filtering (mocked)
document.addEventListener('DOMContentLoaded', ()=>{ loadFailedLogins(); });

const mockAttempts = [
  {ip:'192.0.2.10', user:'unknown', attempts:12, last:'2026-05-26 11:05'},
  {ip:'203.0.113.5', user:'testuser', attempts:4, last:'2026-05-25 09:20'},
  {ip:'198.51.100.42', user:'admin1', attempts:7, last:'2026-05-26 10:50'}
];

function loadFailedLogins(){
  renderAttempts(mockAttempts);
  document.getElementById('applyThreshold').addEventListener('click', ()=>{
    const t = Number(document.getElementById('threshold').value) || 1;
    renderAttempts(mockAttempts.filter(a=>a.attempts>=t));
  });
}

function renderAttempts(list){
  const el = document.getElementById('attemptsList');
  el.innerHTML = '';
  list.forEach(a=>{
    const d = document.createElement('div');
    d.className = 'attempt-row';
    d.innerHTML = `<div>
      <div><strong>${escapeHtml(a.ip)}</strong></div>
      <div class="meta">User: ${escapeHtml(a.user)}</div>
      <div class="meta">Last: ${escapeHtml(a.last)}</div>
    </div>
    <div style="text-align:right">
      <div class="meta">Attempts: <strong>${a.attempts}</strong></div>
      <div style="margin-top:8px"><button class="small-btn" data-ip="${escapeHtml(a.ip)}">Block IP</button></div>
    </div>`;
    el.appendChild(d);
  });
}

function escapeHtml(str){ if(typeof str !== 'string') return str; return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
