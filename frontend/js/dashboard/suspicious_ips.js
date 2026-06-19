document.addEventListener('DOMContentLoaded', () => {
  loadSuspiciousIps();
  const searchBtn = document.getElementById('searchBtn');
  if (searchBtn) {
    searchBtn.addEventListener('click', () => {
      const q = document.getElementById('searchIp').value.trim();
      loadSuspiciousIps(q);
    });
  }
});

async function loadSuspiciousIps(query = '') {
  try {
    const res = await fetch(`api/get_suspicious_ips.php?q=${encodeURIComponent(query)}`);
    const data = await res.json();
    renderIps(data.ips || []);
  } catch (err) {
    console.error('Failed to load suspicious IPs', err);
    document.getElementById('ipsList').innerHTML = '<div class="muted">Unable to load data.</div>';
  }
}

function renderIps(list) {
  const el = document.getElementById('ipsList');
  el.innerHTML = '';
  if (list.length === 0) {
    el.innerHTML = '<div class="muted" style="padding:10px;">No suspicious IPs found.</div>';
    return;
  }
  list.forEach(ip => {
    const d = document.createElement('div');
    d.className = 'ip-row';
    d.innerHTML = `<div>
      <div><strong>${escapeHtml(ip.ip)}</strong></div>
      <div class="meta">${escapeHtml(ip.reason)}</div>
      <div class="meta">Seen: ${escapeHtml(ip.firstSeen)} → ${escapeHtml(ip.lastSeen)}</div>
    </div>
    <div style="text-align:right">
      <div style="margin-bottom:8px">${ip.blocked ? '<span class="badge high">Blocked</span>' : '<span class="badge low">Watching</span>'}</div>
      <div><button class="small-btn toggle-block-btn" data-ip="${escapeHtml(ip.ip)}" data-blocked="${ip.blocked ? 0 : 1}">
        ${ip.blocked ? 'Watch Only' : 'Block IP'}
      </button></div>
    </div>`;
    el.appendChild(d);
  });

  el.querySelectorAll('.toggle-block-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const ip = e.currentTarget.dataset.ip;
      const targetBlockedState = Number(e.currentTarget.dataset.blocked);
      const actionText = targetBlockedState ? 'block' : 'watchlist';
      
      if (confirm(`Are you sure you want to change status of ${ip} to ${actionText}?`)) {
        try {
          const res = await fetch('api/block_ip.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip: ip, reason: 'Status updated via watchlist tools', blocked: targetBlockedState })
          });
          const result = await res.json();
          alert(result.message);
          const searchInput = document.getElementById('searchIp');
          loadSuspiciousIps(searchInput ? searchInput.value.trim() : '');
        } catch (err) {
          console.error('Failed to update IP status', err);
          alert('Error updating IP status. Please try again.');
        }
      }
    });
  });
}

function escapeHtml(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}
