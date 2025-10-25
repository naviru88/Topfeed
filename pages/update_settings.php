<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $currentPassword = $_POST['current_password'] ?? '';
  $newPassword = $_POST['new_password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';
  
  try {
    // Validate inputs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
      throw new Exception('All fields are required.');
    }
    
    if ($newPassword !== $confirmPassword) {
      throw new Exception('New passwords do not match.');
    }
    
    if (strlen($newPassword) < 6) {
      throw new Exception('Password must be at least 6 characters long.');
    }
    
    // Get current user
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT password FROM user WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
      throw new Exception('Current password is incorrect.');
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();
    
    $_SESSION['settings_success'] = "Password updated successfully!";
    
  } catch (Exception $e) {
    $_SESSION['settings_error'] = $e->getMessage();
  }
}

header("Location: settings.php");
exit;
?>
