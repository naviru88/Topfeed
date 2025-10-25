<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
  $theme = $_POST['theme'];
  
  // Validate theme value
  $allowedThemes = ['light', 'dark', 'auto'];
  if (in_array($theme, $allowedThemes)) {
    // Store theme preference in session
    $_SESSION['theme'] = $theme;
    
    // Optionally, store in database
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE user SET theme = ? WHERE id = ?");
    $stmt->bind_param("si", $theme, $userId);
    $stmt->execute();
    
    $_SESSION['theme_success'] = "Theme updated successfully!";
  } else {
    $_SESSION['theme_error'] = "Invalid theme selection.";
  }
}

header("Location: settings.php");
exit;
?>
