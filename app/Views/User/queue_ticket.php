<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Queue Ticket | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    .queue-number { font-size: 6rem; font-weight: 800; line-height: 1; }
    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    .serving-glow { box-shadow: 0 0 30px rgba(25,135,84,.4); border: 2px solid #198754; }
  </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="<?= base_url('patient/dashboard') ?>">
      <i class="bi bi-clipboard2-pulse"></i> QueueMed
    </a>
    <span class="small text-muted" id="last-updated">Connecting...</span>
  </div>
</nav>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <!-- Main ticket card -->
      <div class="card border-0 shadow text-center" id="ticket-card" style="border-radius:1.5rem; overflow:hidden;">

        <div class="card-header py-4 bg-primary" id="ticket-header">
          <h4 class="text-white fw-bold mb-0">
            <i class="bi bi-ticket-perforated-fill"></i> Queue Ticket
          </h4>
          <p class="text-white-50 mb-0 small">QueueMed Clinic</p>
        </div>

        <div class="card-body py-4" id="ticket-body">
          <!-- Status Icon -->
          <div id="status-icon" class="mb-3">
            <i class="bi bi-hourglass-split text-primary" style="font-size:4rem;"></i>
          </div>

          <!-- Status Message -->
          <div id="status-message"></div>

          <!-- Queue Number -->
          <div class="queue-number text-primary my-3" id="queue-number">
            #<?= $appointment['queue_number'] ?>
          </div>

          <!-- Alert -->
          <div id="status-alert" class="mx-3"></div>

          <!-- Stats Row -->
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

          <!-- Appointment Details -->
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

          <!-- Buttons -->
          <div class="mt-4 d-flex gap-2 justify-content-center">
            <a href="<?= base_url('patient/dashboard') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-house"></i> Dashboard
            </a>
            <button onclick="fetchStatus()" class="btn btn-primary" id="refresh-btn">
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
const apiUrl = '<?= base_url('api/queue-status/') ?>' + appointmentId;

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

  // Update ahead count
  aheadCount.textContent = ahead;
  waitTime.textContent   = '~' + (ahead * 15);

  // Update badge
  const badgeColors = {
    pending:   'bg-warning text-dark',
    confirmed: 'bg-primary',
    in_queue:  'bg-info text-dark',
    serving:   'bg-success',
    completed: 'bg-secondary',
    cancelled: 'bg-danger',
  };
  badge.className = 'badge ' + (badgeColors[status] || 'bg-secondary');
  badge.textContent = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());

  // Update UI based on status
  if (status === 'serving') {
    icon.innerHTML       = '<i class="bi bi-person-check-fill text-success" style="font-size:4rem;"></i>';
    alert.innerHTML      = '<div class="alert alert-success fw-bold fs-5"><i class="bi bi-bell-fill"></i> It\'s Your Turn! Please proceed now.</div>';
    queueNum.className   = 'queue-number text-success my-3';
    card.classList.add('serving-glow');
    header.style.background = '#198754';
    statsRow.classList.add('d-none');
    refreshTxt.classList.add('d-none');
    // Play sound
    playBeep();

  } else if (status === 'completed') {
    icon.innerHTML  = '<i class="bi bi-check-circle-fill text-secondary" style="font-size:4rem;"></i>';
    alert.innerHTML = '<div class="alert alert-secondary">Your appointment is completed. Thank you!</div>';
    statsRow.classList.add('d-none');
    refreshTxt.classList.add('d-none');
    clearInterval(window.pollInterval);

  } else if (status === 'cancelled') {
    icon.innerHTML  = '<i class="bi bi-x-circle-fill text-danger" style="font-size:4rem;"></i>';
    alert.innerHTML = '<div class="alert alert-danger">Your appointment was cancelled.</div>';
    statsRow.classList.add('d-none');
    refreshTxt.classList.add('d-none');
    clearInterval(window.pollInterval);

  } else {
    // Waiting
    icon.innerHTML = '<i class="bi bi-hourglass-split text-primary" style="font-size:4rem;"></i>';
    statsRow.classList.remove('d-none');
    card.classList.remove('serving-glow');
    header.style.background = '#0d6efd';
    queueNum.className = 'queue-number text-primary my-3';

    if (ahead === 0) {
      alert.innerHTML = '<div class="alert alert-success fw-semibold"><i class="bi bi-bell-fill"></i> You\'re next! Please be ready.</div>';
    } else if (ahead <= 3) {
      alert.innerHTML = '<div class="alert alert-warning fw-semibold"><i class="bi bi-clock"></i> Almost your turn! Stay nearby.</div>';
    } else {
      alert.innerHTML = '<div class="alert alert-info"><i class="bi bi-hourglass-split"></i> Please wait patiently.</div>';
    }
  }

  // Last updated
  const now = new Date();
  document.getElementById('last-updated').textContent =
    'Updated: ' + now.toLocaleTimeString();
}

function fetchStatus() {
  fetch(apiUrl)
    .then(r => r.json())
    .then(data => updateUI(data))
    .catch(() => {
      document.getElementById('last-updated').textContent = 'Connection error. Retrying...';
    });
}

function playBeep() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    osc.connect(ctx.destination);
    osc.frequency.value = 880;
    osc.start();
    osc.stop(ctx.currentTime + 0.3);
  } catch(e) {}
}

// Initial UI from PHP
updateUI({
  status: '<?= $appointment['status'] ?>',
  ahead:  <?= $ahead ?>
});

// Poll every 10 seconds
window.pollInterval = setInterval(fetchStatus, 10000);
</script>
</body>
</html>