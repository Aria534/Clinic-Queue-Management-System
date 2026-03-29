<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Patient Portal | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="<?= base_url('patient/dashboard') ?>">
      <i class="bi bi-clipboard2-pulse fs-5"></i> QueueMed
    </a>
    <div class="d-flex align-items-center gap-2 ms-auto">
      <ul class="nav nav-pills gap-1 mb-0" id="portalTabs">
        <li class="nav-item">
          <button class="nav-link active small" onclick="showTab('book',this)">
            <i class="bi bi-calendar-plus"></i> Book
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link small" onclick="showTab('queue',this)">
            <i class="bi bi-ticket-perforated"></i> Queue
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link small" onclick="showTab('check',this)">
            <i class="bi bi-search"></i> Check
          </button>
        </li>
      </ul>
      <span class="ms-3 small text-muted">Hi, <strong><?= esc(session()->get('name')) ?></strong></span>
      <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger ms-2">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</nav>

<!-- LIVE BANNER -->
<div class="bg-primary text-white py-2">
  <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-danger">LIVE</span>
      <span class="small">Now Serving: <strong class="font-monospace">A007</strong> — Window 2, Dr. Santos</span>
    </div>
    <div class="d-flex gap-3 small">
      <span><i class="bi bi-hourglass-split text-warning"></i> 4 Waiting</span>
      <span><i class="bi bi-clock text-white-50"></i> ~18 min avg wait</span>
    </div>
  </div>
</div>

<!-- PAGE CONTENT -->
<div class="container py-4">

  <!-- STATS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-warning"><?= $stats['pending'] ?></div>
        <div class="small text-muted">Pending</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-primary"><?= $stats['confirmed'] ?></div>
        <div class="small text-muted">Confirmed</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-success"><?= $stats['completed'] ?></div>
        <div class="small text-muted">Completed</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-secondary"><?= count($appointments) ?></div>
        <div class="small text-muted">Total</div>
      </div>
    </div>
  </div>

  <!-- TAB: BOOK -->
  <div class="tab-pane-q" id="tab-book">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-calendar-plus me-2 text-primary"></i>Book an Appointment</h5>
            <small class="text-muted">Fill in your details to reserve a slot.</small>
          </div>
          <div class="card-body">

            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-circle-fill"></i> <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger py-2">
              <ul class="mb-0 small">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('patient/book') ?>">
              <?= csrf_field() ?>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Full Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control"
                      placeholder="Maria Santos"
                      value="<?= esc(old('name', session()->get('name'))) ?>" required/>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Contact Number</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="tel" name="contact" class="form-control"
                      placeholder="09XXXXXXXXX"
                      value="<?= esc(old('contact')) ?>" required/>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Service / Concern</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-clipboard2-pulse"></i></span>
                    <select name="service_id" class="form-select" required>
                      <option value="" disabled selected>Select a service</option>
                      <?php foreach ($services ?? [] as $s): ?>
                      <option value="<?= $s['id'] ?>" <?= old('service_id') == $s['id'] ? 'selected' : '' ?>>
                        <?= esc($s['name']) ?> (<?= $s['duration'] ?> mins)
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Preferred Date</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    <input type="date" name="appt_date" class="form-control"
                      min="<?= date('Y-m-d') ?>"
                      value="<?= esc(old('appt_date')) ?>" required/>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Preferred Time</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-clock"></i></span>
                    <input type="time" name="appt_time" class="form-control"
                      min="08:00" max="17:00"
                      value="<?= esc(old('appt_time')) ?>" required/>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-calendar-check me-1"></i> Confirm Appointment
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB: QUEUE -->
  <div class="tab-pane-q d-none" id="tab-queue">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-ticket-perforated me-2 text-primary"></i>My Appointments</span>
          </div>
          <?php if (empty($appointments)): ?>
          <div class="card-body text-center text-muted py-4">
            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
            No appointments yet. Book one now!
          </div>
          <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($appointments as $appt): ?>
            <li class="list-group-item d-flex align-items-center gap-3 py-3">
              <div class="text-center px-2">
                <div class="fs-4 fw-bold text-primary font-monospace">#<?= $appt['queue_number'] ?></div>
                <small class="text-muted">Queue</small>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold small"><?= esc($appt['service_name'] ?? '') ?></div>
                <div class="text-muted" style="font-size:.78rem">
                  <?= date('M d, Y', strtotime($appt['appointment_date'])) ?>
                  — <?= date('h:i A', strtotime($appt['appointment_time'])) ?>
                </div>
              </div>
              <?php
                $status = $appt['status'] ?? 'pending';
                $badge = match($status) {
                  'confirmed' => 'bg-primary',
                  'in_queue'  => 'bg-info text-dark',
                  'serving'   => 'bg-success',
                  'completed' => 'bg-secondary',
                  'cancelled' => 'bg-danger',
                  default     => 'bg-warning text-dark',
                };
              ?>
              <div class="d-flex flex-column align-items-end gap-1">
                <span class="badge <?= $badge ?>"><?= ucfirst(str_replace('_',' ',$status)) ?></span>
                <?php if (!in_array($status, ['completed','cancelled','serving'])): ?>
                <a href="<?= base_url('patient/queue/'.$appt['id']) ?>" class="btn btn-outline-primary btn-sm" style="font-size:.7rem">
                  <i class="bi bi-ticket"></i> Track
                </a>
                <a href="<?= base_url('patient/appointments/cancel/'.$appt['id']) ?>"
                   class="btn btn-outline-danger btn-sm"
                   style="font-size:.7rem"
                   onclick="return confirm('Cancel this appointment?')">
                  <i class="bi bi-x-circle"></i> Cancel
                </a>
                <?php endif; ?>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB: CHECK -->
  <div class="tab-pane-q d-none" id="tab-check">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 text-center">
          <div class="card-body py-4">
            <h5 class="fw-semibold mb-1">Check Your Queue</h5>
            <p class="text-muted small mb-3">Enter your queue number to see your position.</p>
            <div class="input-group mb-3">
              <input type="text" id="checkInput" class="form-control text-center font-monospace fw-bold text-uppercase"
                placeholder="e.g. A008" maxlength="5" style="letter-spacing:2px;font-size:1.1rem"/>
              <button class="btn btn-primary" onclick="checkTicket()">
                <i class="bi bi-search"></i>
              </button>
            </div>
            <div id="checkResult"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- FOOTER -->
<footer class="border-top bg-white text-center text-muted small py-3 mt-4">
  &copy; <?= date('Y') ?> QueueMed — Online Appointment & Queue Management System
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showTab(tab, btn) {
  document.querySelectorAll('.tab-pane-q').forEach(p => p.classList.add('d-none'));
  document.querySelectorAll('#portalTabs .nav-link').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + tab)?.classList.remove('d-none');
  btn.classList.add('active');
}

function checkTicket() {
  const input  = document.getElementById('checkInput').value.trim().toUpperCase();
  const result = document.getElementById('checkResult');
  result.innerHTML = `<div class="alert alert-info small">Queue number <strong>${input}</strong> — feature coming soon.</div>`;
}
</script>
</body>
</html>