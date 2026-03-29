<?php
$page    = $page ?? 'dashboard';
$name    = session()->get('name') ?? 'Admin';
$initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr($name, strpos($name, ' ') + 1, 1) : ''));

$nav_items = [
  ['id'=>'dashboard',    'label'=>'Dashboard',     'icon'=>'bi-grid-1x2-fill',    'href'=> base_url('admin/dashboard')],
  ['id'=>'appointments', 'label'=>'Appointments',  'icon'=>'bi-calendar-check',   'href'=> base_url('admin/appointments')],
  ['id'=>'queue',        'label'=>'Queue Monitor', 'icon'=>'bi-ticket-perforated','href'=> base_url('admin/queue')],
  ['id'=>'users',        'label'=>'Patients',      'icon'=>'bi-people',           'href'=> base_url('admin/users')],
  ['id'=>'services',     'label'=>'Services',      'icon'=>'bi-clipboard2-pulse', 'href'=> base_url('admin/services')],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= ucfirst($page) ?> | QueueMed Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    .sidebar{width:240px;min-height:100vh;position:fixed;top:0;left:0;z-index:200;}
    .main-content{margin-left:240px;padding-top:60px;}
    .topbar{left:240px;right:0;height:60px;z-index:100;}
    .nav-link.active{background-color:rgba(255,255,255,0.15);border-radius:6px;}
    @media(max-width:991px){
      .sidebar{transform:translateX(-100%);transition:transform .3s;}
      .sidebar.show{transform:translateX(0);}
      .main-content{margin-left:0;}
      .topbar{left:0;}
    }
  </style>
</head>
<body class="bg-light">

<!-- SIDEBAR -->
<div class="sidebar bg-primary text-white d-flex flex-column p-0 shadow">
  <div class="px-3 py-3 border-bottom border-white border-opacity-25">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-clipboard2-pulse fs-4"></i>
      <div>
        <div class="fw-bold">QueueMed</div>
        <div class="small opacity-75">Admin Panel</div>
      </div>
    </div>
  </div>
  <nav class="flex-grow-1 py-2 px-2 overflow-auto">
    <div class="text-uppercase small opacity-50 px-2 mt-2 mb-1" style="font-size:.65rem;letter-spacing:1px;">Main</div>
    <?php foreach ($nav_items as $item): ?>
    <a href="<?= $item['href'] ?>" class="nav-link text-white d-flex align-items-center gap-2 py-2 px-2 mb-1 <?= $page===$item['id']?'active fw-semibold':'opacity-75' ?>">
      <i class="bi <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-3 border-top border-white border-opacity-25">
    <div class="d-flex align-items-center gap-2">
      <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:34px;height:34px;font-size:.8rem;">
        <?= esc($initials) ?>
      </div>
      <div class="flex-grow-1 overflow-hidden">
        <div class="small fw-semibold text-truncate"><?= esc($name) ?></div>
        <div class="small opacity-75">Administrator</div>
      </div>
      <a href="<?= base_url('logout') ?>" class="text-white opacity-75" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
    </div>
  </div>
</div>

<!-- TOPBAR -->
<nav class="navbar topbar bg-white border-bottom shadow-sm px-3 position-fixed d-flex align-items-center justify-content-between w-100">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <ol class="breadcrumb mb-0 small">
      <li class="breadcrumb-item"><i class="bi bi-house"></i></li>
      <li class="breadcrumb-item active"><?= ucfirst($page) ?></li>
    </ol>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="<?= base_url('admin/appointments') ?>" class="btn btn-sm btn-primary">
      <i class="bi bi-plus-lg me-1"></i> New Appointment
    </a>
  </div>
</nav>

<!-- PAGE CONTENT -->
<div class="main-content">
  <div class="p-4">

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

    <!-- ==================== DASHBOARD ==================== -->
    <?php if ($page === 'dashboard'): ?>

    <div class="alert alert-primary d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <div class="small text-uppercase fw-semibold opacity-75">🔴 Now Serving</div>
        <div class="display-6 fw-bold">A007</div>
        <div class="small opacity-75">Window 2 — Dr. Santos</div>
      </div>
      <div class="d-flex gap-3 flex-wrap">
        <div class="text-center">
          <div class="fs-4 fw-bold text-warning"><?= $pending_count ?? 0 ?></div>
          <div class="small">Waiting</div>
        </div>
        <div class="text-center">
          <div class="fs-4 fw-bold text-success"><?= $serving_count ?? 0 ?></div>
          <div class="small">Serving</div>
        </div>
        <div class="text-center">
          <div class="fs-4 fw-bold"><?= $completed_today ?? 0 ?></div>
          <div class="small">Completed</div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-primary bg-opacity-10 text-primary p-2 fs-5 mb-2 d-inline-block"><i class="bi bi-calendar2-week"></i></span>
            <div class="fs-3 fw-bold text-primary"><?= $total_today ?? 0 ?></div>
            <div class="small text-muted">Total Today</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-warning bg-opacity-10 text-warning p-2 fs-5 mb-2 d-inline-block"><i class="bi bi-hourglass-split"></i></span>
            <div class="fs-3 fw-bold text-warning"><?= $pending_count ?? 0 ?></div>
            <div class="small text-muted">Pending</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-success bg-opacity-10 text-success p-2 fs-5 mb-2 d-inline-block"><i class="bi bi-check-circle"></i></span>
            <div class="fs-3 fw-bold text-success"><?= $completed_today ?? 0 ?></div>
            <div class="small text-muted">Completed Today</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-info bg-opacity-10 text-info p-2 fs-5 mb-2 d-inline-block"><i class="bi bi-people"></i></span>
            <div class="fs-3 fw-bold text-info"><?= $total_patients ?? 0 ?></div>
            <div class="small text-muted">Total Patients</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Recent Appointments</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>Queue #</th><th>Patient</th><th>Service</th><th>Date</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($recent_appts ?? [] as $a): ?>
            <tr>
              <td><span class="fw-bold text-primary font-monospace"><?= esc($a['queue_number'] ?? '-') ?></span></td>
              <td><?= esc($a['patient_name'] ?? $a['name'] ?? '-') ?></td>
              <td class="text-muted small"><?= esc($a['service'] ?? '-') ?></td>
              <td class="text-muted small"><?= esc($a['appointment_date'] ?? '-') ?></td>
              <td>
                <?php
                  $s = $a['status'] ?? 'pending';
                  $b = match($s) { 'confirmed'=>'primary','completed'=>'success','cancelled'=>'danger','serving'=>'info', default=>'warning' };
                ?>
                <span class="badge bg-<?= $b ?>"><?= ucfirst($s) ?></span>
              </td>
              <td>
                <form method="POST" action="<?= base_url('admin/appointments/update') ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= $a['id'] ?>">
                  <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                    <?php foreach (['pending','confirmed','serving','completed','cancelled'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $s===$opt?'selected':'' ?>><?= ucfirst($opt) ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== APPOINTMENTS ==================== -->
    <?php elseif ($page === 'appointments'): ?>

    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">All Appointments</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>Queue #</th><th>Patient</th><th>Service</th><th>Date</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($appointments ?? [] as $a): ?>
            <tr>
              <td><span class="fw-bold text-primary font-monospace"><?= esc($a['queue_number'] ?? '-') ?></span></td>
              <td><?= esc($a['patient_name'] ?? $a['name'] ?? '-') ?></td>
              <td class="text-muted small"><?= esc($a['service'] ?? '-') ?></td>
              <td class="text-muted small"><?= esc($a['appointment_date'] ?? '-') ?></td>
              <td>
                <?php
                  $s = $a['status'] ?? 'pending';
                  $b = match($s) { 'confirmed'=>'primary','completed'=>'success','cancelled'=>'danger','serving'=>'info', default=>'warning' };
                ?>
                <span class="badge bg-<?= $b ?>"><?= ucfirst($s) ?></span>
              </td>
              <td>
                <form method="POST" action="<?= base_url('admin/appointments/update') ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= $a['id'] ?>">
                  <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                    <?php foreach (['pending','confirmed','serving','completed','cancelled'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $s===$opt?'selected':'' ?>><?= ucfirst($opt) ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== QUEUE ==================== -->
    <?php elseif ($page === 'queue'): ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0">Queue Monitor</h5>
      <form method="POST" action="<?= base_url('admin/queue/next') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-skip-forward me-1"></i> Call Next
        </button>
      </form>
    </div>

    <div class="card shadow-sm border-0">
      <ul class="list-group list-group-flush">
        <?php foreach ($queue ?? [] as $i => $q): ?>
        <li class="list-group-item d-flex align-items-center gap-3 py-3">
          <span class="fw-bold font-monospace text-primary"><?= esc($q['queue_number'] ?? '-') ?></span>
          <div class="flex-grow-1">
            <div class="fw-semibold small"><?= esc($q['patient_name'] ?? '-') ?></div>
            <div class="text-muted" style="font-size:.78rem"><?= esc($q['service'] ?? '-') ?></div>
          </div>
          <?php $s = $q['status'] ?? 'waiting'; ?>
          <?php if ($s === 'serving'): ?>
            <span class="badge bg-success">Serving</span>
          <?php else: ?>
            <span class="badge bg-warning text-dark">Waiting</span>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
        <?php if (empty($queue)): ?>
        <li class="list-group-item text-center text-muted py-4">No queue for today.</li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- ==================== USERS ==================== -->
    <?php elseif ($page === 'users'): ?>

    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Patients</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users ?? [] as $i => $u): ?>
            <tr>
              <td class="text-muted small"><?= $i+1 ?></td>
              <td><?= esc($u['name']) ?></td>
              <td class="text-muted small"><?= esc($u['email']) ?></td>
              <td class="text-muted small"><?= esc($u['phone'] ?? '-') ?></td>
              <td class="text-muted small"><?= esc($u['created_at'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== SERVICES ==================== -->
    <?php elseif ($page === 'services'): ?>

    <div class="row g-3">
      <div class="col-12 col-md-5">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-semibold">Add Service</div>
          <div class="card-body">
            <form method="POST" action="<?= base_url('admin/services/store') ?>">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Service Name</label>
                <input type="text" name="name" class="form-control" required/>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Duration (mins)</label>
                <input type="number" name="duration" class="form-control" value="30" required/>
              </div>
              <button type="submit" class="btn btn-primary w-100">Add Service</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-7">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white fw-semibold">All Services</div>
          <ul class="list-group list-group-flush">
            <?php foreach ($services ?? [] as $svc): ?>
            <li class="list-group-item d-flex align-items-center justify-content-between py-3">
              <div>
                <div class="fw-semibold small"><?= esc($svc['name']) ?></div>
                <div class="text-muted" style="font-size:.78rem"><?= esc($svc['duration'] ?? '') ?> mins</div>
              </div>
              <form method="POST" action="<?= base_url('admin/services/toggle/' . $svc['id']) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm <?= $svc['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?>">
                  <?= $svc['is_active'] ? 'Active' : 'Inactive' ?>
                </button>
              </form>
            </li>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
            <li class="list-group-item text-center text-muted py-3">No services yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  toggle?.addEventListener('click', () => sidebar.classList.toggle('show'));
  document.addEventListener('click', e => {
    if (!sidebar.contains(e.target) && !toggle?.contains(e.target))
      sidebar.classList.remove('show');
  });
</script>
</body>
</html>
