<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $user = loginUser($email, $password);
  if ($user) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: ../pages/index.php");
    exit;
  } else {
    $error = "Invalid credentials.";
  }
}
$themeClass = getThemeClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Topfeed</title>
  <link rel="stylesheet" href="/Topfeed/assets/style.css">
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
      
      <form method="POST" class="auth-form">
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
        <p><a href="../pages/index.php">Continue as Guest</a></p>
      </div>
    </div>
  </div>
</body>
</html>
