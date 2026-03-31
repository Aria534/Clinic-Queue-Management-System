<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Now Serving | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body { background: #f0f4f8; }
    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
    .now-serving-banner {
      background: linear-gradient(135deg, #dbeafe, #eff6ff);
      border-radius: 1.5rem;
      border: 2px solid #bfdbfe;
    }
    .queue-number-big {
      font-size: 8rem;
      font-weight: 900;
      color: #1e3a5f;
      line-height: 1;
    }
    .stat-card {
      border-radius: 1rem;
      border: none;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
  </style>
</head>
<body>

<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="<?= base_url('/') ?>">
      <i class="bi bi-clipboard2-pulse"></i> QueueMed
    </a>
    <span class="small text-muted">
      <i class="bi bi-clock-history pulse"></i>
      <span id="last-updated">Connecting...</span>
    </span>
  </div>
</nav>

<div class="container py-4">

  <!-- NOW SERVING BANNER -->
  <div class="now-serving-banner p-4 mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge bg-danger pulse">● LIVE</span>
      <span class="fw-semibold text-muted small text-uppercase letter-spacing-1">Now Serving</span>
    </div>
    <div class="row align-items-center">
      <div class="col">
        <div class="queue-number-big" id="serving-number">
          <?php if ($serving): ?>
            A<?= str_pad($serving['queue_number'], 3, '0', STR_PAD_LEFT) ?>
          <?php else: ?>
            <span class="text-muted fs-1">— No Active Queue —</span>
          <?php endif; ?>
        </div>
        <div class="text-muted mt-1" id="serving-name">
          <?= $serving ? esc($serving['patient_name']) : '' ?>
        </div>
      </div>
      <div class="col-auto text-end">
        <div class="d-flex gap-4">
          <div class="text-center">
            <div class="fs-3 fw-bold text-warning" id="waiting-count"><?= $waiting ?></div>
            <small class="text-muted">Waiting</small>
          </div>
          <div class="text-center">
            <div class="fs-3 fw-bold text-success" id="serving-count"><?= $serving ? 1 : 0 ?></div>
            <small class="text-muted">Serving</small>
          </div>
          <div class="text-center">
            <div class="fs-3 fw-bold text-secondary" id="completed-count"><?= $completed ?></div>
            <small class="text-muted">Completed</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- QUEUE LIST -->
  <div class="card border-0 shadow-sm" style="border-radius:1.5rem;">
    <div class="card-header bg-white py-3" style="border-radius:1.5rem 1.5rem 0 0;">
      <h5 class="mb-0 fw-semibold">
        <i class="bi bi-list-ol text-primary me-2"></i>Today's Queue
      </h5>
    </div>
    <div class="card-body p-0">
      <div id="queue-list">
        <?php if (empty($queue)): ?>
        <div class="text-center text-muted py-5">
          <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
          No queue for today yet.
        </div>
        <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($queue as $q): ?>
          <?php
            $status = $q['status'];
            $badge = match($status) {
              'serving'   => 'bg-success',
              'confirmed' => 'bg-primary',
              'in_queue'  => 'bg-info text-dark',
              'completed' => 'bg-secondary',
              'cancelled' => 'bg-danger',
              default     => 'bg-warning text-dark',
            };
          ?>
          <li class="list-group-item d-flex align-items-center gap-3 py-3
            <?= $status === 'serving' ? 'bg-success bg-opacity-10' : '' ?>">
            <div class="text-center" style="width:60px;">
              <div class="fs-4 fw-bold text-primary font-monospace">
                A<?= str_pad($q['queue_number'], 3, '0', STR_PAD_LEFT) ?>
              </div>
            </div>
            <div class="flex-grow-1">
              <div class="fw-semibold"><?= esc($q['patient_name']) ?></div>
              <div class="small text-muted"><?= esc($q['service_name'] ?? '—') ?></div>
            </div>
            <span class="badge <?= $badge ?>">
              <?= ucfirst(str_replace('_', ' ', $status)) ?>
            </span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <p class="text-center text-muted small mt-3">
    <i class="bi bi-arrow-repeat pulse"></i> Auto-refreshes every 10 seconds
  </p>

</div>

<script>
function fetchLive() {
  fetch('<?= base_url('api/queue-live') ?>')
    .then(r => r.json())
    .then(data => {
      // Update serving banner
      const servingNum = document.getElementById('serving-number');
      const servingName = document.getElementById('serving-name');
      if (data.serving) {
        const padded = 'A' + String(data.serving.queue_number).padStart(3, '0');
        servingNum.textContent  = padded;
        servingName.textContent = data.serving.patient_name;
      } else {
        servingNum.innerHTML    = '<span class="text-muted fs-1">— No Active Queue —</span>';
        servingName.textContent = '';
      }

      // Update counts
      document.getElementById('waiting-count').textContent   = data.waiting ?? 0;
      document.getElementById('serving-count').textContent   = data.serving ? 1 : 0;
      document.getElementById('completed-count').textContent = data.completed ?? 0;

      // Update last updated time
      document.getElementById('last-updated').textContent =
        'Updated: ' + new Date().toLocaleTimeString();
    })
    .catch(() => {
      document.getElementById('last-updated').textContent = 'Connection error. Retrying...';
    });
}

setInterval(fetchLive, 10000);
</script>

</body>
</html>