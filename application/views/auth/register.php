<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — ExpenseTracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4338ca 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:2rem 0; }
    .auth-card { background:#fff; border-radius:20px; padding:2.5rem 2rem; width:100%; max-width:440px; box-shadow:0 25px 60px rgba(0,0,0,.25); }
    .auth-card .brand { text-align:center; margin-bottom:1.75rem; }
    .auth-card .brand .icon { width:60px; height:60px; background:linear-gradient(135deg,#6366f1,#8b5cf6); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto .75rem; }
    .auth-card .brand .icon i { color:#fff; font-size:1.5rem; }
    .auth-card .brand h4 { font-weight:700; color:#111827; margin:0; }
    .auth-card .brand p { color:#6b7280; font-size:.85rem; margin:.25rem 0 0; }
    .form-label { font-size:.83rem; font-weight:600; color:#374151; }
    .form-control { border-radius:8px; border-color:#d1d5db; font-size:.875rem; padding:.6rem .9rem; }
    .form-control:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.15); }
    .btn-auth { background:linear-gradient(135deg,#6366f1,#8b5cf6); border:none; border-radius:10px; font-weight:600; padding:.7rem; font-size:.95rem; width:100%; color:#fff; transition:opacity .2s; }
    .btn-auth:hover { opacity:.9; color:#fff; }
    .input-group-text { border-color:#d1d5db; background:#f9fafb; border-radius:8px 0 0 8px; }
  </style>
</head>
<body>
<div class="auth-card">
  <div class="brand">
    <div class="icon"><i class="fa-solid fa-wallet"></i></div>
    <h4>Create Account</h4>
    <p>Start tracking your expenses today</p>
  </div>

  <?= form_open('register_post') ?>
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-user text-muted"></i></span>
        <input type="text" name="name" value="<?= set_value('name') ?>"
               class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
               placeholder="John Doe" required>
        <div class="invalid-feedback"><?= form_error('name') ?></div>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-regular fa-envelope text-muted"></i></span>
        <input type="email" name="email" value="<?= set_value('email') ?>"
               class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
               placeholder="you@example.com" required>
        <div class="invalid-feedback"><?= form_error('email') ?></div>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-lock text-muted"></i></span>
        <input type="password" name="password"
               class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
               placeholder="Min. 6 characters" required>
        <div class="invalid-feedback"><?= form_error('password') ?></div>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label">Confirm Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-lock text-muted"></i></span>
        <input type="password" name="confirm_password"
               class="form-control <?= form_error('confirm_password') ? 'is-invalid' : '' ?>"
               placeholder="Repeat password" required>
        <div class="invalid-feedback"><?= form_error('confirm_password') ?></div>
      </div>
    </div>
    <button type="submit" class="btn-auth">
      <i class="fa-solid fa-user-plus me-2"></i>Create Account
    </button>
  <?= form_close() ?>

  <p class="text-center mt-3 mb-0" style="font-size:.85rem; color:#6b7280;">
    Already have an account?
    <a href="<?= site_url('login') ?>" class="fw-600 text-decoration-none" style="color:#6366f1;">Sign in</a>
  </p>
</div>
</body>
</html>
