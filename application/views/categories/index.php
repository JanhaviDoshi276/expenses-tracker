<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h5 class="mb-0 fw-700">Manage Categories</h5>
    <small class="text-muted">Changes auto-reflect to all expenses</small>
  </div>
  <button type="button" class="btn btn-primary" id="newCategoryBtn">
    <i class="fa-solid fa-plus me-1"></i>New Category
  </button>
</div>

<!-- Search -->
<form method="GET" action="<?= site_url('categories') ?>" class="mb-4">
  <div class="input-group" style="max-width:360px;">
    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
    <input type="text" name="search" class="form-control" placeholder="Search category name…" value="<?= htmlspecialchars($search ?? '') ?>">
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($search): ?>
      <a href="<?= site_url('categories') ?>" class="btn btn-outline-secondary">Clear</a>
    <?php endif; ?>
  </div>
</form>

<!-- Category grid -->
<div class="row g-3" id="categoriesGrid">
  <?php foreach ($categories as $cat): ?>
  <div class="col-sm-6 col-md-4 col-lg-3" id="cat-card-<?= $cat->id ?>">
    <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
      <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3 mb-2">
          <div style="width:42px;height:42px;border-radius:12px;background:<?= htmlspecialchars($cat->color) ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid <?= htmlspecialchars($cat->icon) ?> text-white"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="fw-700 text-dark" style="font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($cat->name) ?></div>
            <small class="text-muted"><?= htmlspecialchars($cat->icon) ?></small>
          </div>
        </div>
        <?php if ($cat->is_default): ?>
          <span class="badge bg-warning text-dark" style="font-size:.7rem;"><i class="fa-solid fa-shield me-1"></i>Default</span>
        <?php endif; ?>
      </div>
      <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3 px-3">
        <button type="button"
                class="btn btn-sm btn-outline-primary flex-grow-1 btn-edit-cat"
                data-id="<?= $cat->id ?>"
                style="border-radius:7px;font-size:.8rem;">
          <i class="fa-solid fa-pen"></i> Edit
        </button>
        <?php if (!$cat->is_default): ?>
        <button type="button"
                class="btn btn-sm btn-outline-danger btn-delete-cat"
                data-id="<?= $cat->id ?>"
                data-name="<?= htmlspecialchars($cat->name, ENT_QUOTES) ?>"
                style="border-radius:7px;"
                title="Delete">
          <i class="fa-solid fa-trash"></i>
        </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Category Modal (Add/Edit) -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;">
      <div class="modal-header" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:16px 16px 0 0;">
        <h5 class="modal-title fw-700" id="categoryModalTitle"><i class="fa-solid fa-tags me-2"></i>Add Category</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="cat_id">
        <div id="catAlert" class="d-none mb-3"></div>
        <div class="mb-3">
          <label class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" id="cat_name" class="form-control" placeholder="e.g. Groceries">
        </div>
        <div class="mb-3">
          <label class="form-label">Icon (FontAwesome class) <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text" id="iconPreview"><i class="fa-solid fa-tag" id="iconPreviewI"></i></span>
            <input type="text" id="cat_icon" class="form-control" placeholder="fa-tag">
          </div>
          <small class="text-muted">Find icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a></small>
        </div>
        <div class="mb-3">
          <label class="form-label">Color <span class="text-danger">*</span></label>
          <div class="d-flex gap-2 flex-wrap mb-2" id="colorSwatches">
            <?php
            $swatches = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f59e0b','#10b981','#06b6d4','#3b82f6','#84cc16','#ef4444','#f97316','#64748b'];
            foreach ($swatches as $sw):
            ?>
            <div class="color-swatch"
                 data-color="<?= $sw ?>"
                 style="width:28px;height:28px;border-radius:50%;background:<?= $sw ?>;cursor:pointer;border:2px solid transparent;">
            </div>
            <?php endforeach; ?>
          </div>
          <input type="color" id="cat_color" class="form-control form-control-color" value="#6366f1" title="Or pick a custom color">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4" id="saveCatBtn">
          <i class="fa-solid fa-floppy-disk me-1"></i>Save
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  /* ── helpers ── */
  function updateIconPreview(val) {
    document.getElementById('iconPreviewI').className = 'fa-solid ' + (val.trim() || 'fa-tag');
  }

  function selectColor(color) {
    document.getElementById('cat_color').value = color;
    document.querySelectorAll('.color-swatch').forEach(s => {
      s.style.borderColor = s.dataset.color === color ? '#1e1b4b' : 'transparent';
    });
  }

  function resetModal(isEdit) {
    document.getElementById('cat_id').value   = '';
    document.getElementById('cat_name').value = '';
    document.getElementById('cat_icon').value = 'fa-tag';
    document.getElementById('catAlert').className = 'd-none';
    document.getElementById('categoryModalTitle').innerHTML =
      isEdit ? '<i class="fa-solid fa-pen me-2"></i>Edit Category'
             : '<i class="fa-solid fa-tags me-2"></i>Add Category';
    updateIconPreview('fa-tag');
    selectColor('#6366f1');
  }

  /* ── New category ── */
  document.getElementById('newCategoryBtn').addEventListener('click', () => {
    resetModal(false);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal')).show();
  });

  /* ── Edit / Delete — event delegation on grid ── */
  document.getElementById('categoriesGrid').addEventListener('click', function (e) {
    const editBtn   = e.target.closest('.btn-edit-cat');
    const deleteBtn = e.target.closest('.btn-delete-cat');

    if (editBtn) {
      const id = editBtn.dataset.id;
      ajaxGet('categories/get/' + id).then(res => {
        if (!res.success) return;
        const c = res.category;
        resetModal(true);
        document.getElementById('cat_id').value   = c.id;
        document.getElementById('cat_name').value = c.name;
        document.getElementById('cat_icon').value = c.icon;
        updateIconPreview(c.icon);
        selectColor(c.color);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryModal')).show();
      });
    }

    if (deleteBtn) {
      const id   = deleteBtn.dataset.id;
      const name = deleteBtn.dataset.name;
      if (!confirm('Delete category "' + name + '"?\nAll related expenses will be moved to "Unspecified".')) return;
      ajaxPost('categories/delete/' + id, csrfParams())
        .then(res => {
          if (res.success) { document.getElementById('cat-card-' + id)?.remove(); showToast(res.message); }
          else showToast(res.message, 'error');
        })
        .catch(() => showToast('Network error.', 'error'));
    }
  });

  /* ── Color swatches ── */
  document.getElementById('colorSwatches').addEventListener('click', function (e) {
    const swatch = e.target.closest('.color-swatch');
    if (swatch) selectColor(swatch.dataset.color);
  });

  /* ── Color picker input ── */
  document.getElementById('cat_color').addEventListener('input', function () {
    selectColor(this.value);
  });

  /* ── Icon input live preview ── */
  document.getElementById('cat_icon').addEventListener('input', function () {
    updateIconPreview(this.value);
  });

  /* ── Save category ── */
  document.getElementById('saveCatBtn').addEventListener('click', function () {
    const btn = this;
    const id  = document.getElementById('cat_id').value;
    btnLoading(btn);

    ajaxPost(
      id ? 'categories/update/' + id : 'categories/store',
      csrfParams({
        name:  document.getElementById('cat_name').value,
        icon:  document.getElementById('cat_icon').value,
        color: document.getElementById('cat_color').value,
      })
    )
    .then(res => {
      btnReset(btn);
      if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        showToast(res.message);
        setTimeout(() => location.reload(), 700);
      } else {
        const msg = res.errors ? Object.values(res.errors).join('<br>') : (res.message || 'Error.');
        showAlert(document.getElementById('catAlert'), msg);
      }
    })
    .catch(() => { btnReset(btn); showToast('Network error.', 'error'); });
  });
}());
</script>
