<?php
$isEdit   = isset($expense);
$formAction = $isEdit ? site_url('expenses/update/'.$expense->id) : site_url('expenses/store');
?>

<div class="row justify-content-center">
  <div class="col-lg-7 col-xl-6">

    <!-- Breadcrumb -->
    <nav class="mb-3" style="font-size:.83rem;">
      <a href="<?= site_url('expenses') ?>" class="text-muted text-decoration-none">
        <i class="fa-solid fa-list-ul me-1"></i>Expenses
      </a>
      <span class="mx-2 text-muted">/</span>
      <span class="text-dark fw-600"><?= $isEdit ? 'Edit Expense' : 'Add Expense' ?></span>
    </nav>

    <div class="card form-card shadow-sm">
      <div class="card-header bg-white border-bottom py-3 px-4">
        <h6 class="mb-0 fw-700">
          <i class="fa-solid <?= $isEdit ? 'fa-pen-to-square' : 'fa-circle-plus' ?> me-2" style="color:#6366f1;"></i>
          <?= $isEdit ? 'Edit Expense' : 'Add New Expense' ?>
        </h6>
      </div>
      <div class="card-body p-4">

        <?= form_open($formAction) ?>

          <!-- Title -->
          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title"
                   class="form-control <?= form_error('title') ? 'is-invalid' : '' ?>"
                   value="<?= $isEdit ? htmlspecialchars($expense->title) : set_value('title') ?>"
                   placeholder="e.g. Lunch at restaurant">
            <div class="invalid-feedback"><?= form_error('title') ?></div>
          </div>

          <!-- Amount + Date row -->
          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" name="amount" step="0.01" min="0.01"
                       class="form-control <?= form_error('amount') ? 'is-invalid' : '' ?>"
                       value="<?= $isEdit ? $expense->amount : set_value('amount') ?>"
                       placeholder="0.00">
                <div class="invalid-feedback"><?= form_error('amount') ?></div>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" name="expense_date"
                     class="form-control <?= form_error('expense_date') ? 'is-invalid' : '' ?>"
                     value="<?= $isEdit ? $expense->expense_date : set_value('expense_date', date('Y-m-d')) ?>"
                     max="<?= date('Y-m-d') ?>">
              <div class="invalid-feedback"><?= form_error('expense_date') ?></div>
            </div>
          </div>

          <!-- Category -->
          <div class="mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category_id"
                    class="form-select <?= form_error('category_id') ? 'is-invalid' : '' ?>">
              <option value="">— Select category —</option>
              <?php foreach ($categories as $cat): ?>
                <?php
                  $selected = '';
                  if ($isEdit && $expense->category_id == $cat->id) $selected = 'selected';
                  elseif ( ! $isEdit && set_value('category_id') == $cat->id) $selected = 'selected';
                ?>
                <option value="<?= $cat->id ?>" <?= $selected ?>>
                  <?= htmlspecialchars($cat->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback"><?= form_error('category_id') ?></div>
          </div>

          <!-- Note -->
          <div class="mb-4">
            <label class="form-label">Note <span class="text-muted fw-400">(optional)</span></label>
            <textarea name="note" rows="3"
                      class="form-control <?= form_error('note') ? 'is-invalid' : '' ?>"
                      placeholder="Any additional details…"><?= $isEdit ? htmlspecialchars($expense->note) : set_value('note') ?></textarea>
            <div class="invalid-feedback"><?= form_error('note') ?></div>
          </div>

          <!-- Actions -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
              <i class="fa-solid <?= $isEdit ? 'fa-floppy-disk' : 'fa-plus' ?> me-2"></i>
              <?= $isEdit ? 'Update Expense' : 'Add Expense' ?>
            </button>
            <a href="<?= site_url('expenses') ?>" class="btn btn-outline-secondary px-4">
              Cancel
            </a>
          </div>

        <?= form_close() ?>

      </div><!-- card-body -->
    </div><!-- card -->
  </div>
</div>
