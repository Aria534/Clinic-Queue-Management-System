<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book a Queue | QueueMed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
</head>
<body class="bg-light">

<nav class="navbar navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <span class="navbar-brand fw-bold text-primary">
      <i class="bi bi-clipboard2-pulse"></i> QueueMed
    </span>
  </div>
</nav>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">

      <div class="card border-0 shadow" style="border-radius:1.5rem;">
        <div class="card-body p-4">

          <h4 class="fw-bold mb-1">Get Your Queue Number</h4>
          <p class="text-muted small mb-4">No login required. Fill in your details below.</p>

          <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <form method="POST" action="<?= base_url('book') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Full Name</label>
              <input type="text" name="patient_name" class="form-control"
                     placeholder="e.g. Juan dela Cruz" required
                     value="<?= old('patient_name') ?>"/>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Email Address</label>
              <input type="email" name="patient_email" class="form-control"
                     placeholder="you@email.com" required
                     value="<?= old('patient_email') ?>"/>
              <div class="form-text">Your queue ticket will be sent here.</div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Service</label>
              <select name="service_id" class="form-select" required>
                <option value="">Select a service...</option>
                <?php foreach ($services as $svc): ?>
                <option value="<?= $svc['id'] ?>" <?= old('service_id')==$svc['id']?'selected':'' ?>>
                  <?= esc($svc['name']) ?> (<?= $svc['duration'] ?> mins)
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold small">Notes <span class="text-muted fw-normal">(optional)</span></label>
              <textarea name="notes" class="form-control" rows="2"
                        placeholder="Any concerns or additional info..."><?= old('notes') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
              <i class="bi bi-ticket-perforated me-1"></i> Get Queue Number
            </button>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>