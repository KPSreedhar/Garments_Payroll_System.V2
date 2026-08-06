<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config.php';

$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $date = $_POST['date'] ?? '';
    $workerId = $_POST['worker'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if ($date === '' || $workerId === '' || !ctype_digit((string) $quantity) || (int) $quantity < 1) {
        $message = 'Please fill in all fields correctly.';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare('INSERT INTO work_entries (worker_id, entry_date, quantity) VALUES (?, ?, ?)');
        $stmt->bind_param('isi', $workerId, $date, $quantity);
        $stmt->execute();
        $stmt->close();
        $message = 'Work entry saved successfully.';
    }
}

$workers = $conn->query('SELECT id, name FROM workers ORDER BY name ASC');

$pageTitle = 'Submit Work Entry | Garment Payroll System';
$activePage = 'entry';
require __DIR__ . '/includes/header.php';
?>

<h1>Enter Daily Work Data</h1>

<?php if ($message): ?>
  <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form class="card-form" method="POST">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="date">Date</label>
    <input type="date" id="date" name="date" required value="<?= date('Y-m-d') ?>">
  </div>

  <div class="form-group">
    <label for="worker">Select Worker</label>
    <select id="worker" name="worker" required>
      <option value="">-- Select Worker --</option>
      <?php if ($workers->num_rows === 0): ?>
        <option value="" disabled>No workers yet — add one first</option>
      <?php else: while ($w = $workers->fetch_assoc()): ?>
        <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
      <?php endwhile; endif; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="quantity">Quantity (shifts/pieces)</label>
    <input type="number" id="quantity" name="quantity" min="1" required>
  </div>

  <button type="submit"><i class="fas fa-save"></i> Save Entry</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
