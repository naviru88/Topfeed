<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$blogId = $_GET['id'] ?? null;

if (!$blogId) {
  header("Location: ../pages/personal.php");
  exit;
}

$blog = getBlogById($blogId);

// Validate blog exists
if (!$blog) {
  die("Blog not found.");
}

// Validate user owns the blog
if (!isBlogOwner($_SESSION['user_id'], $blogId)) {
  die("Unauthorized access. You can only delete your own blogs.");
}

// Delete the blog
deleteBlog($blogId);

// Optional: Delete the thumbnail file from uploads directory
if (!empty($blog['thumbnail'])) {
  $thumbnailPath = '../uploads/' . $blog['thumbnail'];
  if (file_exists($thumbnailPath)) {
    unlink($thumbnailPath);
  }
}

// Redirect to personal page with success message
$_SESSION['delete_success'] = "Blog deleted successfully!";
header("Location: ../pages/personal.php");
exit;
?>
