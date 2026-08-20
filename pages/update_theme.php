<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: " . BASE_PATH . "auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
  verifyCsrfToken();
  $theme = $_POST['theme'];
  
  $allowedThemes = ['light', 'dark', 'auto'];
  if (in_array($theme, $allowedThemes)) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE user SET theme = ? WHERE id = ?");
    $stmt->bind_param("si", $theme, $userId);
    $stmt->execute();
    
    $_SESSION['theme_success'] = "Theme updated successfully!";
  } else {
    $_SESSION['theme_error'] = "Invalid theme selection.";
  }
}

header("Location: " . BASE_PATH . "pages/settings.php");
exit;