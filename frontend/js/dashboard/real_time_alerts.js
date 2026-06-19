document.addEventListener('DOMContentLoaded', () => {
  initRealtimeAlerts();
});

let rateChart = null;
let alerts = [];

async function initRealtimeAlerts() {
  const ctx = document.getElementById('rateChart');
  if (ctx && typeof Chart !== 'undefined') {
    rateChart = new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: { labels: [], datasets: [{ label: 'Alerts/min', data: [], borderColor: '#1fa2d6', backgroundColor: 'rgba(31,162,214,0.06)', fill: true }] },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
  }

  await pollAlerts();
  setInterval(pollAlerts, 4000);

  const ackAllBtn = document.getElementById('ackAll');
  if (ackAllBtn) {
    ackAllBtn.addEventListener('click', async () => {
      if (confirm('Are you sure you want to resolve all active alerts?')) {
        for (const a of alerts) {
          if (!a.resolved) {
            await resolveThreatOnServer(a.id);
          }
        }
        await pollAlerts();
      }
    });
  }

  const clearResolvedBtn = document.getElementById('clearResolved');
  if (clearResolvedBtn) {
    clearResolvedBtn.addEventListener('click', () => {
      alerts = alerts.filter(a => !a.resolved);
      refreshUI();
    });
  }
}

async function pollAlerts() {
  try {
    const res = await fetch('api/get_real_time_alerts.php');
    const data = await res.json();
    
    const newAlerts = data.alerts || [];
    const oldIds = new Set(alerts.map(a => a.id));
    
    alerts = newAlerts;
    refreshUI();
    
    newAlerts.forEach(a => {
      if (oldIds.size > 0 && !oldIds.has(a.id)) {
        renderAlertRow(a, true);
      }
    });
  } catch (err) {
    console.error('Failed to poll alerts', err);
  }
}

function refreshUI() {
  const unresolved = alerts.filter(a => !a.resolved).length;
  
  const alertsCountEl = document.getElementById('alertsCount');
  if (alertsCountEl) alertsCountEl.textContent = unresolved;

  const unresolvedCountEl = document.getElementById('unresolvedCount');
  if (unresolvedCountEl) unresolvedCountEl.textContent = unresolved;

  const list = document.getElementById('alertsList');
  if (list) {
    list.innerHTML = '';
    alerts.forEach(a => renderAlertRow(a, false));
  }

  if (rateChart) {
    const now = new Date();
    const label = now.toLocaleTimeString();
    const bucketCount = alerts.filter(a => !a.resolved).length;
    const maxPoints = 10;
    rateChart.data.labels.push(label);
    rateChart.data.datasets[0].data.push(bucketCount);
    if (rateChart.data.labels.length > maxPoints) {
      rateChart.data.labels.shift();
      rateChart.data.datasets[0].data.shift();
    }
    rateChart.update();
  }
}

async function resolveThreatOnServer(threatId) {
  try {
    const res = await fetch('api/resolve_threat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ threat_id: threatId })
    });
    return await res.json();
  } catch (err) {
    console.error('Error resolving threat', err);
    return { success: false };
  }
}

function renderAlertRow(a, flash) {
  const list = document.getElementById('alertsList');
  if (!list) return;

  if (flash) {
    const existing = document.getElementById(`alert-row-${a.id}`);
    if (existing) return;
  }

  const row = document.createElement('div');
  row.id = `alert-row-${a.id}`;
  row.className = 'alert-row';
  
  const statusHtml = a.resolved 
    ? '<span style="color:#4ade80; font-size: 0.85rem; font-weight: bold; margin-left:10px;">Resolved</span>' 
    : `<button class="small-btn resolve-btn" data-id="${a.id}" style="margin-left:10px;">Resolve</button>`;

  row.innerHTML = `<div>
      <div><strong>${escapeHtml(a.type)}</strong> ${a.resolved ? statusHtml : ''}</div>
      <div class="meta">${escapeHtml(a.detected_at)} — ${escapeHtml(a.ip)}</div>
    </div>
    <div style="text-align:right">
      <div class="meta">${escapeHtml(a.severity)}</div>
      <div style="margin-top:6px">
        ${!a.resolved ? `<button class="small-btn resolve-btn" data-id="${a.id}">Resolve</button>` : ''}
      </div>
    </div>`;

  if (a.severity === 'High' || a.severity === 'Critical') {
    row.querySelector('.meta').insertAdjacentHTML('beforebegin', '<div class="badge high">HIGH</div>');
  } else if (a.severity === 'Medium') {
    row.querySelector('.meta').insertAdjacentHTML('beforebegin', '<div class="badge medium">MED</div>');
  } else {
    row.querySelector('.meta').insertAdjacentHTML('beforebegin', '<div class="badge low">LOW</div>');
  }

  const resolveButtons = row.querySelectorAll('.resolve-btn');
  resolveButtons.forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = Number(e.currentTarget.dataset.id);
      const res = await resolveThreatOnServer(id);
      if (res.success) {
        const idx = alerts.findIndex(x => x.id === id);
        if (idx !== -1) {
          alerts[idx].resolved = 1;
        }
        refreshUI();
      } else {
        alert('Could not resolve threat.');
      }
    });
  });

  if (flash) {
    row.style.transition = 'transform 220ms ease, opacity 220ms ease';
    row.style.transform = 'translateY(-6px)';
    row.style.opacity = '0';
    list.prepend(row);
    requestAnimationFrame(() => { row.style.transform = 'translateY(0)'; row.style.opacity = '1'; });
  } else {
    list.appendChild(row);
  }
}

function escapeHtml(str) {
  if (typeof str !== 'string') return str;
  return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}
