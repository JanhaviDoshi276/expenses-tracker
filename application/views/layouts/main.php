<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? htmlspecialchars($title) . ' — ExpenseTracker' : 'ExpenseTracker' ?></title>
  <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>">
  <meta name="csrf-name"  content="<?= $this->security->get_csrf_token_name() ?>">
  <meta name="base-url"   content="<?= base_url() ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --primary:#6366f1; --primary-dark:#4f46e5; --sidebar-w:260px; --sidebar-bg:#1e1b4b; --sidebar-text:#c7d2fe; --sidebar-active:#6366f1; }
    * { font-family:'Inter',sans-serif; }
    body { background:#f8f9fc; min-height:100vh; }
    #sidebar { width:var(--sidebar-w); min-height:100vh; background:var(--sidebar-bg); position:fixed; top:0; left:0; z-index:1000; transition:transform .25s ease; overflow-y:auto; }
    #sidebar .brand { padding:1.25rem 1.25rem .75rem; border-bottom:1px solid rgba(255,255,255,.08); }
    #sidebar .brand h5 { color:#fff; font-weight:700; font-size:1.1rem; margin:0; }
    #sidebar .brand span { color:var(--primary); }
    #sidebar .nav-link { color:var(--sidebar-text); padding:.6rem 1.25rem; border-radius:8px; margin:1px 10px; font-size:.845rem; font-weight:500; transition:background .2s,color .2s; display:flex; align-items:center; gap:.6rem; }
    #sidebar .nav-link:hover, #sidebar .nav-link.active { background:var(--sidebar-active); color:#fff; }
    #sidebar .nav-link i { width:18px; text-align:center; }
    .sidebar-section { padding:.6rem 1.25rem .2rem; font-size:.67rem; color:#6366f1; text-transform:uppercase; letter-spacing:.08em; font-weight:700; }
    #main-content { margin-left:var(--sidebar-w); min-height:100vh; display:flex; flex-direction:column; }
    #topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:.7rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:999; }
    #topbar .page-title { font-weight:600; font-size:.95rem; color:#111827; margin:0; }
    .stat-card { border:none; border-radius:14px; padding:1.3rem 1.4rem; color:#fff; position:relative; overflow:hidden; }
    .stat-card .icon { width:46px; height:46px; border-radius:12px; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:1.25rem; }
    .stat-card .label { font-size:.76rem; opacity:.85; text-transform:uppercase; letter-spacing:.05em; }
    .stat-card .value { font-size:1.5rem; font-weight:700; margin-top:.15rem; }
    .bg-violet  { background:linear-gradient(135deg,#6366f1,#8b5cf6); }
    .bg-emerald { background:linear-gradient(135deg,#10b981,#059669); }
    .bg-amber   { background:linear-gradient(135deg,#f59e0b,#d97706); }
    .bg-rose    { background:linear-gradient(135deg,#f43f5e,#e11d48); }
    .bg-cyan    { background:linear-gradient(135deg,#06b6d4,#0891b2); }
    .table-card { border-radius:14px; overflow:hidden; border:none; }
    .table-card .table { margin:0; }
    .table-card .table th { background:#f9fafb; font-size:.76rem; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; font-weight:600; border-top:none; }
    .badge-cat { font-size:.71rem; padding:.3em .7em; border-radius:20px; }
    .form-label { font-size:.82rem; font-weight:600; color:#374151; }
    .form-control, .form-select { border-radius:8px; border-color:#d1d5db; font-size:.875rem; padding:.52rem .82rem; }
    .form-control:focus, .form-select:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(99,102,241,.15); }
    .btn-primary { background:var(--primary); border-color:var(--primary); border-radius:8px; font-weight:600; }
    .btn-primary:hover { background:var(--primary-dark); border-color:var(--primary-dark); }
    .fw-600 { font-weight:600!important; } .fw-700 { font-weight:700!important; }
    .role-badge-superadmin { background:#fef3c7; color:#92400e; }
    .role-badge-admin { background:#ede9fe; color:#5b21b6; }
    .role-badge-user { background:#f0fdf4; color:#166534; }
    @media (max-width:768px) { #sidebar { transform:translateX(-100%); } #sidebar.open { transform:translateX(0); } #main-content { margin-left:0; } }
    .toast-container { z-index:9999; }
  </style>
</head>
<body>
<?php
  $uid  = $this->session->userdata('user_id');
  $role = $this->session->userdata('user_role');
  $uri  = uri_string();
  $sidebarUser = null;
  if ($uid) {
      $this->load->model('User_model');
      $sidebarUser = $this->User_model->getById($uid);
  }
  $avatarUrl = ($sidebarUser && !empty($sidebarUser->avatar))
    ? base_url('assets/uploads/avatars/' . $sidebarUser->avatar)
    : base_url('assets/images/default-avatar.svg');
?>
<nav id="sidebar">
  <div class="brand">
    <h5><i class="fa-solid fa-wallet me-2"></i><span>Expense</span>Tracker</h5>
  </div>
  <div class="px-3 py-2 d-flex align-items-center gap-2" style="border-bottom:1px solid rgba(255,255,255,.08);">
    <img src="<?= $avatarUrl ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.25);">
    <div style="overflow:hidden;">
      <div style="color:#fff;font-size:.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($user['name'] ?? '') ?></div>
      <div style="color:#a5b4fc;font-size:.7rem;"><?= ucfirst($role ?? 'user') ?></div>
    </div>
  </div>
  <ul class="nav flex-column mt-1 pb-4">
    <li class="nav-item">
      <a href="<?= site_url('dashboard') ?>" class="nav-link <?= ($uri === 'dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge-high"></i> Dashboard
      </a>
    </li>
    <div class="sidebar-section">Expenses</div>
    <li class="nav-item">
      <a href="<?= site_url('expenses') ?>" class="nav-link <?= (strpos($uri, 'expenses') === 0) ? 'active' : '' ?>">
        <i class="fa-solid fa-clock-rotate-left"></i> Transaction History
      </a>
    </li>
    <div class="sidebar-section">Tools</div>
    <li class="nav-item">
      <a href="<?= site_url('export') ?>" class="nav-link <?= (strpos($uri, 'export') === 0) ? 'active' : '' ?>">
        <i class="fa-solid fa-file-export"></i> Export
      </a>
    </li>
    <?php if (in_array($role, ['superadmin', 'admin'])): ?>
    <div class="sidebar-section">Administration</div>
    <li class="nav-item">
      <a href="<?= site_url('categories') ?>" class="nav-link <?= (strpos($uri, 'categories') === 0) ? 'active' : '' ?>">
        <i class="fa-solid fa-tags"></i> Categories
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= site_url('users') ?>" class="nav-link <?= (strpos($uri, 'users') === 0) ? 'active' : '' ?>">
        <i class="fa-solid fa-users"></i> User Management
      </a>
    </li>
    
    <?php endif; ?>
    <div class="sidebar-section">Account</div>
    <li class="nav-item">
      <a href="<?= site_url('profile') ?>" class="nav-link <?= (strpos($uri, 'profile') === 0) ? 'active' : '' ?>">
        <i class="fa-regular fa-user"></i> Profile
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= site_url('logout') ?>" class="nav-link" style="color:#fca5a5;">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </li>
  </ul>
</nav>

<div id="main-content">
  <div id="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm d-md-none" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
      <h6 class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></h6>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-light text-dark border" style="font-size:.75rem;">
        <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($user['name'] ?? '') ?>
      </span>
      <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-danger">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>
  </div>

  <div class="px-4 pt-3" id="flash-area">
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-check"></i><?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i><?= $this->session->flashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  </div>

  <div class="p-4 flex-grow-1">
    <?php $this->load->view($content_view) ?>
  </div>
  <footer class="text-center py-3 text-muted" style="font-size:.76rem;">&copy; <?= date('Y') ?> ExpenseTracker</footer>
</div>

<!-- ============ ADD EXPENSE MODAL ============ -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:16px 16px 0 0;">
        <h5 class="modal-title fw-700"><i class="fa-solid fa-circle-plus me-2"></i>Add Expense</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="expenseModalAlert" class="d-none"></div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="exp_title" class="form-control" placeholder="e.g. Lunch, Uber ride…">
          </div>
          <div class="col-sm-5">
            <label class="form-label">Amount <span class="text-danger">*</span></label>
            <input type="number" id="exp_amount" class="form-control" step="0.01" min="0.01" placeholder="0.00">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Currency <span class="text-danger">*</span></label>
            <select id="exp_currency" class="form-select">
              <?php foreach ($currencies  as $c): ?>
                <option value="<?= $c->code ?>"><?= $c->code ?> <?= $c->symbol ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-3">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" id="exp_date" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select id="exp_category_id" class="form-select">
              <option value="">— Select —</option>
              <?php
                $this->load->model('Category_model');
                $modalCats = $this->Category_model->getAccessible(
                    $this->session->userdata('user_id'),
                    $this->session->userdata('user_role')
                );
                foreach ($modalCats as $cat):
              ?>
                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Note <span class="text-muted fw-400">(optional)</span></label>
            <textarea id="exp_note" class="form-control" rows="2" placeholder="Any additional details…"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4" id="saveExpenseBtn">
          <i class="fa-solid fa-plus me-1"></i>Add Expense
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ============ EDIT EXPENSE MODAL ============ -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:16px 16px 0 0;">
        <h5 class="modal-title fw-700"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Expense</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="edit_exp_id">
        <div id="editExpenseAlert" class="d-none"></div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="edit_exp_title" class="form-control">
          </div>
          <div class="col-sm-5">
            <label class="form-label">Amount <span class="text-danger">*</span></label>
            <input type="number" id="edit_exp_amount" class="form-control" step="0.01" min="0.01">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Currency</label>
            <select id="edit_exp_currency" class="form-select">
              <?php foreach ($currencies  as $c): ?>
                <option value="<?= $c->code ?>"><?= $c->code ?> <?= $c->symbol ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-3">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" id="edit_exp_date" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Category</label>
            <select id="edit_exp_category_id" class="form-select">
              <?php foreach ($modalCats as $cat): ?>
                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Note</label>
            <textarea id="edit_exp_note" class="form-control" rows="2"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning px-4 text-dark fw-600" id="updateExpenseBtn">
          <i class="fa-solid fa-floppy-disk me-1"></i>Update
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="appToast" class="toast align-items-center border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body fw-500" id="toastMsg"></div>
      <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {

  function openAddExpenseModal() {
    ['exp_title','exp_amount','exp_note'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });

    document.getElementById('exp_date').value = new Date().toISOString().slice(0,10);
    document.getElementById('exp_currency').value = 'INR';
    document.getElementById('exp_category_id').value = '';

    const alert = document.getElementById('expenseModalAlert');
    if (alert) alert.className = 'd-none';

    bootstrap.Modal
      .getOrCreateInstance(document.getElementById('addExpenseModal'))
      .show();
  }


});
/* ─── Global helpers ──────────────────────────────────── */
const BASE_URL  = document.querySelector('meta[name="base-url"]').content;
const CSRF_NAME = document.querySelector('meta[name="csrf-name"]').content;
const getCSRF   = () => document.querySelector('meta[name="csrf-token"]').content;

function csrfParams(extra) {
  const p = new URLSearchParams(extra || {});
  p.set(CSRF_NAME, getCSRF());
  return p;
}

function ajaxPost(url, params) {
  return fetch(BASE_URL + url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: params
  }).then(r => r.json());
}

function ajaxGet(url) {
  return fetch(BASE_URL + url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
}

function btnLoading(btn, label) {
  btn.disabled  = true;
  btn._orig     = btn.innerHTML;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>' + (label || 'Saving…');
}

function btnReset(btn) {
  btn.disabled  = false;
  if (btn._orig) btn.innerHTML = btn._orig;
}

function showAlert(el, msg) {
  el.className  = 'alert alert-danger';
  el.innerHTML  = '<i class="fa-solid fa-triangle-exclamation me-1"></i>' + msg;
}

function showToast(msg, type) {
  const t = document.getElementById('appToast');
  t.className = 'toast align-items-center border-0 text-bg-' + (type === 'error' ? 'danger' : 'success');
  document.getElementById('toastMsg').textContent = msg;
  bootstrap.Toast.getOrCreateInstance(t, { delay: 3200 }).show();
}

/* ─── Sidebar toggle ──────────────────────────────────── */
document.getElementById('sidebarToggle')?.addEventListener('click', () =>
  document.getElementById('sidebar').classList.toggle('open')
);


/* ─── Save Expense ────────────────────────────────────── */
document.getElementById('saveExpenseBtn').addEventListener('click', function () {
  const btn = this;
  btnLoading(btn);

  ajaxPost('expenses/store', csrfParams({
    title:        document.getElementById('exp_title').value,
    amount:       document.getElementById('exp_amount').value,
    currency:     document.getElementById('exp_currency').value,
    expense_date: document.getElementById('exp_date').value,
    category_id:  document.getElementById('exp_category_id').value,
    note:         document.getElementById('exp_note').value,
  }))
  .then(res => {
    btnReset(btn);
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('addExpenseModal')).hide();
      showToast(res.message);
      setTimeout(() => location.reload(), 800);
    } else {
      const msg = res.errors ? Object.values(res.errors).join('<br>') : (res.message || 'Error occurred.');
      showAlert(document.getElementById('expenseModalAlert'), msg);
    }
  })
  .catch(() => { btnReset(btn); showToast('Network error. Please try again.', 'error'); });
});

/* ─── Edit Expense (used by expenses page and dashboard) */
function editExpense(id) {
  ajaxGet('expenses/get/' + id).then(res => {
    if (!res.success) return;
    const e = res.expense;
    document.getElementById('edit_exp_id').value          = e.id;
    document.getElementById('edit_exp_title').value       = e.title;
    document.getElementById('edit_exp_amount').value      = e.amount;
    document.getElementById('edit_exp_currency').value    = e.currency;
    document.getElementById('edit_exp_date').value        = e.expense_date;
    document.getElementById('edit_exp_category_id').value = e.category_id;
    document.getElementById('edit_exp_note').value        = e.note || '';
    document.getElementById('editExpenseAlert').className = 'd-none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editExpenseModal')).show();
  });
}

document.getElementById('updateExpenseBtn').addEventListener('click', function () {
  const btn = this;
  const id  = document.getElementById('edit_exp_id').value;
  btnLoading(btn, 'Updating…');

  ajaxPost('expenses/update/' + id, csrfParams({
    title:        document.getElementById('edit_exp_title').value,
    amount:       document.getElementById('edit_exp_amount').value,
    currency:     document.getElementById('edit_exp_currency').value,
    expense_date: document.getElementById('edit_exp_date').value,
    category_id:  document.getElementById('edit_exp_category_id').value,
    note:         document.getElementById('edit_exp_note').value,
  }))
  .then(res => {
    btnReset(btn);
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('editExpenseModal')).hide();
      showToast(res.message);
      setTimeout(() => location.reload(), 800);
    } else {
      const msg = res.errors ? Object.values(res.errors).join('<br>') : (res.message || 'Error.');
      showAlert(document.getElementById('editExpenseAlert'), msg);
    }
  })
  .catch(() => { btnReset(btn); showToast('Network error.', 'error'); });
});

/* ─── Delete Expense (used by expenses page) */
function deleteExpense(id) {
  if (!confirm('Delete this expense? This cannot be undone.')) return;
  ajaxPost('expenses/delete/' + id, csrfParams())
    .then(res => {
      if (res.success) { document.getElementById('expense-row-' + id)?.remove(); showToast(res.message); }
      else showToast(res.message || 'Could not delete.', 'error');
    })
    .catch(() => showToast('Network error.', 'error'));
}
</script>
<?php $this->load->view('layouts/scripts') ?>
</body>
</html>
