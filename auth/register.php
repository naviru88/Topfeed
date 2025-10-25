<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (registerUser($username, $email, $password)) {
    $_SESSION['register_success'] = "Registration successful! Please login.";
    header("Location: login.php");
    exit;
  } else {
    $error = "Registration failed. Email may already be in use.";
  }
}
$themeClass = getThemeClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Topfeed</title>
  <link rel="stylesheet" href="/Topfeed/assets/style.css">
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
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Choose a username" required>
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
