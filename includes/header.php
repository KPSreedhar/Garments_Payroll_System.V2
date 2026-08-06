<?php
/**
 * Shared page header: <head>, nav bar.
 * Expects $pageTitle (string) and optionally $activePage (string, one of
 * 'home' | 'workers' | 'entry' | 'manage' | 'report') to be set before including.
 */
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'Garment Payroll System') ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav class="navbar" role="navigation" aria-label="Primary navigation">
    <div class="nav-container">
      <a href="home.php" class="nav-title-link">
        <h1 class="nav-title"><i class="fas fa-tshirt"></i> Garment Payroll System</h1>
      </a>

      <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
      </button>

      <ul class="nav-links" id="navLinks">
        <li><a href="home.php" class="<?= $activePage === 'home' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="workers.php" class="<?= $activePage === 'workers' ? 'active' : '' ?>"><i class="fas fa-user-plus"></i> Add Workers</a></li>
        <li><a href="work-entry.php" class="<?= $activePage === 'entry' ? 'active' : '' ?>"><i class="fas fa-tasks"></i> Submit Work</a></li>
        <li><a href="manage-entries.php" class="<?= $activePage === 'manage' ? 'active' : '' ?>"><i class="fas fa-edit"></i> Manage Entries</a></li>
        <li><a href="salary-report.php" class="<?= $activePage === 'report' ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i> Salary Report</a></li>
        <li><button class="btn-logout" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</button></li>
      </ul>
    </div>
  </nav>

  <main class="container">
