<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$registerSuccess = $_SESSION['register_success'] ?? null;
unset($_SESSION['register_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrfToken();
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $user = loginUser($email, $password);
  if ($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    header("Location: " . BASE_PATH . "pages/index.php");
    exit;
  } else {
    $error = "Invalid credentials.";
  }
}
$themeClass = getThemeClass();
$csrfToken = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Topfeed</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>assets/style.css">
</head>
<body class="<?= $themeClass ?>">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Login to continue to Topfeed</p>
      </div>

      <?php if (isset($error)): ?>
        <div class="error-message">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($registerSuccess): ?>
        <div class="success-message">
          <?= htmlspecialchars($registerSuccess) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        
        <button type="submit" class="btn-primary">Login</button>
      </form>
      
      <div class="auth-footer">
        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
        <p><a href="<?= BASE_PATH ?>pages/index.php">Continue as Guest</a></p>
      </div>
    </div>
  </div>
</body>
</html>
