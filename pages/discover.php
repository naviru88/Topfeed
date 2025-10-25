<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$categories = getCategories();
$selectedCategory = $_GET['category'] ?? null;
$blogs = $selectedCategory ? getBlogsByCategory($selectedCategory) : [];
$themeClass = getThemeClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Discover - Topfeed</title>
   <link rel="stylesheet" href="/Topfeed/assets/style.css">
</head>
<body class="<?= $themeClass ?>">
  <?php include '../includes/header.php'; ?>

  <main>
    <div class="container">
      <div class="page-header">
        <h1>Discover by Category</h1>
        <p>Find blogs that match your interests</p>
      </div>

      <div class="category-grid">
        <?php foreach ($categories as $category => $icon): ?>
          <a href="?category=<?= urlencode($category) ?>" class="category-card">
            <div class="category-icon"><?= $icon ?></div>
            <div class="category-name"><?= htmlspecialchars($category) ?></div>
          </a>
        <?php endforeach; ?>
      </div>

      <?php if ($selectedCategory): ?>
        <div style="margin-top: 3rem;">
          <div class="page-header">
            <h2 style="font-size: 2rem;"><?= htmlspecialchars($selectedCategory) ?> Blogs</h2>
            <a href="discover.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">← Back to all categories</a>
          </div>
          
          <?php if (empty($blogs)): ?>
            <div style="text-align: center; padding: 3rem 0;">
              <p style="color: var(--text-secondary); font-size: 1.125rem;">No blogs in this category yet.</p>
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../blog/create.php" class="btn-primary" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Be the first to create one!</a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="blog-grid">
              <?php foreach ($blogs as $blog): ?>
                <div class="blog-tile">
                  <?php if (!empty($blog['thumbnail'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($blog['thumbnail']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="blog-tile-image">
                  <?php else: ?>
                    <div class="blog-tile-image" style="display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                      <?= $categories[$selectedCategory] ?>
                    </div>
                  <?php endif; ?>
                  
                  <div class="blog-tile-content">
                    <h2><?= htmlspecialchars($blog['title']) ?></h2>
                    
                    <div class="blog-meta">
                      <span>👤 <?= htmlspecialchars($blog['author']) ?></span>
                      <span>📅 <?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
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
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
