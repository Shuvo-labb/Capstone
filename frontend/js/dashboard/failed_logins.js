// Programmer Name : MD ABU SAYED SHUVO
// Program Name    : failed_logins.js
// Description     : Dashboard client script
// First Commit Date: Friday,19-Jun-2026
// Last Commit Date : Friday,19-Jun-2026
document.addEventListener('DOMContentLoaded', () => {
  const thresholdInput = document.getElementById('threshold');
  const initialThreshold = thresholdInput ? (Number(thresholdInput.value) || 5) : 5;
  loadFailedLogins(initialThreshold);
  
  const applyBtn = document.getElementById('applyThreshold');
  if (applyBtn) {
    applyBtn.addEventListener('click', () => {
      const t = Number(document.getElementById('threshold').value) || 1;
      loadFailedLogins(t);
    });
  }
});

async function loadFailedLogins(threshold = 5) {
  try {
    const res = await fetch(`api/get_failed_logins.php?threshold=${threshold}`);
    const data = await res.json();
    renderAttempts(data.attempts || []);
  } catch (err) {
    console.error('Failed to load failed logins', err);
    document.getElementById('attemptsList').innerHTML = '<div class="muted">Unable to load data.</div>';
  }
}

function renderAttempts(list) {
  const el = document.getElementById('attemptsList');
  el.innerHTML = '';
  if (list.length === 0) {
    el.innerHTML = '<div class="muted" style="padding:10px;">No failed login spikes detected.</div>';
    return;
  }
  list.forEach(a => {
    const d = document.createElement('div');
    d.className = 'attempt-row';
    d.innerHTML = `<div>
      <div><strong>${escapeHtml(a.ip)}</strong></div>
      <div class="meta">User: ${escapeHtml(a.user)}</div>
      <div class="meta">Last: ${escapeHtml(a.last)}</div>
    </div>
    <div style="text-align:right">
      <div class="meta">Attempts: <strong>${a.attempts}</strong></div>
      <div style="margin-top:8px"><button class="small-btn block-ip-btn" data-ip="${escapeHtml(a.ip)}">Block IP</button></div>
    </div>`;
    el.appendChild(d);
  });

  el.querySelectorAll('.block-ip-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const ip = e.currentTarget.dataset.ip;
      if (confirm(`Are you sure you want to block IP address: ${ip}?`)) {
        try {
          const res = await fetch('api/block_ip.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip: ip, reason: 'Brute force spike on login attempts', blocked: 1 })
          });
          const result = await res.json();
          alert(result.message);
          const thresholdInput = document.getElementById('threshold');
          loadFailedLogins(thresholdInput ? (Number(thresholdInput.value) || 1) : 1);
        } catch (err) {
          console.error('Failed to block IP', err);
          alert('Error blocking IP. Please try again.');
        }
      }
    });
  });
}

function escapeHtml(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}
