<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config.php';

$weekStart = $_GET['weekStart'] ?? '';
$weekEnd = $_GET['weekEnd'] ?? '';
$error = '';
$report = [];
$grandTotal = 0.0;

if (isset($_GET['generate'])) {
    if ($weekStart === '' || $weekEnd === '') {
        $error = 'Please select both Start and End dates.';
    } elseif ($weekStart > $weekEnd) {
        $error = 'Start date must be before or equal to End date.';
    } else {
        // All aggregation happens in SQL — no PHP loops needed.
        $stmt = $conn->prepare(
            'SELECT w.id, w.name, w.payment_type, w.rate,
                    COALESCE(SUM(we.quantity), 0) AS total_quantity,
                    COALESCE(SUM(we.quantity), 0) * w.rate AS total_salary
             FROM workers w
             LEFT JOIN work_entries we
                    ON we.worker_id = w.id AND we.entry_date BETWEEN ? AND ?
             GROUP BY w.id, w.name, w.payment_type, w.rate
             ORDER BY w.name ASC'
        );
        $stmt->bind_param('ss', $weekStart, $weekEnd);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $report[] = $row;
            $grandTotal += (float) $row['total_salary'];
        }
        $stmt->close();
    }
}

$pageTitle = 'Weekly Salary Report | Garment Payroll System';
$activePage = 'report';
require __DIR__ . '/includes/header.php';
?>

<h1>Weekly Salary Report</h1>

<?php if ($error): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form class="report-controls" method="GET">
  <div>
    <label for="weekStart">Start Date</label>
    <input type="date" id="weekStart" name="weekStart" value="<?= htmlspecialchars($weekStart) ?>">
  </div>
  <div>
    <label for="weekEnd">End Date</label>
    <input type="date" id="weekEnd" name="weekEnd" value="<?= htmlspecialchars($weekEnd) ?>">
  </div>
  <button type="submit" name="generate" value="1"><i class="fas fa-calculator"></i> Generate Report</button>
</form>

<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Worker Name</th>
        <th>Type</th>
        <th>Rate</th>
        <th>Total Quantity</th>
        <th>Total Salary</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!isset($_GET['generate'])): ?>
        <tr class="no-data"><td colspan="5">Select a date range and generate the report.</td></tr>
      <?php elseif (empty($report) && !$error): ?>
        <tr class="no-data"><td colspan="5">No workers found.</td></tr>
      <?php else: foreach ($report as $r): ?>
        <tr>
          <td data-label="Worker"><?= htmlspecialchars($r['name']) ?></td>
          <td data-label="Type"><?= $r['payment_type'] === 'shift' ? 'Shift-Based' : 'Piece-Based' ?></td>
          <td data-label="Rate">₹<?= number_format((float) $r['rate'], 2) ?></td>
          <td data-label="Total Quantity"><?= (int) $r['total_quantity'] ?></td>
          <td data-label="Total Salary">₹<?= number_format((float) $r['total_salary'], 2) ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php if (isset($_GET['generate']) && !empty($report)): ?>
  <div class="report-total">Grand Total: ₹<?= number_format($grandTotal, 2) ?></div>
<?php endif; ?>

<script>
  document.getElementById("weekStart").addEventListener("change", () => {
    const startVal = document.getElementById("weekStart").value;
    const endInput = document.getElementById("weekEnd");
    if (startVal && !endInput.value) {
      const start = new Date(startVal);
      const end = new Date(start);
      end.setDate(start.getDate() + 6);
      endInput.value = end.toISOString().split("T")[0];
    }
  });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
