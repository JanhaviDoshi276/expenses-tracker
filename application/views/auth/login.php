<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — ExpenseTracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Inter', sans-serif; }
    body { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%); min-height: 100vh; display: flex; align-items: center; }
    .login-card { border-radius: 20px; border: none; box-shadow: 0 25px 60px rgba(0,0,0,.35); }
    .brand-icon { width: 60px; height: 60px; background: linear-gradient(135deg,#6366f1,#8b5cf6); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #fff; margin: 0 auto 1rem; }
    .form-control { border-radius: 10px; border: 1.5px solid #e5e7eb; padding: .65rem 1rem; font-size: .9rem; }
    .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
    .btn-login { background: linear-gradient(135deg,#6366f1,#8b5cf6); border: none; border-radius: 10px; padding: .7rem; font-weight: 700; font-size: .95rem; letter-spacing: .02em; }
    .btn-login:hover { opacity: .92; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-sm-9 col-md-6 col-lg-5 col-xl-4">
      <div class="card login-card">
        <div class="card-body p-5">
          <div class="brand-icon"><i class="fa-solid fa-wallet"></i></div>
          <h4 class="text-center fw-700 mb-1">ExpenseTracker</h4>
          <p class="text-center text-muted mb-4" style="font-size:.875rem;">Sign in to your account</p>

          <?php if (isset($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="border-radius:10px;">
              <i class="fa-solid fa-triangle-exclamation"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success mb-4" style="border-radius:10px;">
              <?= $this->session->flashdata('success') ?>
            </div>
          <?php endif; ?>

          <?= form_open('login_post') ?>
            <div class="mb-3">
              <label class="form-label fw-600" style="font-size:.85rem;">Email Address</label>
              <input type="email" name="email" class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
                     value="<?= set_value('email') ?>" placeholder="you@example.com" autofocus>
              <div class="invalid-feedback"><?= form_error('email') ?></div>
            </div>
            <div class="mb-4">
              <label class="form-label fw-600" style="font-size:.85rem;">Password</label>
              <input type="password" name="password" class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
                     placeholder="Enter Password">
              <div class="invalid-feedback"><?= form_error('password') ?></div>
            </div>
            <button type="submit" class="btn btn-login btn-primary w-100 text-white">
              <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
            </button>
          <?= form_close() ?>

          
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
