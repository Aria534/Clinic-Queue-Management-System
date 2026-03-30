<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    * { font-family: Arial, sans-serif !important; }
  </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card shadow" style="width:100%;max-width:440px;">
  <div class="card-body p-4">

    <!-- BRAND -->
    <div class="text-center mb-4">
      <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;font-size:1.5rem;">
        <i class="bi bi-clipboard2-pulse"></i>
      </div>
      <h4 class="fw-bold mb-0">QueueMed</h4>
      <small class="text-muted">Appointment & Queue Management System</small>
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success d-flex align-items-center gap-2 py-2">
      <i class="bi bi-check-circle-fill"></i>
      <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>

    <!-- ERROR MESSAGE -->
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
      <i class="bi bi-exclamation-circle-fill"></i>
      <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- VALIDATION ERRORS -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger py-2">
      <ul class="mb-0 small">
        <?php foreach (session()->getFlashdata('errors') as $err): ?>
        <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <!-- LOGIN FORM -->
    <form method="POST" action="<?= base_url('login') ?>">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label fw-semibold small">Email Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" class="form-control"
            placeholder="Enter your email"
            value="<?= old('email') ?>" required autofocus/>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold small">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" id="password" class="form-control"
            placeholder="Enter your password" required/>
          <button type="button" class="btn btn-outline-secondary" id="togglePw">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check mb-0">
          <input class="form-check-input" type="checkbox" id="remember"/>
          <label class="form-check-label small" for="remember">Remember me</label>
        </div>
        <a href="#" class="small text-primary">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login
      </button>
    </form>

  </div>
  <div class="card-footer text-center text-muted small py-2">
    &copy; <?= date('Y') ?> QueueMed. All rights reserved.
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const togglePw = document.getElementById('togglePw');
  const pwInput  = document.getElementById('password');
  const eyeIcon  = document.getElementById('eyeIcon');
  togglePw?.addEventListener('click', () => {
    const hidden = pwInput.type === 'password';
    pwInput.type = hidden ? 'text' : 'password';
    eyeIcon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
  });
</script>
</body>
</html>