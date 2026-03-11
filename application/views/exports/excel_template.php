<?php
// This view is streamed directly with Excel MIME type
$monthNames = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
$filterLabel = [];
if (!empty($filters['month'])) $filterLabel[] = $monthNames[(int)$filters['month']];
if (!empty($filters['year']))  $filterLabel[] = $filters['year'];
if (!empty($filters['search'])) $filterLabel[] = 'keyword: ' . $filters['search'];
$periodStr = $filterLabel ? implode(', ', $filterLabel) : 'All Time';
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>
<x:ExcelWorksheet><x:Name>Expenses</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>
</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
<style>
  body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
  table { border-collapse: collapse; width: 100%; }
  th, td { border: 1px solid #d1d5db; padding: 7px 10px; }
  .title-row td { background: #4338ca; color: #fff; font-size: 14pt; font-weight: bold; border: none; }
  .meta-row td { background: #ede9fe; color: #4338ca; font-size: 9pt; border: none; }
  .head-row th { background: #6366f1; color: #fff; font-weight: bold; text-align: left; }
  .even { background: #f9fafb; }
  .total-row td { background: #f3f4f6; font-weight: bold; }
  .amount { text-align: right; font-weight: 600; color: #4338ca; }
  .total-amount { text-align: right; font-weight: bold; font-size: 12pt; color: #4338ca; }
</style>
</head>
<body>
<table>
  <!-- Title -->
  <tr class="title-row"><td colspan="6">💸 Daily Expense Tracker — Export</td></tr>
  <tr class="meta-row"><td colspan="6">Period: <?= htmlspecialchars($periodStr) ?> &nbsp;|&nbsp; Generated: <?= date('d M Y, H:i') ?> &nbsp;|&nbsp; User: <?= htmlspecialchars($profile->name) ?></td></tr>
  <tr><td colspan="6"></td></tr>

  <!-- Headers -->
  <tr class="head-row">
    <th>#</th>
    <th>Date</th>
    <th>Title</th>
    <th>Category</th>
    <th>Note</th>
    <th>Amount (<?= htmlspecialchars($symbol) ?>)</th>
  </tr>

  <!-- Rows -->
  <?php if ($expenses): $i = 1; foreach ($expenses as $e): ?>
  <tr class="<?= ($i % 2 === 0) ? 'even' : '' ?>">
    <td><?= $i++ ?></td>
    <td><?= date('d M Y', strtotime($e->expense_date)) ?></td>
    <td><?= htmlspecialchars($e->title) ?></td>
    <td><?= htmlspecialchars($e->category_name) ?></td>
    <td><?= htmlspecialchars($e->note ?? '') ?></td>
    <td class="amount"><?= $symbol ?><?= number_format($e->amount, 2) ?></td>
  </tr>
  <?php endforeach; else: ?>
  <tr><td colspan="6" style="text-align:center;color:#6b7280;">No expenses found for selected filters.</td></tr>
  <?php endif; ?>

  <!-- Total -->
  <tr class="total-row">
    <td colspan="5" style="text-align:right;font-weight:bold;">TOTAL</td>
    <td class="total-amount"><?= $symbol ?><?= number_format($total, 2) ?></td>
  </tr>

  <tr><td colspan="6"></td></tr>
  <tr><td colspan="6" style="font-size:8pt;color:#9ca3af;border:none;">Exported from ExpenseTracker &copy; <?= date('Y') ?></td></tr>
</table>
</body>
</html>
