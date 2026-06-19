// real_time_alerts.js — lightweight simulated real-time alerts UI
// Keeps code simple: polls / simulates alerts and updates the UI + a small rate chart.

document.addEventListener('DOMContentLoaded', () => {
  initRealtimeAlerts();
});

let rateChart = null;
let alerts = [];

function initRealtimeAlerts(){
  // initialize chart with empty data
  const ctx = document.getElementById('rateChart');
  if(ctx && typeof Chart !== 'undefined'){
    rateChart = new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: { labels: [], datasets: [{ label: 'Alerts/min', data: [], borderColor:'#1fa2d6', backgroundColor:'rgba(31,162,214,0.06)', fill:true }] },
      options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
    });
  }

  // mock incoming alerts every 2-6 seconds
  setInterval(() => {
    const newAlert = generateMockAlert();
    pushAlert(newAlert);
  }, 3000 + Math.floor(Math.random()*3000));

  // update counts and chart periodically
  setInterval(() => { refreshUI(); }, 2000);

  // quick action handlers
  document.getElementById('ackAll').addEventListener('click', () => { alerts.forEach(a=>a.ack=true); refreshUI(); });
  document.getElementById('clearResolved').addEventListener('click', () => { alerts = alerts.filter(a=>!a.resolved); refreshUI(); });
}

function generateMockAlert(){
  const types = ['SQL Injection','XSS','Brute Force','Malware Upload','Port Scan'];
  const severities = ['High','Medium','Low'];
  const ip = `192.0.2.${Math.floor(1+Math.random()*250)}`;
  return { id:Date.now()+Math.floor(Math.random()*1000), detected_at: new Date().toISOString().replace('T',' ').slice(0,19), type: types[Math.floor(Math.random()*types.length)], severity: severities[Math.floor(Math.random()*severities.length)], ip, ack:false, resolved:false };
}

function pushAlert(alert){
  // keep most recent 200
  alerts.unshift(alert);
  if(alerts.length>200) alerts.pop();
  // briefly flash new alert
  renderAlertRow(alert, true);
}

function refreshUI(){
  // update counts
  const last5m = alerts.filter(a => { return (Date.now() - new Date(a.detected_at).getTime()) < 5*60*1000; }).length;
  const unresolved = alerts.filter(a => !a.resolved).length;
  document.getElementById('alertsCount').textContent = last5m;
  document.getElementById('unresolvedCount').textContent = unresolved;

  // update list (limit 100 displayed)
  const list = document.getElementById('alertsList');
  list.innerHTML = '';
  alerts.slice(0,100).forEach(a => renderAlertRow(a, false));

  // update rate chart: simple sliding window of last 10 intervals
  if(rateChart){
    const now = new Date();
    const label = now.toLocaleTimeString();
    const bucketCount = alerts.filter(a => (Date.now() - new Date(a.detected_at).getTime()) < 60*1000).length;
    const maxPoints = 10;
    rateChart.data.labels.push(label);
    rateChart.data.datasets[0].data.push(bucketCount);
    if(rateChart.data.labels.length>maxPoints){ rateChart.data.labels.shift(); rateChart.data.datasets[0].data.shift(); }
    rateChart.update();
  }
}

function renderAlertRow(a, flash){
  const list = document.getElementById('alertsList');
  if(!list) return;
  const row = document.createElement('div');
  row.className = 'alert-row';
  row.innerHTML = `<div>
      <div><strong>${escapeHtml(a.type)}</strong></div>
      <div class="meta">${escapeHtml(a.detected_at)} — ${escapeHtml(a.ip)}</div>
    </div>
    <div style="text-align:right">
      <div class="meta">${escapeHtml(a.severity)}</div>
      <div style="margin-top:6px">
        <button class="small-btn" data-id="${a.id}" data-action="ack">Acknowledge</button>
        <button class="small-btn" data-id="${a.id}" data-action="resolve">Resolve</button>
      </div>
    </div>`;

  // severity badge color
  if(a.severity === 'High') row.querySelector('.meta').insertAdjacentHTML('beforebegin', '<div class="badge high">HIGH</div>');
  else if(a.severity === 'Medium') row.querySelector('.meta').insertAdjacentHTML('beforebegin', '<div class="badge medium">MED</div>');
  else row.querySelector('.meta').insertAdjacentHTML('beforebegin', '<div class="badge low">LOW</div>');

  // attach action handlers
  row.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', (e)=>{
      const id = Number(e.currentTarget.dataset.id);
      const action = e.currentTarget.dataset.action;
      const idx = alerts.findIndex(x=>x.id===id);
      if(idx===-1) return;
      if(action==='ack') alerts[idx].ack = true;
      if(action==='resolve') alerts[idx].resolved = true;
      refreshUI();
    });
  });

  if(flash){
    row.style.transition = 'transform 220ms ease, opacity 220ms ease';
    row.style.transform = 'translateY(-6px)';
    row.style.opacity = '0';
    list.prepend(row);
    requestAnimationFrame(()=>{ row.style.transform='translateY(0)'; row.style.opacity='1'; });
    setTimeout(()=> refreshUI(), 400);
  } else {
    list.appendChild(row);
  }
}

function escapeHtml(str){ if(typeof str !== 'string') return str; return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
