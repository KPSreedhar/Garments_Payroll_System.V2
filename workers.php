<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config.php';

$message = '';
$messageType = 'success';

// ---- Handle form submissions (add / update) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = trim($_POST['name'] ?? '');
    $paymentType = $_POST['paymentType'] ?? '';
    $rate = $_POST['rate'] ?? '';
    $workerId = $_POST['worker_id'] ?? '';

    if ($name === '' || !in_array($paymentType, ['shift', 'piece'], true) || !is_numeric($rate) || (float) $rate <= 0) {
        $message = 'Please fill in all fields with a valid rate.';
        $messageType = 'error';
    } else {
        if ($workerId !== '') {
            // Update existing worker
            $stmt = $conn->prepare('UPDATE workers SET name = ?, payment_type = ?, rate = ? WHERE id = ?');
            $stmt->bind_param('ssdi', $name, $paymentType, $rate, $workerId);
            $stmt->execute();
            $stmt->close();
            $message = 'Worker updated successfully.';
        } else {
            // Add new worker
            $stmt = $conn->prepare('INSERT INTO workers (name, payment_type, rate) VALUES (?, ?, ?)');
            $stmt->bind_param('ssd', $name, $paymentType, $rate);
            $stmt->execute();
            $stmt->close();
            $message = 'Worker added successfully.';
        }
    }
}

// ---- Handle delete ----
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM workers WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: workers.php?deleted=1');
    exit;
}
if (isset($_GET['deleted'])) {
    $message = 'Worker deleted.';
}

// ---- Load worker to edit, if requested ----
$editWorker = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare('SELECT id, name, payment_type, rate FROM workers WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editWorker = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ---- Load all workers ----
$workers = $conn->query('SELECT id, name, payment_type, rate FROM workers ORDER BY name ASC');

$pageTitle = 'Add Workers | Garment Payroll System';
$activePage = 'workers';
require __DIR__ . '/includes/header.php';
?>

<h1>Add New Worker</h1>

<?php if ($message): ?>
  <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form class="card-form" method="POST">
  <?= csrf_field() ?>
  <input type="hidden" name="worker_id" value="<?= $editWorker['id'] ?? '' ?>">

  <div class="form-group">
    <label for="name">Worker Name</label>
    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($editWorker['name'] ?? '') ?>">
  </div>

  <div class="form-group">
    <label for="paymentType">Payment Type</label>
    <select id="paymentType" name="paymentType" required>
      <option value="">-- Select --</option>
      <option value="shift" <?= (($editWorker['payment_type'] ?? '') === 'shift') ? 'selected' : '' ?>>Shift-Based</option>
      <option value="piece" <?= (($editWorker['payment_type'] ?? '') === 'piece') ? 'selected' : '' ?>>Piece-Based</option>
    </select>
  </div>

  <div class="form-group">
    <label for="rate">Rate (per shift or piece)</label>
    <input type="number" id="rate" name="rate" required min="0.01" step="0.01" value="<?= htmlspecialchars($editWorker['rate'] ?? '') ?>">
  </div>

  <div class="form-actions">
    <button type="submit"><?= $editWorker ? 'Update Worker' : 'Add Worker' ?></button>
    <?php if ($editWorker): ?>
      <a href="workers.php" class="btn btn-secondary" style="text-decoration:none;">Cancel</a>
    <?php endif; ?>
  </div>
</form>

<h2>Workers List</h2>
<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Payment Type</th>
        <th>Rate</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($workers->num_rows === 0): ?>
        <tr class="no-data"><td colspan="4">No workers yet. Add one above.</td></tr>
      <?php else: while ($w = $workers->fetch_assoc()): ?>
        <tr>
          <td data-label="Name"><?= htmlspecialchars($w['name']) ?></td>
          <td data-label="Payment Type"><?= $w['payment_type'] === 'shift' ? 'Shift-Based' : 'Piece-Based' ?></td>
          <td data-label="Rate">₹<?= number_format((float) $w['rate'], 2) ?></td>
          <td data-label="Actions">
            <div class="action-buttons">
              <a href="workers.php?edit=<?= $w['id'] ?>" class="btn" style="text-decoration:none;"><i class="fas fa-edit"></i></a>
              <a href="workers.php?delete=<?= $w['id'] ?>" class="btn btn-danger" style="text-decoration:none;"
                 onclick="return confirm('Delete <?= htmlspecialchars(addslashes($w['name'])) ?>? This will also delete their work entries.');">
                 <i class="fas fa-trash"></i>
              </a>
            </div>
          </td>
        </tr>
      <?php endwhile; endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
