<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Queue Ticket | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body { background: #f0f4f8; }

    .queue-number { font-size: 6rem; font-weight: 800; line-height: 1; }

    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

    .serving-glow {
      box-shadow: 0 0 40px rgba(25,135,84,.5);
      border: 3px solid #198754 !important;
    }

    #serving-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(25, 135, 84, 0.97);
      z-index: 9999;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      animation: fadeInOverlay 0.4s ease;
    }
    #serving-overlay.show { display: flex; }
    @keyframes fadeInOverlay { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

    .overlay-queue-number {
      font-size: 10rem;
      font-weight: 900;
      color: #fff;
      line-height: 1;
      text-shadow: 0 4px 30px rgba(0,0,0,0.2);
      animation: zoomPulse 1s ease-in-out infinite alternate;
    }
    @keyframes zoomPulse {
      from { transform: scale(1); }
      to   { transform: scale(1.06); }
    }

    .overlay-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 0.5rem;
    }
    .overlay-sub {
      font-size: 1.1rem;
      color: rgba(255,255,255,0.8);
      margin-bottom: 2rem;
    }
    .overlay-dismiss {
      background: #fff;
      color: #198754;
      border: none;
      font-weight: 700;
      font-size: 1rem;
      padding: 0.75rem 2rem;
      border-radius: 50px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .overlay-dismiss:hover { background: #e8f5e9; }

    .card { border-radius: 1.5rem !important; }
  </style>
</head>
<body>

<div id="serving-overlay">
  <div class="mb-3">
    <i class="bi bi-bell-fill text-white" style="font-size:4rem; animation: pulse 1s infinite;"></i>
  </div>
  <div class="overlay-title">🎉 It's Your Turn!</div>
  <div class="overlay-queue-number">
    #<?= $appointment['queue_number'] ?>
  </div>
  <div class="overlay-sub">Please proceed to the consultation room now.</div>
  <button class="overlay-dismiss" onclick="dismissOverlay()">
    <i class="bi bi-check-circle"></i> OK, I'm going!
  </button>
</div>

<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <!-- ✅ FIX 1: navbar brand now goes to home/booking page -->
    <a class="navbar-brand fw-bold text-primary" href="<?= base_url('/') ?>">
      <i class="bi bi-clipboard2-pulse"></i> QueueMed
    </a>
    <span class="small text-muted" id="last-updated">Connecting...</span>
  </div>
</nav>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card border-0 shadow text-center" id="ticket-card">

        <div class="card-header py-4 bg-primary" id="ticket-header">
          <h4 class="text-white fw-bold mb-0">
            <i class="bi bi-ticket-perforated-fill"></i> Queue Ticket
          </h4>
          <p class="text-white-50 mb-0 small">QueueMed Clinic</p>
        </div>

        <div class="card-body py-4">
          <div id="status-icon" class="mb-3">
            <i class="bi bi-hourglass-split text-primary" style="font-size:4rem;"></i>
          </div>

          <div id="status-alert" class="mx-3"></div>

          <div class="queue-number text-primary my-3" id="queue-number">
            #<?= $appointment['queue_number'] ?>
          </div>

          <div class="d-flex justify-content-center gap-4 my-3" id="stats-row">
            <div class="text-center">
              <h2 class="fw-bold text-danger mb-0" id="ahead-count"><?= $ahead ?></h2>
              <small class="text-muted">Ahead of you</small>
            </div>
            <div class="vr"></div>
            <div class="text-center">
              <h2 class="fw-bold text-primary mb-0">#<?= $appointment['queue_number'] ?></h2>
              <small class="text-muted">Your number</small>
            </div>
            <div class="vr"></div>
            <div class="text-center">
              <h2 class="fw-bold text-success mb-0" id="wait-time">~<?= $ahead * 15 ?></h2>
              <small class="text-muted">Mins wait</small>
            </div>
          </div>

          <div class="card bg-light border-0 text-start mx-2 mt-3" style="border-radius:1rem;">
            <div class="card-body">
              <h6 class="fw-bold mb-3"><i class="bi bi-calendar-check"></i> Appointment Details</h6>
              <div class="row g-2">
                <div class="col-6">
                  <small class="text-muted d-block">Patient</small>
                  <span class="fw-semibold"><?= esc($appointment['patient_name'] ?? session()->get('name')) ?></span>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block">Service</small>
                  <span class="fw-semibold"><?= esc($appointment['service_name']) ?></span>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block">Date</small>
                  <span class="fw-semibold"><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></span>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block">Time</small>
                  <span class="fw-semibold"><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></span>
                </div>
                <div class="col-12">
                  <small class="text-muted d-block">Status</small>
                  <span class="badge" id="status-badge"><?= ucfirst(str_replace('_',' ',$appointment['status'])) ?></span>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2 justify-content-center">
            <!-- ✅ FIX 2: button now goes to home/booking page -->
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-plus-circle"></i> Get Another Number
            </a>
            <button onclick="fetchStatus()" class="btn btn-primary">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
          </div>

          <p class="text-muted small mt-3" id="auto-refresh-text">
            <i class="bi bi-clock-history pulse"></i> Auto-updates every 10 seconds
          </p>
        </div>

        <div class="card-footer bg-light py-3">
          <small class="text-muted">Please keep this ticket. Do not lose your queue number.</small>
        </div>

      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const appointmentId = <?= $appointment['id'] ?>;
const apiUrl        = '<?= base_url('api/queue-status/') ?>' + appointmentId;
let   wasServing    = '<?= $appointment['status'] ?>' === 'serving';
let   overlayShown  = false;

function dismissOverlay() {
  document.getElementById('serving-overlay').classList.remove('show');
}

function showServingOverlay() {
  if (overlayShown) return;
  overlayShown = true;
  document.getElementById('serving-overlay').classList.add('show');
}

function updateUI(data) {
  const status     = data.status;
  const ahead      = data.ahead;
  const icon       = document.getElementById('status-icon');
  const alert      = document.getElementById('status-alert');
  const statsRow   = document.getElementById('stats-row');
  const badge      = document.getElementById('status-badge');
  const card       = document.getElementById('ticket-card');
  const header     = document.getElementById('ticket-header');
  const aheadCount = document.getElementById('ahead-count');
  const waitTime   = document.getElementById('wait-time');
  const refreshTxt = document.getElementById('auto-refresh-text');
  const queueNum   = document.getElementById('queue-number');

  aheadCount.textContent = ahead;
  waitTime.textContent   = '~' + (ahead * 15);

  const badgeColors = {
    pending:   'bg-warning text-dark',
    confirmed: 'bg-primary',
    in_queue:  'bg-info text-dark',
    serving:   'bg-success',
    completed: 'bg-secondary',
    cancelled: 'bg-danger',
  };
  badge.className   = 'badge ' + (badgeColors[status] || 'bg-secondary');
  badge.textContent = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

  if (status === 'serving') {
    if (!wasServing) showServingOverlay();
    wasServing = true;

    icon.innerHTML          = '<i class="bi bi-person-check-fill text-success" style="font-size:4rem;"></i>';
    alert.innerHTML         = '<div class="alert alert-success fw-bold fs-5 pulse"><i class="bi bi-bell-fill"></i> It\'s Your Turn! Please proceed now.</div>';
    queueNum.className      = 'queue-number text-success my-3';
    card.classList.add('serving-glow');
    header.style.background = '#198754';
    statsRow.classList.add('d-none');
    refreshTxt.classList.add('d-none');

  } else if (status === 'completed') {
    wasServing = false;
    icon.innerHTML  = '<i class="bi bi-check-circle-fill text-secondary" style="font-size:4rem;"></i>';
    alert.innerHTML = '<div class="alert alert-secondary">Your appointment is completed. Thank you!</div>';
    statsRow.classList.add('d-none');
    refreshTxt.classList.add('d-none');
    clearInterval(window.pollInterval);

  } else if (status === 'cancelled') {
    wasServing = false;
    icon.innerHTML  = '<i class="bi bi-x-circle-fill text-danger" style="font-size:4rem;"></i>';
    alert.innerHTML = '<div class="alert alert-danger">Your appointment was cancelled.</div>';
    statsRow.classList.add('d-none');
    refreshTxt.classList.add('d-none');
    clearInterval(window.pollInterval);

  } else {
    wasServing   = false;
    overlayShown = false;
    icon.innerHTML          = '<i class="bi bi-hourglass-split text-primary" style="font-size:4rem;"></i>';
    statsRow.classList.remove('d-none');
    card.classList.remove('serving-glow');
    header.style.background = '#0d6efd';
    queueNum.className      = 'queue-number text-primary my-3';

    if (ahead === 0) {
      alert.innerHTML = '<div class="alert alert-success fw-semibold"><i class="bi bi-bell-fill"></i> You\'re next! Please be ready.</div>';
    } else if (ahead <= 3) {
      alert.innerHTML = '<div class="alert alert-warning fw-semibold"><i class="bi bi-clock"></i> Almost your turn! Stay nearby.</div>';
    } else {
      alert.innerHTML = '<div class="alert alert-info"><i class="bi bi-hourglass-split"></i> Please wait patiently.</div>';
    }
  }

  document.getElementById('last-updated').textContent =
    'Updated: ' + new Date().toLocaleTimeString();
}

function fetchStatus() {
  fetch(apiUrl)
    .then(r => r.json())
    .then(data => updateUI(data))
    .catch(() => {
      document.getElementById('last-updated').textContent = 'Connection error. Retrying...';
    });
}

// Initial render from PHP
updateUI({
  status: '<?= $appointment['status'] ?>',
  ahead:  <?= $ahead ?>
});

// Poll every 10 seconds
window.pollInterval = setInterval(fetchStatus, 10000);
</script>
</body>
</html>