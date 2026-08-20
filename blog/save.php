<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: " . BASE_PATH . "auth/login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'])) {
  verifyCsrfToken();
  try {
    $blogId = intval($_POST['blog_id']);
    
    if ($blogId <= 0) {
      throw new Exception('Invalid blog ID.');
    }
    
    saveBlog($_SESSION['user_id'], $blogId);
    $_SESSION['save_success'] = "Blog saved successfully!";
  } catch (Exception $e) {
    $_SESSION['save_error'] = $e->getMessage();
  }
}

// Always redirect to a safe, known page (no HTTP_REFERER)
header("Location: " . BASE_PATH . "pages/index.php");
exit;