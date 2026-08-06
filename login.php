<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        // Prepared statement — no SQL injection possible here.
        $stmt = $conn->prepare('SELECT id, email, password_hash FROM admins WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        // password_verify() checks the plaintext password against the bcrypt
        // hash stored in the DB — the real password is never stored anywhere.
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: home.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | Garment Payroll System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-image">
        <div class="auth-image-content">
          <h2>Garment Payroll System</h2>
          <p>Streamline your garment factory payroll operations with our efficient management system</p>
        </div>
      </div>

      <div class="auth-form">
        <div class="logo">
          <i class="fas fa-tshirt"></i>
          <span class="logo-text">GarmentPay</span>
        </div>

        <h1>Welcome Back</h1>
        <p>Sign in to access your payroll dashboard</p>

        <?php if ($error): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <?= csrf_field() ?>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>

          <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Sign In</button>
        </form>

        <div class="demo-credentials">
          <h4>Default admin (change after first login):</h4>
          <p><strong>Email:</strong> sreedhar@gmail.com</p>
          <p><strong>Password:</strong> kpn1234</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
