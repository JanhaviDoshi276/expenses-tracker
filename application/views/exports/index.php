<?php
$monthNames  = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
$currentYear = date('Y');
$exportBase  = site_url('export');
?>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <!-- Header banner -->
    <div class="card shadow-sm mb-4" style="border-radius:16px;border:none;background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;">
      <div class="card-body p-4 d-flex align-items-center gap-3">
        <div style="width:56px;height:56px;background:rgba(255,255,255,.2);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
          <i class="fa-solid fa-file-export"></i>
        </div>
        <div>
          <h5 class="mb-0 fw-700">Export Expenses</h5>
          <p class="mb-0 opacity-75" style="font-size:.88rem;">Download your expense data as PDF or CSV</p>
        </div>
      </div>
    </div>

    <!-- Filter card -->
    <div class="card shadow-sm" style="border-radius:14px;border:none;">
      <div class="card-header bg-white border-bottom py-3 px-4">
        <h6 class="mb-0 fw-700"><i class="fa-solid fa-sliders me-2" style="color:#6366f1;"></i>Filter &amp; Options</h6>
      </div>
      <div class="card-body p-4">
        <div class="row g-3 mb-4">
          <div class="col-sm-6">
            <label class="form-label">Category</label>
            <select id="exp_category" class="form-select">
              <option value="">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Month</label>
            <select id="exp_month" class="form-select">
              <option value="">All Months</option>
              <?php foreach ($monthNames as $n => $name): ?>
                <option value="<?= $n ?>" <?= (date('n') == $n) ? 'selected' : '' ?>><?= $name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Year</label>
            <select id="exp_year" class="form-select">
              <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                <option value="<?= $y ?>" <?= ($y == $currentYear) ? 'selected' : '' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Display Currency</label>
            <select id="exp_display_currency" class="form-select">
              <?php foreach ($currencies as $c): ?>
                <option value="<?= $c->code ?>"><?= htmlspecialchars($c->symbol) ?> <?= htmlspecialchars($c->code) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Search Keyword</label>
            <input type="text" id="exp_search" class="form-control" placeholder="Optional…">
          </div>
        </div>
        <hr class="my-3">
        <p class="fw-600 text-dark mb-3" style="font-size:.88rem;">Choose export format:</p>
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:2px dashed #bbf7d0;">
              <div style="font-size:2rem;margin-bottom:.5rem;">📊</div>
              <h6 class="fw-700 mb-1" style="color:#065f46;">CSV</h6>
              <p class="text-muted mb-3" style="font-size:.8rem;">Open in Excel, Google Sheets, etc.</p>
              <button type="button" class="btn btn-sm btn-success px-4 btn-do-export" data-format="csv" style="border-radius:8px;">
                <i class="fa-solid fa-download me-1"></i>Download CSV
              </button>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-3 rounded-3 text-center" style="background:#fff7ed;border:2px dashed #fed7aa;">
              <div style="font-size:2rem;margin-bottom:.5rem;">📄</div>
              <h6 class="fw-700 mb-1" style="color:#92400e;">PDF</h6>
              <p class="text-muted mb-3" style="font-size:.8rem;">Print-ready formatted report</p>
              <button type="button" class="btn btn-sm btn-warning px-4 text-dark btn-do-export" data-format="pdf" style="border-radius:8px;">
                <i class="fa-solid fa-file-pdf me-1"></i>Download PDF
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const exportBase = '<?= addslashes($exportBase) ?>';

  document.querySelectorAll('.btn-do-export').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const format = this.dataset.format;
      const params = new URLSearchParams();
      const cat    = document.getElementById('exp_category').value;
      const month  = document.getElementById('exp_month').value;
      const year   = document.getElementById('exp_year').value;
      const search = document.getElementById('exp_search').value;
      const dc     = document.getElementById('exp_display_currency').value;
      if (cat)    params.set('category_id', cat);
      if (month)  params.set('month', month);
      if (year)   params.set('year', year);
      if (search) params.set('search', search);
      if (dc)     params.set('display_currency', dc);
      window.location.href = exportBase + '/' + format + '?' + params.toString();
    });
  });
}());
</script>
