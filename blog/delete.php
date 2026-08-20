<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: " . BASE_PATH . "auth/login.php");
  exit;
}

// POST-only deletion to prevent CSRF via link injection
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: " . BASE_PATH . "pages/personal.php");
  exit;
}

verifyCsrfToken();

$blogId = intval($_POST['blog_id'] ?? 0);

if ($blogId <= 0) {
  $_SESSION['settings_error'] = "Invalid blog ID.";
  header("Location: " . BASE_PATH . "pages/personal.php");
  exit;
}

$blog = getBlogById($blogId);

if (!$blog) {
  $_SESSION['settings_error'] = "Blog not found.";
  header("Location: " . BASE_PATH . "pages/personal.php");
  exit;
}

if (!isBlogOwner($_SESSION['user_id'], $blogId)) {
  $_SESSION['settings_error'] = "Unauthorized access.";
  header("Location: " . BASE_PATH . "pages/personal.php");
  exit;
}

// Delete the blog
if (deleteBlog($blogId)) {
  // Delete the thumbnail file
  if (!empty($blog['thumbnail'])) {
    $thumbnailPath = __DIR__ . '/../uploads/' . $blog['thumbnail'];
    if (file_exists($thumbnailPath)) {
      unlink($thumbnailPath);
    }
  }
  $_SESSION['delete_success'] = "Blog deleted successfully!";
} else {
  $_SESSION['settings_error'] = "Failed to delete blog.";
}

header("Location: " . BASE_PATH . "pages/personal.php");
exit;