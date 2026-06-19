// suspicious_ips.js — display and search a small list of suspicious IPs (mocked)
document.addEventListener('DOMContentLoaded', () => { loadSuspiciousIps(); });

const mockIps = [
  {ip:'192.0.2.10', reason:'Multiple SQLi attempts', firstSeen:'2026-05-20', lastSeen:'2026-05-26', blocked:1},
  {ip:'198.51.100.42', reason:'XSS payloads', firstSeen:'2026-05-18', lastSeen:'2026-05-25', blocked:0},
  {ip:'203.0.113.5', reason:'Brute force', firstSeen:'2026-05-22', lastSeen:'2026-05-24', blocked:0}
];

function loadSuspiciousIps(){
  renderIps(mockIps);
  document.getElementById('searchBtn').addEventListener('click', ()=>{
    const q = document.getElementById('searchIp').value.trim();
    if(!q){ renderIps(mockIps); return; }
    renderIps(mockIps.filter(i => i.ip.includes(q)));
  });
}

function renderIps(list){
  const el = document.getElementById('ipsList');
  el.innerHTML = '';
  list.forEach(ip => {
    const d = document.createElement('div');
    d.className = 'ip-row';
    d.innerHTML = `<div>
      <div><strong>${escapeHtml(ip.ip)}</strong></div>
      <div class="meta">${escapeHtml(ip.reason)}</div>
      <div class="meta">Seen: ${escapeHtml(ip.firstSeen)} → ${escapeHtml(ip.lastSeen)}</div>
    </div>
    <div style="text-align:right">
      <div style="margin-bottom:8px">${ip.blocked?'<span class="badge high">Blocked</span>':'<span class="badge low">Watching</span>'}</div>
      <div><button class="small-btn" data-ip="${escapeHtml(ip.ip)}">Add to watchlist</button></div>
    </div>`;
    el.appendChild(d);
  });
}

function escapeHtml(str){ if(typeof str !== 'string') return str; return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
