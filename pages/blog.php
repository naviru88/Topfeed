<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: " . BASE_PATH . "pages/index.php");
  exit;
}

$blog = getBlogById((int)$_GET['id']);
if (!$blog) {
  echo "<h1>Blog not found.</h1><p><a href='" . BASE_PATH . "pages/index.php'>Go back home</a></p>";
  exit;
}

if (isset($_SESSION['user_id'])) {
  logViewHistory($_SESSION['user_id'], $blog['id']);
}

$themeClass = getThemeClass();
$csrfToken = generateCsrfToken();
$canEdit = isset($_SESSION['user_id']) && isBlogOwner($_SESSION['user_id'], $blog['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($blog['title']) ?> - Topfeed</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>assets/style.css">
  <style>
    .blog-content {
      font-size: 1.125rem;
      line-height: 1.8;
      color: var(--text-primary);
    }
    
    .blog-content h1, .blog-content h2, .blog-content h3 {
      margin-top: 1.5rem;
      margin-bottom: 1rem;
      font-weight: 700;
      line-height: 1.3;
    }
    .blog-content h1 { font-size: 2rem; }
    .blog-content h2 { font-size: 1.75rem; }
    .blog-content h3 { font-size: 1.5rem; }
    
    .blog-content p { 
      margin-bottom: 1rem; 
    }
    
    .blog-content ul, .blog-content ol {
      margin-left: 2rem;
      margin-bottom: 1rem;
    }
    
    .blog-content li {
      margin-bottom: 0.5rem;
    }
    
    .blog-content img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin: 1.5rem 0;
      display: block;
    }
    
    .blog-content a {
      color: var(--primary);
      text-decoration: underline;
    }
    
    .blog-content a:hover {
      color: var(--primary-dark);
    }
    
    .blog-content strong {
      font-weight: 700;
    }
    
    .blog-content em {
      font-style: italic;
    }
    
    .blog-content blockquote {
      border-left: 4px solid var(--primary);
      padding-left: 1rem;
      margin: 1.5rem 0;
      color: var(--text-secondary);
      font-style: italic;
    }
  </style>
</head>
<body class="<?= $themeClass ?>">
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <main>
    <div class="container">
      <article style="max-width: 800px; margin: 0 auto;">
        <div class="profile-section">
          <?php if (!empty($blog['thumbnail'])): ?>
            <img src="<?= BASE_PATH ?>uploads/<?= htmlspecialchars($blog['thumbnail']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" style="width: 100%; height: 400px; object-fit: cover; border-radius: 12px; margin-bottom: 2rem;">
          <?php endif; ?>
          
          <div style="margin-bottom: 2rem;">
            <?php if (!empty($blog['category'])): ?>
              <span style="display: inline-block; padding: 0.5rem 1rem; background: var(--primary); color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">
                <?= htmlspecialchars($blog['category']) ?>
              </span>
            <?php endif; ?>
            
            <h1 style="font-size: 2.5rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem;">
              <?= htmlspecialchars($blog['title']) ?>
            </h1>
            
            <div class="blog-meta" style="font-size: 1rem;">
              <span>👤 <strong><?= htmlspecialchars($blog['author']) ?></strong></span>
              <span>📅 <?= date('F d, Y', strtotime($blog['created_at'])) ?></span>
            </div>
          </div>
          
          <div class="blog-content">
            <?= sanitizeHtml($blog['content']) ?>
          </div>

          <?php if ($canEdit): ?>
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--border); display: flex; gap: 1rem;">
              <a href="<?= BASE_PATH ?>blog/update.php?id=<?= $blog['id'] ?>" class="btn-small btn-edit" style="padding: 0.75rem 1.5rem; font-size: 1rem;">✏️ Edit</a>
              <form method="POST" action="<?= BASE_PATH ?>blog/delete.php" onsubmit="return confirm('Are you sure you want to delete this blog?')">
                <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn-small btn-delete" style="padding: 0.75rem 1.5rem; font-size: 1rem;">🗑️ Delete</button>
              </form>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['user_id'])): ?>
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--border);">
              <form method="POST" action="<?= BASE_PATH ?>blog/save.php">
                <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn-primary" style="width: 100%;">💾 Save to Read Later</button>
              </form>
            </div>
          <?php else: ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: var(--background); border-radius: 12px; text-align: center;">
              <p style="margin-bottom: 1rem; color: var(--text-secondary);">Want to save this blog?</p>
              <a href="<?= BASE_PATH ?>auth/login.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Login to Save</a>
            </div>
          <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
          <a href="<?= BASE_PATH ?>pages/index.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">← Back to Home</a>
        </div>
      </article>
    </div>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
