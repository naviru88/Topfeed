<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$user = getUserById($_SESSION['user_id']);

// Get and clear session messages
$settingsSuccess = $_SESSION['settings_success'] ?? null;
$settingsError = $_SESSION['settings_error'] ?? null;
$themeSuccess = $_SESSION['theme_success'] ?? null;
$themeError = $_SESSION['theme_error'] ?? null;
unset($_SESSION['settings_success']);
unset($_SESSION['settings_error']);
unset($_SESSION['theme_success']);
unset($_SESSION['theme_error']);

$themeClass = getThemeClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - Topfeed</title>
  <link rel="stylesheet" href="/Topfeed/assets/style.css">
</head>
<body class="<?= $themeClass ?>">
  <?php include '../includes/header.php'; ?>

  <main>
    <div class="container">
      <div class="page-header">
        <h1>⚙️ Settings</h1>
        <p>Manage your account preferences</p>
      </div>

      <?php if ($settingsSuccess || $themeSuccess): ?>
        <div class="success-message">
          <?= htmlspecialchars($settingsSuccess ?? $themeSuccess) ?>
        </div>
      <?php endif; ?>
      
      <?php if ($settingsError || $themeError): ?>
        <div class="error-message">
          <?= htmlspecialchars($settingsError ?? $themeError) ?>
        </div>
      <?php endif; ?>

      <div style="max-width: 600px; margin: 0 auto;">
        <div class="profile-section">
          <h2>🔐 Change Password</h2>
          <form method="POST" action="update_settings.php" class="auth-form">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>
            </div>
            
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required minlength="6">
            </div>
            
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
            </div>
            
            <button type="submit" class="btn-primary">Update Password</button>
          </form>
        </div>

        <div class="profile-section">
          <h2>🎨 Appearance</h2>
          <form method="POST" action="update_theme.php" class="auth-form">
            <div class="form-group">
              <label for="theme">Theme</label>
              <select id="theme" name="theme">
                <option value="light" <?= ($user['theme'] ?? 'light') === 'light' ? 'selected' : '' ?>>☀️ Light Mode</option>
                <option value="dark" <?= ($user['theme'] ?? 'light') === 'dark' ? 'selected' : '' ?>>🌙 Dark Mode</option>
                <option value="auto" <?= ($user['theme'] ?? 'light') === 'auto' ? 'selected' : '' ?>>🔄 Auto (System)</option>
              </select>
              <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">
                Current theme: <strong><?= ucfirst($user['theme'] ?? 'light') ?></strong>
              </p>
            </div>
            
            <button type="submit" class="btn-primary">Apply Theme</button>
          </form>
        </div>

        <div class="profile-section">
          <h2>📧 Email Preferences</h2>
          <form method="POST" action="update_notifications.php" class="auth-form">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border);">
              <input type="checkbox" id="newsletter" name="newsletter" style="width: 20px; height: 20px;">
              <label for="newsletter" style="margin: 0; cursor: pointer;">Receive newsletter and updates</label>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border);">
              <input type="checkbox" id="blog_notifications" name="blog_notifications" style="width: 20px; height: 20px;">
              <label for="blog_notifications" style="margin: 0; cursor: pointer;">Notify when someone saves my blogs</label>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 1rem;">Save Preferences</button>
          </form>
        </div>

        <div class="profile-section" style="border: 2px solid var(--error);">
          <h2 style="color: var(--error);">⚠️ Danger Zone</h2>
          <p style="color: var(--text-secondary); margin-bottom: 1rem;">
            Once you delete your account, there is no going back. Please be certain.
          </p>
          <button onclick="if(confirm('Are you sure you want to delete your account? This action cannot be undone!')) window.location.href='delete_account.php';" style="background: var(--error); padding: 0.75rem 1.5rem; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
            Delete Account
          </button>
        </div>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
