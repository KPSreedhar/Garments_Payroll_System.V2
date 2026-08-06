<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config.php';

$message = '';
$messageType = 'success';

// ---- Handle inline update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $entryId = (int) ($_POST['entry_id'] ?? 0);
    $date = $_POST['date'] ?? '';
    $workerId = $_POST['worker'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if (!$entryId || $date === '' || $workerId === '' || !ctype_digit((string) $quantity) || (int) $quantity < 1) {
        $message = 'Please fill in all fields correctly.';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare('UPDATE work_entries SET worker_id = ?, entry_date = ?, quantity = ? WHERE id = ?');
        $stmt->bind_param('isii', $workerId, $date, $quantity, $entryId);
        $stmt->execute();
        $stmt->close();
        header('Location: manage-entries.php?updated=1');
        exit;
    }
}

// ---- Handle delete ----
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM work_entries WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage-entries.php?deleted=1');
    exit;
}
if (isset($_GET['deleted'])) { $message = 'Entry deleted.'; }
if (isset($_GET['updated'])) { $message = 'Entry updated.'; }

// ---- Load entry to edit, if requested ----
$editEntry = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare('SELECT id, worker_id, entry_date, quantity FROM work_entries WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editEntry = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$workersForSelect = $conn->query('SELECT id, name FROM workers ORDER BY name ASC');

$entries = $conn->query(
    'SELECT we.id, we.entry_date, we.quantity, w.name AS worker_name
     FROM work_entries we
     JOIN workers w ON w.id = we.worker_id
     ORDER BY we.entry_date DESC, we.id DESC'
);

$pageTitle = 'Manage Work Entries | Garment Payroll System';
$activePage = 'manage';
require __DIR__ . '/includes/header.php';
?>

<h1>Manage Work Entries</h1>

<?php if ($message): ?>
  <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($editEntry): ?>
<form class="card-form" method="POST">
  <?= csrf_field() ?>
  <input type="hidden" name="entry_id" value="<?= $editEntry['id'] ?>">
  <h2 style="margin-bottom:1rem;">Edit Entry</h2>

  <div class="form-group">
    <label for="date">Date</label>
    <input type="date" id="date" name="date" required value="<?= htmlspecialchars($editEntry['entry_date']) ?>">
  </div>

  <div class="form-group">
    <label for="worker">Worker</label>
    <select id="worker" name="worker" required>
      <?php $workersForSelect->data_seek(0); while ($w = $workersForSelect->fetch_assoc()): ?>
        <option value="<?= $w['id'] ?>" <?= $w['id'] == $editEntry['worker_id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="quantity">Quantity</label>
    <input type="number" id="quantity" name="quantity" min="1" required value="<?= (int) $editEntry['quantity'] ?>">
  </div>

  <div class="form-actions">
    <button type="submit">Update Entry</button>
    <a href="manage-entries.php" class="btn btn-secondary" style="text-decoration:none;">Cancel</a>
  </div>
</form>
<?php endif; ?>

<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Worker</th>
        <th>Quantity</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($entries->num_rows === 0): ?>
        <tr class="no-data"><td colspan="4">No work entries available. Add entries through the Submit Work page.</td></tr>
      <?php else: while ($e = $entries->fetch_assoc()): ?>
        <tr>
          <td data-label="Date"><?= htmlspecialchars($e['entry_date']) ?></td>
          <td data-label="Worker"><?= htmlspecialchars($e['worker_name']) ?></td>
          <td data-label="Quantity"><?= (int) $e['quantity'] ?></td>
          <td data-label="Actions">
            <div class="action-buttons">
              <a href="manage-entries.php?edit=<?= $e['id'] ?>" class="btn" style="text-decoration:none;"><i class="fas fa-edit"></i></a>
              <a href="manage-entries.php?delete=<?= $e['id'] ?>" class="btn btn-danger" style="text-decoration:none;"
                 onclick="return confirm('Delete this entry?');"><i class="fas fa-trash"></i></a>
            </div>
          </td>
        </tr>
      <?php endwhile; endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
