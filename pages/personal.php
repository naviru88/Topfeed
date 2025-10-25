<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$user = getUserById($_SESSION['user_id']);
$published = getUserBlogs($_SESSION['user_id']);
$saved = getSavedBlogs($_SESSION['user_id']);
$history = getViewHistory($_SESSION['user_id']);

$deleteSuccess = $_SESSION['delete_success'] ?? null;
unset($_SESSION['delete_success']);
$themeClass = getThemeClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Topfeed</title>
  <link rel="stylesheet" href="/Topfeed/assets/style.css">
</head>
<body class="<?= $themeClass ?>">
  <?php include '../includes/header.php'; ?>

  <main>
    <div class="container">
      <div class="page-header">
        <h1>Welcome back, <?= htmlspecialchars($user['username']) ?>! 👋</h1>
        <p>Manage your blogs and saved content</p>
      </div>

      <?php if ($deleteSuccess): ?>
        <div class="success-message">
          <?= htmlspecialchars($deleteSuccess) ?>
        </div>
      <?php endif; ?>

      <div class="profile-section">
        <h2>📧 Account Details</h2>
        <div style="padding: 1rem 0;">
          <p style="margin-bottom: 0.5rem;"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
          <p style="margin-bottom: 0.5rem;"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
          <p style="margin-bottom: 0.5rem;"><strong>Member since:</strong> <?= date('F d, Y', strtotime($user['created_at'])) ?></p>
        </div>
      </div>

      <div class="profile-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
          <h2>📝 Published Blogs (<?= count($published) ?>)</h2>
          <a href="../blog/create.php" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1rem; font-size: 0.875rem;">+ New Blog</a>
        </div>
        
        <?php if (empty($published)): ?>
          <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
            <p>You haven't published any blogs yet.</p>
            <a href="../blog/create.php" class="btn-primary" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Create Your First Blog</a>
          </div>
        <?php else: ?>
          <ul>
            <?php foreach ($published as $blog): ?>
              <li>
                <div class="blog-list-item">
                  <div style="flex: 1;">
                    <a href="blog.php?id=<?= $blog['id'] ?>" style="color: var(--text-primary); text-decoration: none; font-weight: 600;">
                      <?= htmlspecialchars($blog['title']) ?>
                    </a>
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
                      <?php if (!empty($blog['category'])): ?>
                        <span>📁 <?= htmlspecialchars($blog['category']) ?></span> • 
                      <?php endif; ?>
                      <span>📅 <?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
                    </div>
                  </div>
                  <div class="blog-list-actions">
                    <a href="../blog/update.php?id=<?= $blog['id'] ?>" class="btn-small btn-edit">Edit</a>
                    <a href="../blog/delete.php?id=<?= $blog['id'] ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="profile-section">
        <h2>💾 Saved Blogs (<?= count($saved) ?>)</h2>
        <?php if (empty($saved)): ?>
          <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
            <p>You haven't saved any blogs yet.</p>
            <a href="index.php" class="btn-primary" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Explore Blogs</a>
          </div>
        <?php else: ?>
          <ul>
            <?php foreach ($saved as $blog): ?>
              <li>
                <div class="blog-list-item">
                  <a href="blog.php?id=<?= $blog['id'] ?>" style="color: var(--text-primary); text-decoration: none; font-weight: 600; flex: 1;">
                    <?= htmlspecialchars($blog['title']) ?>
                  </a>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="profile-section">
        <h2>👁️ View History (<?= count($history) ?>)</h2>
        <?php if (empty($history)): ?>
          <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
            <p>No viewing history yet.</p>
          </div>
        <?php else: ?>
          <ul>
            <?php foreach ($history as $blog): ?>
              <li>
                <div class="blog-list-item">
                  <a href="blog.php?id=<?= $blog['id'] ?>" style="color: var(--text-primary); text-decoration: none; font-weight: 600; flex: 1;">
                    <?= htmlspecialchars($blog['title']) ?>
                  </a>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
