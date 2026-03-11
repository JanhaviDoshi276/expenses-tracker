<?php
$months    = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$catColors = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f59e0b','#10b981','#06b6d4','#3b82f6','#84cc16','#a855f7'];
$symbolMap = ['INR'=>'₹','USD'=>'$','EUR'=>'€','GBP'=>'£','AED'=>'د.إ','JPY'=>'¥','CAD'=>'CA$','AUD'=>'A$'];
$sym       = $symbolMap[$display_currency] ?? ($display_currency . ' ');
?>

<!-- Currency Selector -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h6 class="mb-0 fw-700 text-dark" style="font-size:1rem;">Overview — <?= date('F Y') ?></h6>
    <small class="text-muted">All amounts shown in selected currency</small>
  </div>
  <form method="GET" action="<?= site_url('dashboard') ?>" class="d-flex align-items-center gap-2" id="currencyForm">
    <label class="form-label mb-0 fw-600" style="font-size:.82rem;">Display in:</label>
    <select name="currency" class="form-select form-select-sm" id="currencySelect" style="width:auto;border-radius:8px;">
      <?php foreach ($currencies as $c): ?>
        <option value="<?= $c->code ?>" <?= ($c->code === $display_currency) ? 'selected' : '' ?>>
          <?= $c->symbol ?> <?= $c->code ?> — <?= htmlspecialchars($c->name) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-violet">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="label">This Month</div>
          <div class="value"><?= $sym ?><?= number_format($total_month, 2) ?></div>
        </div>
        <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-emerald">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="label">This Year</div>
          <div class="value"><?= $sym ?><?= number_format($total_year, 2) ?></div>
        </div>
        <div class="icon"><i class="fa-solid fa-chart-line"></i></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-amber">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="label">All Time</div>
          <div class="value"><?= $sym ?><?= number_format($total_all, 2) ?></div>
        </div>
        <div class="icon"><i class="fa-solid fa-wallet"></i></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card bg-rose">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="label">Categories Used</div>
          <div class="value"><?= count($by_category) ?></div>
        </div>
        <div class="icon"><i class="fa-solid fa-tags"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Monthly chart -->
  <div class="col-xl-8">
    <div class="card table-card shadow-sm h-100">
      <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4">
        <h6 class="mb-0 fw-700">Monthly Spending — <?= date('Y') ?> (<?= $sym ?>)</h6>
      </div>
      <div class="card-body p-4">
        <canvas id="monthlyChart" height="120"
          data-chart='<?= htmlspecialchars(json_encode($monthly_chart), ENT_QUOTES) ?>'
          data-symbol='<?= htmlspecialchars($sym, ENT_QUOTES) ?>'>
        </canvas>
      </div>
    </div>
  </div>
  <!-- Category breakdown -->
  <div class="col-xl-4">
    <div class="card table-card shadow-sm h-100">
      <div class="card-header bg-white border-bottom py-3 px-4">
        <h6 class="mb-0 fw-700">This Month by Category</h6>
      </div>
      <div class="card-body p-3" style="overflow-y:auto;max-height:320px;">
        <?php if ($by_category): foreach ($by_category as $i => $cat): ?>
          <div class="d-flex align-items-center mb-3">
            <div class="me-3 flex-shrink-0" style="width:34px;height:34px;border-radius:10px;background:<?= htmlspecialchars($cat->color ?? $catColors[$i % 10]) ?>;display:flex;align-items:center;justify-content:center;">
              <i class="fa-solid <?= htmlspecialchars($cat->icon) ?> text-white" style="font-size:.8rem;"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <small class="fw-600 text-dark"><?= htmlspecialchars($cat->name) ?></small>
                <small class="fw-700" style="color:<?= htmlspecialchars($cat->color ?? $catColors[$i % 10]) ?>">
                  <?= $sym ?><?= number_format($cat->total * $rate, 2) ?>
                </small>
              </div>
              <div class="progress mt-1" style="height:4px;border-radius:10px;">
                <?php $pct = $total_month > 0 ? ($cat->total * $rate / $total_month * 100) : 0 ?>
                <div class="progress-bar" style="width:<?= min(100, $pct) ?>%;background:<?= htmlspecialchars($cat->color ?? $catColors[$i % 10]) ?>;border-radius:10px;"></div>
              </div>
            </div>
          </div>
        <?php endforeach; else: ?>
          <p class="text-muted text-center py-4" style="font-size:.85rem;">No expenses this month.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Recent Expenses -->
<div class="card table-card shadow-sm">
  <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4">
    <h6 class="mb-0 fw-700">Recent Expenses</h6>
    <a href="<?= site_url('expenses') ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">
      View All <i class="fa-solid fa-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th class="px-4">Title</th>
          <th>Category</th>
          <th>Date</th>
          <th>Currency</th>
          <th class="text-end pe-4">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($recent): foreach ($recent as $e): ?>
          <tr>
            <td class="px-4 fw-600"><?= htmlspecialchars($e->title) ?></td>
            <td>
              <span class="badge badge-cat" style="background:<?= htmlspecialchars($e->category_color ?? '#ede9fe') ?>22;color:<?= htmlspecialchars($e->category_color ?? '#6d28d9') ?>;">
                <i class="fa-solid <?= htmlspecialchars($e->category_icon) ?> me-1"></i>
                <?= htmlspecialchars($e->category_name) ?>
              </span>
            </td>
            <td class="text-muted" style="font-size:.83rem;"><?= date('M j, Y', strtotime($e->expense_date)) ?></td>
            <td><span class="badge bg-light text-dark border" style="font-size:.72rem;"><?= htmlspecialchars($e->currency) ?></span></td>
            <td class="text-end pe-4 fw-700" style="color:#6366f1;">
              <?= $sym ?><?= number_format($e->amount * $rate, 2) ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              No expenses yet.
              <!-- <button type="button" class="btn btn-sm btn-link p-0 ms-1" id="dashboardAddBtn">Add one!</button> -->
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>

  
(function () {
  /* Currency selector — auto-submit form on change */
  document.getElementById('currencySelect').addEventListener('change', function () {
    document.getElementById('currencyForm').submit();
  });

  /* Dashboard empty-state add button */
  const dashBtn = document.getElementById('dashboardAddBtn');
  if (dashBtn) dashBtn.addEventListener('click', openAddExpenseModal);

  /* Monthly bar chart */
  const canvas = document.getElementById('monthlyChart');
  if (canvas && typeof Chart !== 'undefined') {
    const data   = JSON.parse(canvas.dataset.chart);
    const sym    = canvas.dataset.symbol;
    const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Expenses (' + sym + ')',
          data,
          backgroundColor: 'rgba(99,102,241,.15)',
          borderColor: '#6366f1',
          borderWidth: 2,
          borderRadius: 8,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            grid: { color: '#f3f4f6' },
            ticks: { callback: v => sym + v.toLocaleString() }
          },
          x: { grid: { display: false } }
        }
      }
    });
  }
}());
</script>
