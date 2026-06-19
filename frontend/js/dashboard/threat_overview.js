document.addEventListener('DOMContentLoaded', () => {
  loadThreatOverview();
});

async function loadThreatOverview(){
  try {
    const res = await fetch('api/get_dashboard.php');
    const data = await res.json();

    document.getElementById('openThreats').textContent = data.openAlerts;
    document.getElementById('highSeverity').textContent = data.highSeverity;

    const recent = data.recent || [];
    const timeline = (data.timeline || []).map(p => ({ date: p.day, count: Number(p.count) }));

    renderTimelineChart(timeline.length ? timeline : [{date:'No data', count:0}]);
    renderSeveritySmall(recent);
    populateRecentList(recent);
  } catch (err) {
    console.error(err);
    document.getElementById('threatsList').innerHTML = '<p class="muted">Unable to load threats.</p>';
  }
}

function renderTimelineChart(points){
  if(typeof Chart === 'undefined') return;
  const labels = points.map(p => p.date);
  const values = points.map(p => p.count);
  const ctx = document.getElementById('timelineChart');
  if(!ctx) return;
  new Chart(ctx.getContext('2d'), {
    type: 'line',
    data: { labels, datasets:[{ label:'Threats', data:values, borderColor:'#4cc9f0', backgroundColor:'rgba(76,201,240,0.08)', fill:true, tension:0.2 }] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
  });
}

function renderSeveritySmall(recent){
  if(typeof Chart === 'undefined') return;
  const counts = {Critical:0, High:0, Medium:0, Low:0};
  recent.forEach(r => {
    const s = r.severity || 'Low';
    if(counts[s] !== undefined) counts[s]++;
  });
  const labels = Object.keys(counts).filter(k => counts[k] > 0);
  const values = labels.map(l => counts[l]);
  const ctx = document.getElementById('severitySmall');
  if(!ctx) return;
  new Chart(ctx.getContext('2d'), {
    type:'doughnut',
    data:{ labels: labels.length ? labels : ['No Data'], datasets:[{ data: values.length ? values : [1], backgroundColor:['#ff4757','#ff6b6b','#f6c85f','#9ef0ff'] }] },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}
  });
}

function populateRecentList(rows){
  const container = document.getElementById('threatsList');
  const topIps = {};
  container.innerHTML = '';

  if (!rows.length) {
    container.innerHTML = '<p class="muted">No threats yet. Upload a log file first.</p>';
    return;
  }

  rows.forEach(r=>{
    const div = document.createElement('div');
    div.className = 'threat-row';
    div.innerHTML = `<div>
        <div><strong>${escapeHtml(r.type)}</strong></div>
        <div class="meta">${escapeHtml(r.detected_at)} — ${escapeHtml(r.ip)}</div>
      </div>
      <div style="text-align:right">
        <div class="meta">${escapeHtml(r.severity)}</div>
      </div>`;
    container.appendChild(div);
    topIps[r.ip] = (topIps[r.ip]||0)+1;
  });

  const ipsEl = document.getElementById('topIpsMini');
  if(ipsEl){
    ipsEl.innerHTML = Object.keys(topIps).map(ip =>
      `<div style="padding:6px 8px;border-radius:8px;background:rgba(255,255,255,0.02);margin-bottom:6px;display:flex;justify-content:space-between">
        <span class="meta">${escapeHtml(ip)}</span><strong>${topIps[ip]}</strong>
      </div>`
    ).join('');
  }
}

function escapeHtml(str){
  if(typeof str !== 'string') return str;
  return str.replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}
