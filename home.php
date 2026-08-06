<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config.php';

// --- Real-time stats, pulled straight from the DB ---
$workerCount = (int) $conn->query('SELECT COUNT(*) AS c FROM workers')->fetch_assoc()['c'];

$todayEntries = (int) $conn->query(
    "SELECT COUNT(*) AS c FROM work_entries WHERE entry_date = CURDATE()"
)->fetch_assoc()['c'];

// This month's payroll: sum(quantity * rate) for entries in the current month.
$monthRow = $conn->query(
    "SELECT COALESCE(SUM(we.quantity * w.rate), 0) AS total
     FROM work_entries we
     JOIN workers w ON w.id = we.worker_id
     WHERE MONTH(we.entry_date) = MONTH(CURDATE()) AND YEAR(we.entry_date) = YEAR(CURDATE())"
)->fetch_assoc();
$monthPayroll = (float) $monthRow['total'];

$recentEntries = (int) $conn->query(
    "SELECT COUNT(*) AS c FROM work_entries WHERE entry_date >= CURDATE() - INTERVAL 7 DAY"
)->fetch_assoc()['c'];

$hour = (int) date('G');
$greetingText = $hour < 12 ? 'Good Morning ☀️' : ($hour < 18 ? 'Good Afternoon 🌤️' : 'Good Evening 🌙');
$adminEmail = $_SESSION['admin_email'] ?? 'Admin';

$pageTitle = 'Dashboard | Garment Payroll System';
$activePage = 'home';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="hero-content">
    <h1>Streamline Your Garment Factory Payroll</h1>
    <p>Efficient, accurate, and hassle-free payroll management, now backed by a real MySQL database instead of browser storage.</p>
    <div style="display:flex; gap:1rem; flex-wrap:wrap;">
      <a href="workers.php" class="btn"><i class="fas fa-play"></i> Get Started</a>
      <a href="#project-info" class="btn btn-secondary"><i class="fas fa-info-circle"></i> Learn More</a>
    </div>
  </div>
  <div class="hero-image">
    <img src="images/payroll.jpg" alt="Garment factory workers">
  </div>
</section>

<section class="greeting">
  <div class="greeting-header">
    <h1 id="greeting"><i class="fas fa-user-tie"></i> <?= htmlspecialchars($greetingText) ?>, <?= htmlspecialchars($adminEmail) ?>!</h1>
    <div class="current-datetime">
      <div class="time" id="currentTime"></div>
      <div id="currentDate"></div>
    </div>
  </div>

  <div class="stats-bar">
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-users"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?= $workerCount ?></div>
        <div class="stat-label">Total Workers</div>
      </div>
    </div>
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?= $todayEntries ?></div>
        <div class="stat-label">Today's Entries</div>
      </div>
    </div>
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-clock"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?= $recentEntries ?></div>
        <div class="stat-label">Entries (Last 7 Days)</div>
      </div>
    </div>
    <div class="stat-item">
      <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
      <div class="stat-content">
        <div class="stat-value">₹<?= number_format($monthPayroll, 2) ?></div>
        <div class="stat-label">This Month's Payroll</div>
      </div>
    </div>
  </div>
</section>

<section>
  <h2><i class="fas fa-tachometer-alt"></i> Quick Actions</h2>
  <div class="dashboard-grid">
    <a href="workers.php" class="card">
      <h2><i class="fas fa-user-plus"></i> Add Workers</h2>
      <p>Register new workers with their payment type and rate.</p>
      <span class="btn">Go to Page</span>
    </a>
    <a href="work-entry.php" class="card">
      <h2><i class="fas fa-tasks"></i> Submit Work</h2>
      <p>Record daily production, shift, or piece-rate work.</p>
      <span class="btn">Go to Page</span>
    </a>
    <a href="salary-report.php" class="card">
      <h2><i class="fas fa-file-invoice-dollar"></i> Salary Report</h2>
      <p>Generate weekly salary reports by date range.</p>
      <span class="btn">Go to Page</span>
    </a>
    <a href="manage-entries.php" class="card">
      <h2><i class="fas fa-edit"></i> Manage Entries</h2>
      <p>Review, edit, or delete work records.</p>
      <span class="btn">Go to Page</span>
    </a>
  </div>
</section>

<section class="project-info" id="project-info">
  <h2><i class="fas fa-info-circle"></i> About This Project</h2>
  <div class="project-info-content">
    <div class="project-info-text">
      <p>The Garment Payroll System helps small to medium-sized garment factories manage worker compensation, whether paid by shift or by piece.</p>
      <p>Key problems solved:</p>
      <ul style="margin: 1rem 0 1rem 1.5rem; color: var(--gray);">
        <li>Complex pay structures with multiple calculation methods</li>
        <li>Time-consuming manual payroll calculations</li>
        <li>Difficulty tracking piece-rate work accurately</li>
        <li>Lack of transparency in payroll processing</li>
      </ul>
    </div>
    <div class="project-info-image">
      <img src="images/Garment_pes.png" alt="Garment factory production">
    </div>
  </div>
  <div class="features-list">
    <div class="feature-item">
      <h3><i class="fas fa-calculator"></i> Flexible Pay Calculations</h3>
      <p>Supports shift-based and piece-rate payments.</p>
    </div>
    <div class="feature-item">
      <h3><i class="fas fa-mobile-alt"></i> Mobile-Friendly</h3>
      <p>Accessible on any device, on the factory floor.</p>
    </div>
    <div class="feature-item">
      <h3><i class="fas fa-shield-alt"></i> Real Login &amp; Data Security</h3>
      <p>Hashed passwords, server-side sessions, and a real MySQL database.</p>
    </div>
    <div class="feature-item">
      <h3><i class="fas fa-database"></i> Real-Time DB Handling</h3>
      <p>Every page reads and writes straight to MySQL — no more browser-only storage.</p>
    </div>
  </div>
</section>

<script>
  function updateDateTime() {
    const now = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
    document.getElementById("currentTime").textContent = now.toLocaleTimeString('en-US', timeOptions);
    document.getElementById("currentDate").textContent = now.toLocaleDateString('en-US', dateOptions);
  }
  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
