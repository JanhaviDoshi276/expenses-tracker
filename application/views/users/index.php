<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h5 class="mb-0 fw-700">User Management</h5>
    <small class="text-muted">Manage users, roles, and category access</small>
  </div>
  <button type="button" class="btn btn-primary" id="newUserBtn">
    <i class="fa-solid fa-user-plus me-1"></i>New User
  </button>
</div>

<!-- Search -->
<form method="GET" action="<?= site_url('users') ?>" class="mb-4">
  <div class="input-group" style="max-width:360px;">
    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
    <input type="text" name="search" class="form-control" placeholder="Search by name or email…" value="<?= htmlspecialchars($search ?? '') ?>">
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($search): ?><a href="<?= site_url('users') ?>" class="btn btn-outline-secondary">Clear</a><?php endif; ?>
  </div>
</form>

<!-- Users table -->
<div class="card table-card shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle" id="usersTable">
      <thead>
        <tr>
          <th class="px-4">#</th>
          <th>User</th>
          <th>Role</th>
          <th>Status</th>
          <th>Currency</th>
          <th>Category Access</th>
          <th>Joined</th>
          <th class="text-center pe-4">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
        <tr id="user-row-<?= $u->id ?>">
          <td class="px-4 text-muted" style="font-size:.82rem;"><?= $i + 1 ?></td>
          <td>
            <div class="fw-600"><?= htmlspecialchars($u->name) ?></div>
            <small class="text-muted"><?= htmlspecialchars($u->email) ?></small>
          </td>
          <td>
            <span class="badge role-badge-<?= $u->role ?>" style="font-size:.75rem;"><?= ucfirst($u->role) ?></span>
          </td>
          <td>
            <button type="button"
                    class="btn btn-sm <?= $u->status ? 'btn-success' : 'btn-secondary' ?> btn-toggle-status"
                    data-id="<?= $u->id ?>"
                    style="border-radius:20px;font-size:.72rem;padding:.25rem .65rem;"
                    <?= ($u->role === 'superadmin') ? 'disabled' : '' ?>>
              <?= $u->status ? 'Active' : 'Inactive' ?>
            </button>
          </td>
          <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($u->currency) ?></span></td>
          <td>
            <?php if ($u->role === 'superadmin'): ?>
              <span class="badge bg-warning text-dark" style="font-size:.72rem;">All (Superadmin)</span>
            <?php elseif ($u->role === 'admin'): ?>
              <span class="badge text-white" style="font-size:.72rem;background:#6366f1;">All (Admin)</span>
            <?php else:
              $count = isset($access_map[$u->id]) ? count($access_map[$u->id]) : 0;
            ?>
              <span class="badge bg-light text-dark border" style="font-size:.72rem;"><?= $count ?> categor<?= $count === 1 ? 'y' : 'ies' ?></span>
            <?php endif; ?>
          </td>
          <td class="text-muted" style="font-size:.82rem;"><?= date('M j, Y', strtotime($u->created_at)) ?></td>
          <td class="text-center pe-4">
            <?php if ($u->role !== 'superadmin'): ?>
            <button type="button"
                    class="btn btn-sm btn-outline-primary me-1 btn-edit-user"
                    data-id="<?= $u->id ?>"
                    style="border-radius:7px;" title="Edit">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button type="button"
                    class="btn btn-sm btn-outline-danger btn-delete-user"
                    data-id="<?= $u->id ?>"
                    data-name="<?= htmlspecialchars($u->name, ENT_QUOTES) ?>"
                    style="border-radius:7px;" title="Delete">
              <i class="fa-solid fa-trash"></i>
            </button>
            <?php else: ?>
            <span class="text-muted" style="font-size:.78rem;"><i class="fa-solid fa-shield me-1"></i>Protected</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="background:linear-gradient(135deg,#1e1b4b,#4f46e5);color:#fff;border-radius:16px 16px 0 0;">
        <h5 class="modal-title fw-700" id="userModalTitle"><i class="fa-solid fa-user-plus me-2"></i>Add User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="user_id">
        <div id="userAlert" class="d-none mb-3"></div>
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" id="u_name" class="form-control" placeholder="John Doe">
          </div>
          <div class="col-sm-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" id="u_email" class="form-control" placeholder="john@example.com">
          </div>
          <div class="col-sm-6">
            <label class="form-label">Password <span id="pwdHint" class="text-muted fw-400">(min 6 chars)</span></label>
            <input type="password" id="u_password" class="form-control" placeholder="Enter password" autocomplete="new-password">
          </div>
          <div class="col-sm-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select id="u_role" class="form-select">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="col-sm-3">
            <label class="form-label">Currency</label>
            <select id="u_currency" class="form-select">
              <?php
                foreach ($currencies as $c):
              ?>
              <option value="<?= $c->code ?>"><?= $c->code ?> — <?= htmlspecialchars($c->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-3 status-field d-none">
            <label class="form-label">Status</label>
            <select id="u_status" class="form-select">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <!-- Category access (only for role=user) -->
          <div class="col-12" id="categoryAccessSection">
            <label class="form-label">Category Access
              <span class="text-muted fw-400">(select which categories this user can access)</span>
            </label>
            <div class="border rounded p-3" style="border-radius:10px!important;max-height:200px;overflow-y:auto;">
              <?php foreach ($categories as $cat): ?>
              <div class="form-check mb-1">
                <input class="form-check-input cat-access-cb" type="checkbox"
                       name="category_ids[]" value="<?= $cat->id ?>"
                       id="cat_cb_<?= $cat->id ?>">
                <label class="form-check-label" for="cat_cb_<?= $cat->id ?>" style="font-size:.85rem;">
                  <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= htmlspecialchars($cat->color) ?>;margin-right:4px;"></span>
                  <?= htmlspecialchars($cat->name) ?>
                </label>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="mt-2 d-flex gap-2">
              <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllCatsBtn">Select All</button>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllCatsBtn">Deselect All</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4" id="saveUserBtn">
          <i class="fa-solid fa-floppy-disk me-1"></i>Save
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  /* ── Role change — show/hide category access section ── */
  function handleRoleChange(role) {
    const catSection = document.getElementById('categoryAccessSection');
    const disabled   = role === 'admin';
    catSection.style.opacity = disabled ? '0.4' : '1';
    catSection.querySelectorAll('input').forEach(i => i.disabled = disabled);
  }

  document.getElementById('u_role').addEventListener('change', function () {
    handleRoleChange(this.value);
  });

  /* ── Select / Deselect all categories ── */
  document.getElementById('selectAllCatsBtn').addEventListener('click', () => {
    document.querySelectorAll('.cat-access-cb:not(:disabled)').forEach(c => c.checked = true);
  });
  document.getElementById('deselectAllCatsBtn').addEventListener('click', () => {
    document.querySelectorAll('.cat-access-cb:not(:disabled)').forEach(c => c.checked = false);
  });

  /* ── Open Add User modal ── */
  function openUserModal() {
    document.getElementById('user_id').value   = '';
    document.getElementById('u_name').value    = '';
    document.getElementById('u_email').value   = '';
    document.getElementById('u_password').value = '';
    document.getElementById('u_role').value    = 'user';
    document.getElementById('u_currency').value = 'INR';
    document.getElementById('u_status').value  = '1';
    document.getElementById('userAlert').className = 'd-none';
    document.getElementById('userModalTitle').innerHTML = '<i class="fa-solid fa-user-plus me-2"></i>Add User';
    document.getElementById('pwdHint').textContent = '(min 6 chars)';
    document.querySelector('.status-field').classList.add('d-none');
    document.querySelectorAll('.cat-access-cb').forEach(c => { c.checked = false; c.disabled = false; });
    handleRoleChange('user');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('userModal')).show();
  }

  document.getElementById('newUserBtn').addEventListener('click', openUserModal);

  /* ── Table event delegation ── */
  document.getElementById('usersTable').addEventListener('click', function (e) {
    const editBtn     = e.target.closest('.btn-edit-user');
    const deleteBtn   = e.target.closest('.btn-delete-user');
    const toggleBtn   = e.target.closest('.btn-toggle-status');

    if (editBtn) {
      const id = editBtn.dataset.id;
      ajaxGet('users/get/' + id).then(res => {
        if (!res.success) return;
        const u = res.user;
        document.getElementById('user_id').value    = u.id;
        document.getElementById('u_name').value     = u.name;
        document.getElementById('u_email').value    = u.email;
        document.getElementById('u_password').value = '';
        document.getElementById('u_role').value     = u.role;
        document.getElementById('u_currency').value = u.currency;
        document.getElementById('u_status').value   = u.status;
        document.getElementById('userAlert').className = 'd-none';
        document.getElementById('userModalTitle').innerHTML = '<i class="fa-solid fa-pen me-2"></i>Edit User';
        document.getElementById('pwdHint').textContent = '(leave blank to keep current)';
        document.querySelector('.status-field').classList.remove('d-none');

        const catIds = (u.category_ids || []).map(Number);
        document.querySelectorAll('.cat-access-cb').forEach(c => {
          c.checked  = catIds.includes(parseInt(c.value));
          c.disabled = false;
        });
        handleRoleChange(u.role);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('userModal')).show();
      });
    }

    if (deleteBtn) {
      const id   = deleteBtn.dataset.id;
      const name = deleteBtn.dataset.name;
      if (!confirm('Delete user "' + name + '"?\nTheir expenses will also be deleted.')) return;
      ajaxPost('users/delete/' + id, csrfParams())
        .then(res => {
          if (res.success) { document.getElementById('user-row-' + id)?.remove(); showToast(res.message); }
          else showToast(res.message, 'error');
        })
        .catch(() => showToast('Network error.', 'error'));
    }

    if (toggleBtn) {
      const id  = toggleBtn.dataset.id;
      ajaxPost('users/toggle-status/' + id, csrfParams())
        .then(res => {
          if (res.success) {
            toggleBtn.className = 'btn btn-sm btn-toggle-status ' +
              (res.status ? 'btn-success' : 'btn-secondary');
            toggleBtn.style.borderRadius = '20px';
            toggleBtn.style.fontSize     = '.72rem';
            toggleBtn.style.padding      = '.25rem .65rem';
            toggleBtn.textContent        = res.status ? 'Active' : 'Inactive';
            showToast(res.message);
          } else showToast(res.message, 'error');
        })
        .catch(() => showToast('Network error.', 'error'));
    }
  });

  /* ── Save User ── */
  document.getElementById('saveUserBtn').addEventListener('click', function () {
    const btn = this;
    const id  = document.getElementById('user_id').value;
    btnLoading(btn);

    const catIds = Array.from(document.querySelectorAll('.cat-access-cb:checked')).map(c => c.value);
    const params = csrfParams({
      name:     document.getElementById('u_name').value,
      email:    document.getElementById('u_email').value,
      password: document.getElementById('u_password').value,
      role:     document.getElementById('u_role').value,
      currency: document.getElementById('u_currency').value,
      status:   document.getElementById('u_status').value,
    });
    catIds.forEach(cid => params.append('category_ids[]', cid));

    ajaxPost(id ? 'users/update/' + id : 'users/store', params)
      .then(res => {
        btnReset(btn);
        if (res.success) {
          bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
          showToast(res.message);
          setTimeout(() => location.reload(), 700);
        } else {
          const msg = res.errors ? Object.values(res.errors).join('<br>') : (res.message || 'Error.');
          showAlert(document.getElementById('userAlert'), msg);
        }
      })
      .catch(() => { btnReset(btn); showToast('Network error.', 'error'); });
  });
}());
</script>
