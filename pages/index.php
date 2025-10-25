<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$blogs = getAllBlogs();

$saveSuccess = $_SESSION['save_success'] ?? null;
$saveError = $_SESSION['save_error'] ?? null;
unset($_SESSION['save_success']);
unset($_SESSION['save_error']);

$themeClass = getThemeClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Topfeed - Discover Amazing Blogs</title>
  <link rel="stylesheet" href="/Topfeed/assets/style.css">
</head>
<body class="<?= $themeClass ?>">
  <?php include '../includes/header.php'; ?>

  <main>
    <div class="container">
      <div class="page-header">
        <h1>Discover Amazing Stories</h1>
        <p>Explore blogs from creators around the world</p>
      </div>
      
      <?php if ($saveSuccess): ?>
        <div class="success-message">
          <?= htmlspecialchars($saveSuccess) ?>
        </div>
      <?php endif; ?>
      
      <?php if ($saveError): ?>
        <div class="error-message">
          <?= htmlspecialchars($saveError) ?>
        </div>
      <?php endif; ?>
      
      <div class="blog-grid">
        <?php if (empty($blogs)): ?>
          <div style="grid-column: 1/-1; text-align: center; padding: 4rem 0;">
            <h2>No blogs yet</h2>
            <p style="color: var(--text-secondary); margin: 1rem 0;">Be the first to create a blog and share your story!</p>
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="../blog/create.php" class="btn-primary" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Create Your First Blog</a>
            <?php else: ?>
              <a href="../auth/register.php" class="btn-primary" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Sign Up to Start Blogging</a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php foreach ($blogs as $blog): ?>
            <div class="blog-tile">
              <?php if (!empty($blog['thumbnail'])): ?>
                <img src="../uploads/<?= htmlspecialchars($blog['thumbnail']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="blog-tile-image">
              <?php else: ?>
                <div class="blog-tile-image" style="display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                  📝
                </div>
              <?php endif; ?>
              
              <div class="blog-tile-content">
                <h2><?= htmlspecialchars($blog['title']) ?></h2>
                
                <div class="blog-meta">
                  <span>👤 <?= htmlspecialchars($blog['author']) ?></span>
                  <?php if (!empty($blog['category'])): ?>
                    <span>📁 <?= htmlspecialchars($blog['category']) ?></span>
                  <?php endif; ?>
                </div>
                
                <p class="blog-excerpt"><?= substr(strip_tags($blog['content']), 0, 120) ?>...</p>
                
                <div class="blog-actions">
                  <a href="blog.php?id=<?= $blog['id'] ?>" class="btn-read">Read More</a>
                  <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="../blog/save.php" style="margin: 0;">
                      <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                      <button type="submit" class="btn-save">💾</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
