<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /User/auth.php');
    exit;
}
$admin_name     = $_SESSION['admin_name'] ?? 'Admin User';
$admin_role     = $_SESSION['admin_role'] ?? 'Administrator';
$admin_initials = strtoupper(substr($admin_name, 0, 1) . (strpos($admin_name, ' ') !== false ? substr($admin_name, strpos($admin_name, ' ') + 1, 1) : ''));
$page_title     = $page_title  ?? 'Dashboard';
$active_page    = $active_page ?? 'dashboard';

$nav_items = [
  ['id'=>'dashboard',    'label'=>'Dashboard',     'icon'=>'bi-grid-1x2-fill',    'href'=>'/admin/dashboard.php'],
  ['id'=>'appointments', 'label'=>'Appointments',  'icon'=>'bi-calendar-check',   'href'=>'/admin/appointments.php'],
  ['id'=>'queue',        'label'=>'Queue Monitor', 'icon'=>'bi-ticket-perforated','href'=>'/admin/queue.php'],
  ['id'=>'patients',     'label'=>'Patients',      'icon'=>'bi-people',           'href'=>'/admin/patients.php'],
  ['id'=>'staff',        'label'=>'Staff',         'icon'=>'bi-person-badge',     'href'=>'/admin/staff.php'],
  ['id'=>'reports',      'label'=>'Reports',       'icon'=>'bi-bar-chart-line',   'href'=>'/admin/reports.php'],
  ['id'=>'notifications','label'=>'Notifications', 'icon'=>'bi-bell',             'href'=>'/admin/notifications.php'],
  ['id'=>'settings',     'label'=>'Settings',      'icon'=>'bi-gear',             'href'=>'/admin/settings.php'],
];

function render_layout($page_title, $active_page, $admin_name, $admin_role, $admin_initials, $nav_items, $content) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?> | QueueMed</title>
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
    <?php foreach (array_slice($nav_items, 0, 4) as $item): ?>
    <a href="<?= $item['href'] ?>" class="nav-link text-white d-flex align-items-center gap-2 py-2 px-2 mb-1 <?= $active_page===$item['id']?'active fw-semibold':'opacity-75' ?>">
      <i class="bi <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
    </a>
    <?php endforeach; ?>
    <div class="text-uppercase small opacity-50 px-2 mt-3 mb-1" style="font-size:.65rem;letter-spacing:1px;">Manage</div>
    <?php foreach (array_slice($nav_items, 4) as $item): ?>
    <a href="<?= $item['href'] ?>" class="nav-link text-white d-flex align-items-center gap-2 py-2 px-2 mb-1 <?= $active_page===$item['id']?'active fw-semibold':'opacity-75' ?>">
      <i class="bi <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
      <?php if ($item['id']==='notifications'): ?><span class="badge bg-danger ms-auto">3</span><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-3 border-top border-white border-opacity-25">
    <div class="d-flex align-items-center gap-2">
      <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:34px;height:34px;font-size:.8rem;">
        <?= htmlspecialchars($admin_initials) ?>
      </div>
      <div class="flex-grow-1 overflow-hidden">
        <div class="small fw-semibold text-truncate"><?= htmlspecialchars($admin_name) ?></div>
        <div class="small opacity-75 text-truncate"><?= htmlspecialchars($admin_role) ?></div>
      </div>
      <a href="/logout.php" class="text-white opacity-75" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
    </div>
  </div>
</div>

<!-- TOPBAR -->
<nav class="navbar topbar bg-white border-bottom shadow-sm px-3 position-fixed d-flex align-items-center justify-content-between w-100">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <ol class="breadcrumb mb-0 small">
      <li class="breadcrumb-item"><i class="bi bi-house"></i></li>
      <li class="breadcrumb-item active"><?= htmlspecialchars($page_title) ?></li>
    </ol>
  </div>
  <div class="d-flex align-items-center gap-2">
    <span class="badge bg-success d-none d-md-inline-flex align-items-center gap-1">
      <i class="bi bi-circle-fill" style="font-size:.4rem;"></i> Now Serving: A007
    </span>
    <button class="btn btn-sm btn-outline-secondary position-relative">
      <i class="bi bi-bell"></i>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.55rem;">3</span>
    </button>
    <a href="/admin/appointments.php?action=new" class="btn btn-sm btn-primary">
      <i class="bi bi-plus-lg me-1"></i> New Appointment
    </a>
  </div>
</nav>

<!-- PAGE CONTENT -->
<div class="main-content">
  <div class="p-4">
    <?= $content ?>
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
<?php } ?>