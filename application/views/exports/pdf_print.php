<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Expense Report</title>
<style>
  * { font-family: Arial, sans-serif; font-size: 12px; }
  body { margin: 20px; color: #1e1b4b; }
  h2 { color: #6366f1; margin-bottom: 4px; }
  .meta { color: #666; margin-bottom: 16px; font-size: 11px; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  th { background: #1e1b4b; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
  td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
  tr:nth-child(even) td { background: #f9fafb; }
  .total-row td { background: #ede9fe; font-weight: bold; color: #4f46e5; }
  .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; }
  @media print {
    @page { size: A4 landscape; margin: 15mm; }
    button { display: none; }
  }
</style>
</head>
<body>
<?php
$symbolMap = ['INR'=>'₹','USD'=>'$','EUR'=>'€','GBP'=>'£','AED'=>'د.إ','JPY'=>'¥','CAD'=>'CA$','AUD'=>'A$'];
$sym = $symbolMap[$displayCurrency] ?? $displayCurrency . ' ';
?>
<div style="display:flex;justify-content:space-between;align-items:start;border-bottom:2px solid #6366f1;padding-bottom:12px;margin-bottom:12px;">
  <div>
    <h2>💰 Expense Report</h2>
    <div class="meta">
      Generated: <?= date('F j, Y, g:ia') ?> &nbsp;|&nbsp;
      Currency: <?= $sym . $displayCurrency ?> &nbsp;|&nbsp;
      User: <?= htmlspecialchars($profile->name ?? '') ?>
    </div>
  </div>
  <button id="printBtn" style="background:#6366f1;color:#fff;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-weight:bold;">
    🖨 Print / Save as PDF
  </button>
  <script>document.getElementById('printBtn').addEventListener('click', function () { window.print(); });</script>
    🖨 Print / Save PDF
  </button>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Title</th>
      <th>Category</th>
      <th>Original</th>
      <th>Amount (<?= $displayCurrency ?>)</th>
      <th>Date</th>
      <th>Added By</th>
      <th>Note</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($expenses as $i => $e):
      $converted = $sym . number_format($e->amount * $rate, 2);
    ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td><?= htmlspecialchars($e->title) ?></td>
      <td><?= htmlspecialchars($e->category_name) ?></td>
      <td><?= ($e->currency_symbol ?? $e->currency) . number_format($e->amount, 2) ?> <?= $e->currency ?></td>
      <td><strong><?= $converted ?></strong></td>
      <td><?= date('M j, Y', strtotime($e->expense_date)) ?></td>
      <td><?= htmlspecialchars($e->added_by_name) ?></td>
      <td><?= htmlspecialchars($e->note ?? '') ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
      <td colspan="4" style="text-align:right;font-weight:bold;">TOTAL:</td>
      <td><strong><?= $sym . number_format($total, 2) ?></strong></td>
      <td colspan="3"></td>
    </tr>
  </tbody>
</table>

<div style="margin-top:20px;color:#666;font-size:10px;">
  <?= count($expenses) ?> expense(s) exported. All amounts shown in <?= $displayCurrency ?>.
</div>
</body>
</html>
