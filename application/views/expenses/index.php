<?php
$monthNames  = [
  1 => 'January',
  2 => 'February',
  3 => 'March',
  4 => 'April',
  5 => 'May',
  6 => 'June',
  7 => 'July',
  8 => 'August',
  9 => 'September',
  10 => 'October',
  11 => 'November',
  12 => 'December'
];
$currentYear = date('Y');
?>

<!-- Filter bar -->
<div class="card shadow-sm mb-4" style="border-radius:14px;border:none;">
  <div class="card-body p-3">
    <form method="GET" action="<?= site_url('expenses') ?>" class="row g-2 align-items-end">
      <div class="col-sm-3">
        <label class="form-label mb-1">Search <small class="text-muted fw-400">(title or added by)</small></label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Search…" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
      </div>
      <div class="col-sm-2">
        <label class="form-label mb-1">Category</label>
        <select name="category_id" class="form-select">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat->id ?>" <?= ($filters['category_id'] == $cat->id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat->name) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-2">
        <label class="form-label mb-1">Month</label>
        <select name="month" class="form-select">
          <option value="">All Months</option>
          <?php foreach ($monthNames as $num => $name): ?>
            <option value="<?= $num ?>" <?= ($filters['month'] == $num) ? 'selected' : '' ?>><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-2">
        <label class="form-label mb-1">Year</label>
        <select name="year" class="form-select">
          <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
            <option value="<?= $y ?>" <?= ($filters['year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-sm-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">
          <i class="fa-solid fa-filter me-1"></i>Filter
        </button>
        <a href="<?= site_url('expenses') ?>" class="btn btn-outline-secondary">
          <i class="fa-solid fa-rotate-left"></i>
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-3">
  <h6 class="mb-0 fw-700">
    <?= count($expenses) ?> transaction(s)
    <span class="text-muted fw-400">— Total: <span style="color:#6366f1;font-weight:700;">₹<?= number_format($total, 2) ?></span></span>
  </h6>
  <button type="button" class="btn btn-primary" id="expPageAddBtn">
    <i class="fa-solid fa-plus me-1"></i>Add Expense
  </button>
</div>

<!-- Table -->
<div class="card table-card shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle" id="expensesTable">
      <thead>
        <tr>
          <th class="px-4">#</th>
          <th>Title</th>
          <th>Category</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Added By</th>
          <th>Note</th>
          <th class="text-center pe-4">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($expenses): foreach ($expenses as $i => $e): ?>
            <tr id="expense-row-<?= $e->id ?>">
              <td class="px-4 text-muted" style="font-size:.82rem;"><?= $i + 1 ?></td>
              <td class="fw-600"><?= htmlspecialchars($e->title) ?></td>
              <td>
                <span class="badge badge-cat" style="background:<?= htmlspecialchars($e->category_color ?? '#ede9fe') ?>22;color:<?= htmlspecialchars($e->category_color ?? '#6d28d9') ?>;">
                  <i class="fa-solid <?= htmlspecialchars($e->category_icon) ?> me-1"></i>
                  <?= htmlspecialchars($e->category_name) ?>
                </span>
              </td>
              <td class="fw-700" style="color:#6366f1;">
                <?= htmlspecialchars($e->currency_symbol ?? $e->currency) ?><?= number_format($e->amount, 2) ?>
                <small class="text-muted fw-400"><?= htmlspecialchars($e->currency) ?></small>
              </td>
              <td class="text-muted" style="font-size:.83rem;"><?= date('M j, Y', strtotime($e->expense_date)) ?></td>
              <td style="font-size:.82rem;">
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($e->added_by_name) ?></span>
                <br><small class="text-muted"><?= date('M j, g:ia', strtotime($e->created_at)) ?></small>
              </td>
              <td class="text-muted" style="font-size:.82rem;max-width:160px;">
                <?php if ($e->note): ?>
                  <span title="<?= htmlspecialchars($e->note) ?>">
                    <?= htmlspecialchars(mb_substr($e->note, 0, 35)) ?><?= mb_strlen($e->note) > 35 ? '…' : '' ?>
                  </span>
                  <?php else: ?>—<?php endif; ?>
              </td>
              <td class="text-center pe-4">
                <button type="button"
                  class="btn btn-sm btn-outline-primary me-1 btn-edit-expense"
                  data-id="<?= $e->id ?>"
                  style="border-radius:7px;"
                  title="Edit">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button"
                  class="btn btn-sm btn-outline-danger btn-delete-expense"
                  data-id="<?= $e->id ?>"
                  style="border-radius:7px;"
                  title="Delete">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach;
        else: ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="fa-solid fa-receipt fa-2x mb-2 d-block opacity-25"></i>
              No transactions found.
              <button type="button" class="btn btn-sm btn-link p-0" id="expEmptyAddBtn">Add your first one!</button>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
      <?php if ($expenses): ?>
        <tfoot>
          <tr style="background:#f9fafb;">
            <td colspan="3" class="px-4 fw-700">Total (original currencies)</td>
            <td class="fw-700" style="color:#6366f1;">₹<?= number_format($total, 2) ?></td>
            <td colspan="4"></td>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<script>
  function openAddExpenseModal() {
    ['exp_title', 'exp_amount', 'exp_note'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('exp_date').value = new Date().toISOString().slice(0, 10);
    document.getElementById('exp_currency').value = 'INR';
    document.getElementById('exp_category_id').value = '';
    document.getElementById('expenseModalAlert').className = 'd-none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('addExpenseModal')).show();
  }
  (function() {
    /* Add expense buttons */
    document.getElementById('expPageAddBtn')?.addEventListener('click', openAddExpenseModal);
    document.getElementById('expEmptyAddBtn')?.addEventListener('click', openAddExpenseModal);

    /* Event delegation for edit / delete buttons */
    document.getElementById('expensesTable').addEventListener('click', function(e) {
      const editBtn = e.target.closest('.btn-edit-expense');
      const deleteBtn = e.target.closest('.btn-delete-expense');
      if (editBtn) editExpense(editBtn.dataset.id);
      if (deleteBtn) deleteExpense(deleteBtn.dataset.id);
    });
  }());
</script>