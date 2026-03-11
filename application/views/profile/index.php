<?php
$activeTab = isset($active_tab) ? $active_tab : 'profile';
$avatarUrl = (!empty($profile->avatar))
    ? base_url('assets/uploads/avatars/' . $profile->avatar)
    : base_url('assets/images/default-avatar.svg');
if (!isset($currencies)) $currencies = [];
?>

<div class="row justify-content-center">
  <div class="col-lg-9 col-xl-8">

    <!-- Profile header card -->
    <div class="card shadow-sm mb-4" style="border-radius:16px;border:none;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;">
      <div class="card-body p-4 d-flex align-items-center gap-4 flex-wrap">
        <div style="position:relative;">
          <img src="<?= $avatarUrl ?>"
               id="avatarPreview"
               alt="Avatar"
               style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.4);">
          <label for="avatarInput"
                 style="position:absolute;bottom:0;right:0;width:28px;height:28px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,.2);">
            <i class="fa-solid fa-pen" style="font-size:.65rem;color:#6366f1;"></i>
          </label>
        </div>
        <div>
          <h5 class="mb-0 fw-700"><?= htmlspecialchars($profile->name) ?></h5>
          <p class="mb-1 opacity-75" style="font-size:.88rem;"><?= htmlspecialchars($profile->email) ?></p>
          <span class="badge" style="background:rgba(255,255,255,.2);font-size:.75rem;">
            Member since <?= date('M Y', strtotime($profile->created_at)) ?>
          </span>
        </div>
        <?php if ($profile->avatar): ?>
        <div class="ms-auto">
          <?= form_open('profile/remove-avatar', ['class' => 'd-inline', 'id' => 'removeAvatarForm']) ?>
            <button type="submit" class="btn btn-sm btn-remove-avatar"
                    style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;font-size:.78rem;border:none;">
              <i class="fa-solid fa-trash me-1"></i>Remove Photo
            </button>
          <?= form_close() ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-4 gap-2" id="profileTabs">
      <li class="nav-item">
        <button type="button"
                class="nav-link <?= $activeTab === 'profile' ? 'active' : '' ?>"
                data-tab="profile">
          <i class="fa-regular fa-user me-1"></i>Edit Profile
        </button>
      </li>
      <li class="nav-item">
        <button type="button"
                class="nav-link <?= $activeTab === 'password' ? 'active' : '' ?>"
                data-tab="password">
          <i class="fa-solid fa-key me-1"></i>Change Password
        </button>
      </li>
    </ul>

    <!-- =========== Profile Tab =========== -->
    <div id="tab-profile" class="<?= $activeTab !== 'profile' ? 'd-none' : '' ?>">
      <div class="card shadow-sm" style="border-radius:14px;border:none;">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <h6 class="mb-0 fw-700"><i class="fa-regular fa-user me-2" style="color:#6366f1;"></i>Profile Information</h6>
        </div>
        <div class="card-body p-4">
          <?= form_open_multipart('profile/update') ?>
            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="d-none">

            <div class="row g-3 mb-3">
              <div class="col-sm-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
                       value="<?= set_value('name', htmlspecialchars($profile->name)) ?>">
                <div class="invalid-feedback"><?= form_error('name') ?></div>
              </div>
              <div class="col-sm-6">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email"
                       class="form-control"
                       value="<?= set_value('email', htmlspecialchars($profile->email)) ?>">
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label">Preferred Currency</label>
              <select name="currency" class="form-select">
                <?php foreach ($currencies as $c): ?>
                  <option value="<?= $c->code ?>" <?= (($profile->currency ?? 'INR') === $c->code) ? 'selected' : '' ?>>
                    <?= $c->symbol ?> <?= $c->code ?> — <?= htmlspecialchars($c->name) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text text-muted" style="font-size:.78rem;">Used for dashboard display and exports.</div>
            </div>

            <!-- Avatar preview area -->
            <div id="avatarNewPreview" class="mb-4 d-none">
              <label class="form-label">New Avatar Preview</label>
              <div class="d-flex align-items-center gap-3">
                <img id="avatarNewImg" src="" alt="Preview"
                     style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;">
                <div>
                  <p class="mb-1 text-dark fw-600" style="font-size:.85rem;" id="avatarFileName"></p>
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAvatarBtn" style="font-size:.78rem;border-radius:7px;">
                    <i class="fa-solid fa-xmark me-1"></i>Remove
                  </button>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
              </button>
              <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
          <?= form_close() ?>
        </div>
      </div>
    </div>

    <!-- =========== Password Tab =========== -->
    <div id="tab-password" class="<?= $activeTab !== 'password' ? 'd-none' : '' ?>">
      <div class="card shadow-sm" style="border-radius:14px;border:none;">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <h6 class="mb-0 fw-700"><i class="fa-solid fa-key me-2" style="color:#6366f1;"></i>Change Password</h6>
        </div>
        <div class="card-body p-4">
          <?= form_open('profile/change-password') ?>
            <div class="mb-3">
              <label class="form-label">Current Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="current_password" id="curPwd"
                       class="form-control <?= form_error('current_password') ? 'is-invalid' : '' ?>"
                       placeholder="Your current password">
                <button type="button" class="btn btn-outline-secondary btn-toggle-pwd" data-target="curPwd" data-icon="curPwdIcon">
                  <i class="fa-regular fa-eye" id="curPwdIcon"></i>
                </button>
              </div>
              <div class="text-danger mt-1" style="font-size:.82rem;"><?= form_error('current_password') ?></div>
            </div>

            <div class="mb-3">
              <label class="form-label">New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="new_password" id="newPwd"
                       class="form-control <?= form_error('new_password') ? 'is-invalid' : '' ?>"
                       placeholder="Min. 6 characters">
                <button type="button" class="btn btn-outline-secondary btn-toggle-pwd" data-target="newPwd" data-icon="newPwdIcon">
                  <i class="fa-regular fa-eye" id="newPwdIcon"></i>
                </button>
              </div>
              <div class="mt-2">
                <div class="progress" style="height:5px;border-radius:10px;">
                  <div id="strengthBar" class="progress-bar" style="width:0%;border-radius:10px;transition:width .3s,background .3s;"></div>
                </div>
                <small id="strengthLabel" class="text-muted" style="font-size:.75rem;"></small>
              </div>
              <div class="text-danger mt-1" style="font-size:.82rem;"><?= form_error('new_password') ?></div>
            </div>

            <div class="mb-4">
              <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="confirm_password" id="confPwd"
                       class="form-control <?= form_error('confirm_password') ? 'is-invalid' : '' ?>"
                       placeholder="Repeat new password">
                <button type="button" class="btn btn-outline-secondary btn-toggle-pwd" data-target="confPwd" data-icon="confPwdIcon">
                  <i class="fa-regular fa-eye" id="confPwdIcon"></i>
                </button>
              </div>
              <div id="matchHint" class="mt-1" style="font-size:.78rem;"></div>
              <div class="text-danger mt-1" style="font-size:.82rem;"><?= form_error('confirm_password') ?></div>
            </div>

            <button type="submit" class="btn btn-primary px-4">
              <i class="fa-solid fa-lock me-2"></i>Update Password
            </button>
          <?= form_close() ?>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function () {
  /* ── Tab switching ── */
  document.getElementById('profileTabs').addEventListener('click', function (e) {
    const btn = e.target.closest('[data-tab]');
    if (!btn) return;
    const tab = btn.dataset.tab;
    document.getElementById('tab-profile').classList.toggle('d-none',  tab !== 'profile');
    document.getElementById('tab-password').classList.toggle('d-none', tab !== 'password');
    document.querySelectorAll('#profileTabs [data-tab]').forEach(b =>
      b.classList.toggle('active', b.dataset.tab === tab)
    );
  });

  /* ── Remove avatar confirm ── */
  const removeForm = document.getElementById('removeAvatarForm');
  if (removeForm) {
    removeForm.addEventListener('submit', function (e) {
      if (!confirm('Remove your avatar photo?')) e.preventDefault();
    });
  }

  /* ── Password visibility toggle — event delegation ── */
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-toggle-pwd');
    if (!btn) return;
    const field = document.getElementById(btn.dataset.target);
    const icon  = document.getElementById(btn.dataset.icon);
    if (!field || !icon) return;
    field.type = field.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
  });

  /* ── Avatar file preview ── */
  document.getElementById('avatarInput')?.addEventListener('change', function () {
    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('avatarNewImg').src      = e.target.result;
        document.getElementById('avatarFileName').textContent = this.files[0].name;
        document.getElementById('avatarNewPreview').classList.remove('d-none');
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  document.getElementById('clearAvatarBtn')?.addEventListener('click', function () {
    document.getElementById('avatarInput').value = '';
    document.getElementById('avatarNewPreview').classList.add('d-none');
  });

  /* ── Password strength meter ── */
  document.getElementById('newPwd')?.addEventListener('input', function () {
    const v = this.value;
    let score = 0;
    if (v.length >= 6)              score++;
    if (v.length >= 10)             score++;
    if (/[A-Z]/.test(v))            score++;
    if (/[0-9]/.test(v))            score++;
    if (/[^A-Za-z0-9]/.test(v))    score++;
    const levels = [
      { pct: '20%', bg: '#ef4444', txt: 'Very weak'  },
      { pct: '40%', bg: '#f97316', txt: 'Weak'        },
      { pct: '60%', bg: '#eab308', txt: 'Fair'        },
      { pct: '80%', bg: '#22c55e', txt: 'Good'        },
      { pct: '100%',bg: '#10b981', txt: 'Strong'      },
    ];
    const l   = levels[Math.max(0, score - 1)] || levels[0];
    const bar = document.getElementById('strengthBar');
    const lbl = document.getElementById('strengthLabel');
    bar.style.width      = v ? l.pct : '0%';
    bar.style.background = l.bg;
    lbl.textContent      = v ? l.txt : '';
    lbl.style.color      = l.bg;
  });

  /* ── Password match hint ── */
  document.getElementById('confPwd')?.addEventListener('input', function () {
    const match = this.value === document.getElementById('newPwd').value;
    const hint  = document.getElementById('matchHint');
    hint.textContent = this.value ? (match ? '✓ Passwords match' : '✗ Passwords do not match') : '';
    hint.style.color = match ? '#10b981' : '#ef4444';
  });
}());
</script>
