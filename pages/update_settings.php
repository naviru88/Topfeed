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
  $currentPassword = $_POST['current_password'] ?? '';
  $newPassword = $_POST['new_password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';
  
  try {
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
      throw new Exception('All fields are required.');
    }
    
    if ($newPassword !== $confirmPassword) {
      throw new Exception('New passwords do not match.');
    }
    
    if (strlen($newPassword) < 6) {
      throw new Exception('Password must be at least 6 characters long.');
    }
    
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT password FROM user WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
      throw new Exception('Current password is incorrect.');
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();
    
    $_SESSION['settings_success'] = "Password updated successfully!";
  } catch (Exception $e) {
    $_SESSION['settings_error'] = $e->getMessage();
  }
}

header("Location: " . BASE_PATH . "pages/settings.php");
exit;