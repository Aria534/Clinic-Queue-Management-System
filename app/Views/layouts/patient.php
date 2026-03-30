<?php
$page_title = $page_title ?? 'Patient Portal';

function render_patient_layout($page_title, $content) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?> | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    * { font-family: Arial, sans-serif !important; }
  </style>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="/Patient/user.php">
      <i class="bi bi-clipboard2-pulse fs-5"></i> QueueMed
    </a>
    <div class="d-flex align-items-center gap-2 ms-auto">
      <ul class="nav nav-pills gap-1 mb-0" id="portalTabs">
        <li class="nav-item">
          <button class="nav-link <?= ($page_title==='Book Appointment'?'active':'') ?> small" onclick="showTab('book',this)">
            <i class="bi bi-calendar-plus"></i> Book
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link <?= ($page_title==='Queue Monitor'?'active':'') ?> small" onclick="showTab('queue',this)">
            <i class="bi bi-ticket-perforated"></i> Queue
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link <?= ($page_title==='Check Ticket'?'active':'') ?> small" onclick="showTab('check',this)">
            <i class="bi bi-search"></i> Check
          </button>
        </li>
      </ul>
      <a href="/User/auth.php" class="btn btn-sm btn-outline-secondary ms-2">
        <i class="bi bi-shield-lock"></i> Admin
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
  <?= $content ?>
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
</script>
</body>
</html>
<?php } ?>