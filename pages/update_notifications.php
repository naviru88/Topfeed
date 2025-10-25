<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newsletter = isset($_POST['newsletter']) ? 1 : 0;
  $blogNotifications = isset($_POST['blog_notifications']) ? 1 : 0;
  
  $userId = $_SESSION['user_id'];
  
  // Store preferences in session for now
  $_SESSION['newsletter'] = $newsletter;
  $_SESSION['blog_notifications'] = $blogNotifications;
  
  // Optionally, store in database (requires adding columns to user table)
  // $stmt = $conn->prepare("UPDATE user SET newsletter = ?, blog_notifications = ? WHERE id = ?");
  // $stmt->bind_param("iii", $newsletter, $blogNotifications, $userId);
  // $stmt->execute();
  
  $_SESSION['settings_success'] = "Notification preferences updated successfully!";
}

header("Location: settings.php");
exit;
?>
