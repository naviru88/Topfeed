<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'])) {
  try {
    $blogId = intval($_POST['blog_id']);
    
    // Validate blog ID
    if ($blogId <= 0) {
      throw new Exception('Invalid blog ID.');
    }
    
    // Save the blog
    saveBlog($_SESSION['user_id'], $blogId);
    
    // Set success message
    $_SESSION['save_success'] = "Blog saved successfully!";
    
  } catch (Exception $e) {
    // Set error message
    $_SESSION['save_error'] = $e->getMessage();
  }
  
  // Redirect back to the referring page or default to index
  $redirect = $_SERVER['HTTP_REFERER'] ?? '../pages/index.php';
  header("Location: $redirect");
  exit;
} else {
  header("Location: ../pages/index.php");
  exit;
}
?>
