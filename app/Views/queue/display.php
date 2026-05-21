<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Now Serving | QueueMed</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Bebas+Neue&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: radial-gradient(ellipse at 30% 20%, #5a2070 0%, #2e0f45 50%, #1a0a2e 100%);
      color: #e8e0f5;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: grid;
      grid-template-rows: auto 1fr auto;
      overflow: hidden;
    }

    /* orbs */
    .orb {
      position: fixed; border-radius: 50%;
      pointer-events: none; filter: blur(90px); z-index: 0;
    }
    .orb-1 { width: 500px; height: 500px; background: rgba(180,80,200,0.18); top: -100px; left: -100px; }
    .orb-2 { width: 350px; height: 350px; background: rgba(120,60,200,0.15); bottom: 0; right: -80px; }

    /* ── HEADER ── */
    header {
      position: relative; z-index: 10;
      display: flex; align-items: center; justify-content: space-between;
      padding: 1.25rem 2.5rem;
      background: rgba(255,255,255,0.05);
      border-bottom: 1px solid rgba(255,255,255,0.08);
      backdrop-filter: blur(12px);
    }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand-icon {
      width: 42px; height: 42px;
      background: linear-gradient(135deg, #c86aaa, #9b5ec8);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
      box-shadow: 0 4px 16px rgba(180,80,200,0.4);
    }
    .brand-name { font-family: 'Bebas Neue', sans-serif; font-size: 2.2rem; letter-spacing: 2px; color: #fff; }
    .brand-sub  { font-size: .9rem; color: rgba(255,255,255,0.35); margin-top: -2px; }

    .clock { text-align: right; }
    .clock-time { font-family: 'Bebas Neue', sans-serif; font-size: 2.8rem; letter-spacing: 3px; color: #fff; }
    .clock-date { font-size: .9rem; color: rgba(255,255,255,0.35); }

    /* ── MAIN ── */
    main {
      position: relative; z-index: 1;
      display: grid;
      grid-template-columns: 1fr 360px;
      height: 100%;
    }

    /* NOW SERVING */
    .now-serving {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 3rem; position: relative; overflow: hidden;
    }
    .now-serving::before {
      content: '';
      position: absolute;
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(180,80,200,0.2) 0%, transparent 70%);
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }

    .ns-label {
      font-size: 1.5rem; font-weight: 700; letter-spacing: .25em;
      text-transform: uppercase; color: #f0c8ff;
      margin-bottom: 1.5rem;
      display: flex; align-items: center; gap: 12px;
    }
    .ns-label .dot {
      width: 16px; height: 16px;
      background: #c86aaa; border-radius: 50%;
      animation: blink 1.5s infinite;
      box-shadow: 0 0 16px rgba(200,106,170,0.9);
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

    .ns-number {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(8rem, 22vw, 18rem);
      line-height: 1; letter-spacing: -4px;
      color: #e8d5ff;
      text-shadow: 0 0 80px rgba(200,106,200,0.4);
      transition: all .4s ease;
    }
    .ns-number.empty { color: rgba(255,255,255,0.15); font-size: clamp(3rem, 8vw, 6rem); letter-spacing: 2px; }

    .ns-patient {
      margin-top: 1rem; font-size: 2rem; font-weight: 600;
      color: rgba(255,255,255,0.8);
    }
    .ns-service {
      margin-top: .5rem; font-size: 1.2rem;
      color: rgba(255,255,255,0.4);
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.1);
      padding: .4rem 1.2rem; border-radius: 50px;
    }

    /* ── SIDEBAR ── */
    .queue-sidebar {
      border-left: 1px solid rgba(255,255,255,0.08);
      background: rgba(255,255,255,0.04);
      backdrop-filter: blur(12px);
      display: flex; flex-direction: column; overflow: hidden;
    }
    .qs-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      display: flex; align-items: center; justify-content: space-between;
    }
    .qs-title { font-size: 1.1rem; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: rgba(255,255,255,0.7); }
    .qs-count {
      background: linear-gradient(135deg, #9b5ec8, #c86aaa);
      color: #fff; font-size: 1rem; font-weight: 700;
      padding: .3rem .9rem; border-radius: 50px;
      box-shadow: 0 2px 8px rgba(155,94,200,0.4);
    }

    .qs-list { flex: 1; overflow-y: auto; padding: .5rem 0; }
    .qs-list::-webkit-scrollbar { width: 3px; }
    .qs-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

    .qs-item {
      display: flex; align-items: center; gap: 14px;
      padding: .85rem 1.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      transition: background .2s;
    }
    .qs-item:hover { background: rgba(255,255,255,0.04); }
    .qs-item:last-child { border-bottom: none; }
    .qs-pos { font-size: .72rem; color: rgba(255,255,255,0.25); width: 20px; text-align: center; }
    .qs-num {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2.2rem; color: #d8a8f8; letter-spacing: 1px; min-width: 64px;
    }
    .qs-info { flex: 1; overflow: hidden; }
    .qs-name { font-size: 1.05rem; font-weight: 500; color: rgba(255,255,255,0.8); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .qs-svc  { font-size: .88rem; color: rgba(255,255,255,0.3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .qs-empty { padding: 2rem 1.5rem; text-align: center; color: rgba(255,255,255,0.45); font-size: 1.1rem; }

    /* ── FOOTER ── */
    footer {
      position: relative; z-index: 10;
      display: flex; align-items: center; justify-content: space-between;
      padding: .85rem 2.5rem;
      background: rgba(255,255,255,0.05);
      border-top: 1px solid rgba(255,255,255,0.08);
      backdrop-filter: blur(12px);
      gap: 1.5rem;
    }
    .stats { display: flex; gap: 2.5rem; }
    .stat-item { display: flex; flex-direction: column; align-items: center; }
    .stat-val  { font-family: 'Bebas Neue', sans-serif; font-size: 3rem; letter-spacing: 1px; }
    .stat-lbl  { font-size: .9rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: .1em; font-weight: 600; }
    .stat-waiting   .stat-val { color: #ffd060; }
    .stat-serving   .stat-val { color: #f0a0d8; }
    .stat-completed .stat-val { color: rgba(255,255,255,0.55); }

    /* ── PER-SERVICE BREAKDOWN ── */
    .service-breakdown {
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
      flex: 1;
      justify-content: center;
    }
    .svc-badge {
      display: flex;
      flex-direction: column;
      align-items: center;
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 12px;
      padding: .4rem 1.1rem;
      min-width: 90px;
    }
    .svc-badge-val {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2.2rem;
      color: #d8a8f8;
      line-height: 1;
    }
    .svc-badge-lbl {
      font-size: .68rem;
      color: rgba(255,255,255,0.4);
      text-transform: uppercase;
      letter-spacing: .07em;
      text-align: center;
      margin-top: 3px;
      line-height: 1.3;
    }

    .refresh-note { font-size: .88rem; color: rgba(255,255,255,0.35); display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .spin { animation: spin 2s linear infinite; display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }

    @keyframes flashIn {
      0%   { transform: scale(1.15); opacity: 0; }
      100% { transform: scale(1);    opacity: 1; }
    }
    .flash { animation: flashIn .5s ease; }
  </style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<header>
  <div class="brand">
    <div class="brand-icon"><i class="bi bi-clipboard2-pulse text-white"></i></div>
    <div>
      <div class="brand-name">QueueMed</div>
      <div class="brand-sub">Queue Management System</div>
    </div>
  </div>
  <div class="clock">
    <div class="clock-time" id="clock-time">00:00:00</div>
    <div class="clock-date" id="clock-date"></div>
  </div>
</header>

<main>
  <div class="now-serving">
    <div class="ns-label"><span class="dot"></span> Now Serving</div>
    <div class="ns-number" id="ns-number">—</div>
    <div class="ns-patient" id="ns-patient"></div>
    <div class="ns-service" id="ns-service" style="display:none;"></div>
  </div>

  <div class="queue-sidebar">
    <div class="qs-header">
      <span class="qs-title">Up Next</span>
      <span class="qs-count" id="qs-count">0</span>
    </div>
    <div class="qs-list" id="qs-list">
      <div class="qs-empty">No one waiting</div>
    </div>
  </div>
</main>

<footer>
  <div class="stats">
    <div class="stat-item stat-waiting">
      <span class="stat-val" id="stat-waiting">0</span>
      <span class="stat-lbl">Waiting</span>
    </div>
    <div class="stat-item stat-serving">
      <span class="stat-val" id="stat-serving">0</span>
      <span class="stat-lbl">Serving</span>
    </div>
    <div class="stat-item stat-completed">
      <span class="stat-val" id="stat-completed">0</span>
      <span class="stat-lbl">Completed</span>
    </div>
  </div>

  <div class="service-breakdown" id="service-breakdown"></div>

  <div class="refresh-note">
    <i class="bi bi-arrow-repeat spin"></i> Auto-refreshes every 5 seconds
  </div>
</footer>

<script>
function updateClock() {
  const now  = new Date();
  const time = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
  const date = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  document.getElementById('clock-time').textContent = time;
  document.getElementById('clock-date').textContent = date;
}
setInterval(updateClock, 1000);
updateClock();

let lastServing = null;
function pad(n) { return String(n).padStart(3, '0'); }

function fetchQueue() {
  fetch('<?= base_url('api/display') ?>')
    .then(r => r.json())
    .then(data => {
      const nsNum     = document.getElementById('ns-number');
      const nsPatient = document.getElementById('ns-patient');
      const nsService = document.getElementById('ns-service');

      if (data.serving) {
        const num = pad(data.serving.queue_number);
        if (num !== lastServing) {
          nsNum.classList.remove('flash');
          void nsNum.offsetWidth;
          nsNum.classList.add('flash');
          lastServing = num;
        }
        nsNum.textContent       = num;
        nsNum.classList.remove('empty');
        nsPatient.textContent   = data.serving.patient_name;
        nsService.textContent   = data.serving.service_name;
        nsService.style.display = 'inline-block';
      } else {
        nsNum.textContent       = '—';
        nsNum.classList.add('empty');
        nsPatient.textContent   = '';
        nsService.style.display = 'none';
        lastServing = null;
      }

      const waiting = (data.queue || []).filter(q => q.status === 'waiting');
      const list    = document.getElementById('qs-list');

      document.getElementById('qs-count').textContent       = waiting.length;
      document.getElementById('stat-waiting').textContent   = data.stats?.waiting   ?? 0;
      document.getElementById('stat-serving').textContent   = data.stats?.serving   ?? 0;
      document.getElementById('stat-completed').textContent = data.stats?.completed ?? 0;

      list.innerHTML = waiting.length === 0
        ? '<div class="qs-empty">No one waiting</div>'
        : waiting.map((q, i) => `
            <div class="qs-item">
              <span class="qs-pos">${i + 1}</span>
              <span class="qs-num">${pad(q.queue_number)}</span>
              <div class="qs-info">
                <div class="qs-name">${q.patient_name}</div>
                <div class="qs-svc">${q.service_name ?? ''}</div>
              </div>
            </div>`).join('');

      // Per-service breakdown badges
      const breakdown = document.getElementById('service-breakdown');
      if (data.stats?.by_service?.length) {
        breakdown.innerHTML = data.stats.by_service.map(s => `
          <div class="svc-badge">
            <span class="svc-badge-val">${s.waiting}</span>
            <span class="svc-badge-lbl">${s.service_name}<br>waiting</span>
          </div>
        `).join('');
      } else {
        breakdown.innerHTML = '';
      }
    })
    .catch(() => {});
}

fetchQueue();
setInterval(fetchQueue, 5000);
</script>
</body>
</html>