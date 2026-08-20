<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: " . BASE_PATH . "auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verifyCsrfToken();
  $newsletter = isset($_POST['newsletter']) ? 1 : 0;
  $blogNotifications = isset($_POST['blog_notifications']) ? 1 : 0;
  
  // Store in session (database columns not yet added)
  $_SESSION['newsletter'] = $newsletter;
  $_SESSION['blog_notifications'] = $blogNotifications;
  
  $_SESSION['settings_success'] = "Notification preferences updated successfully!";
}

header("Location: " . BASE_PATH . "pages/settings.php");
exit;