<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect logged-in users away from register
if (isset($_SESSION['user_id'])) {
  header("Location: " . BASE_PATH . "pages/index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrfToken();
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (strlen($username) < 2 || strlen($username) > 100) {
    $error = "Username must be between 2 and 100 characters.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } elseif (registerUser($username, $email, $password)) {
    $_SESSION['register_success'] = "Registration successful! Please login.";
    header("Location: " . BASE_PATH . "auth/login.php");
    exit;
  } else {
    $error = "Registration failed. Email may already be in use.";
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
  <title>Register - Topfeed</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>assets/style.css">
</head>
<body class="<?= $themeClass ?>">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Join Topfeed</h1>
        <p>Create your account to start blogging</p>
      </div>
      
      <?php if (isset($error)): ?>
        <div class="error-message">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Choose a username" required minlength="2" maxlength="100">
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Create a password" required minlength="6">
        </div>
        
        <button type="submit" class="btn-primary">Sign Up</button>
      </form>
      
      <div class="auth-footer">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </div>
  </div>
</body>
</html>
