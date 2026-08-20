<?php
function registerUser($username, $email, $password) {
  global $conn;
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, 'user')");
  $stmt->bind_param("sss", $username, $email, $hash);
  return $stmt->execute();
}

function loginUser($email, $password) {
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  if ($user && password_verify($password, $user['password'])) {
    return $user;
  }
  return false;
}

function getAllBlogs() {
  global $conn;
  $result = $conn->query("SELECT blogPost.*, user.username AS author FROM blogPost JOIN user ON blogPost.user_id = user.id ORDER BY created_at DESC");
  return $result->fetch_all(MYSQLI_ASSOC);
}

function getBlogById($id) {
  global $conn;
  $stmt = $conn->prepare("SELECT blogPost.*, user.username AS author FROM blogPost JOIN user ON blogPost.user_id = user.id WHERE blogPost.id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function createBlog($userId, $title, $content, $category, $thumbnail) {
  global $conn;
  $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, category, thumbnail) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $userId, $title, $content, $category, $thumbnail);
  return $stmt->execute();
}

function updateBlog($id, $title, $content, $category, $thumbnail) {
  global $conn;
  $stmt = $conn->prepare("UPDATE blogPost SET title = ?, content = ?, category = ?, thumbnail = ?, updated_at = NOW() WHERE id = ?");
  $stmt->bind_param("ssssi", $title, $content, $category, $thumbnail, $id);
  return $stmt->execute();
}

function deleteBlog($id) {
  global $conn;
  $stmt = $conn->prepare("DELETE FROM blogPost WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}

function uploadThumbnail($file) {
  if (!isset($file['error']) || is_array($file['error'])) {
    throw new RuntimeException('Invalid file upload parameters.');
  }

  switch ($file['error']) {
    case UPLOAD_ERR_OK:
      break;
    case UPLOAD_ERR_NO_FILE:
      throw new RuntimeException('No file was uploaded.');
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE:
      throw new RuntimeException('File size exceeds limit.');
    default:
      throw new RuntimeException('Unknown upload error.');
  }

  if ($file['size'] > 5000000) {
    throw new RuntimeException('File size exceeds 5MB limit.');
  }

  $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mimeType = $finfo->file($file['tmp_name']);
  
  if (!in_array($mimeType, $allowedTypes)) {
    throw new RuntimeException('Invalid file type. Only JPEG, PNG, GIF, and WEBP are allowed.');
  }

  $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
  $filename = uniqid('blog_', true) . '.' . $extension;
  $uploadDir = __DIR__ . '/../uploads/';
  $target = $uploadDir . $filename;

  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  if (!move_uploaded_file($file['tmp_name'], $target)) {
    throw new RuntimeException('Failed to move uploaded file.');
  }

  return $filename;
}

function logViewHistory($userId, $blogId) {
  global $conn;
  $stmt = $conn->prepare("INSERT INTO viewHistory (user_id, blog_id, viewed_at) VALUES (?, ?, NOW())");
  $stmt->bind_param("ii", $userId, $blogId);
  $stmt->execute();
}

function getUserById($id) {
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getUserBlogs($userId) {
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM blogPost WHERE user_id = ? ORDER BY created_at DESC");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getSavedBlogs($userId) {
  global $conn;
  $stmt = $conn->prepare("SELECT blogPost.* FROM savedBlogs JOIN blogPost ON savedBlogs.blog_id = blogPost.id WHERE savedBlogs.user_id = ? ORDER BY savedBlogs.saved_at DESC");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getViewHistory($userId) {
  global $conn;
  $stmt = $conn->prepare("SELECT DISTINCT blogPost.*, MAX(viewHistory.viewed_at) as last_viewed FROM viewHistory JOIN blogPost ON viewHistory.blog_id = blogPost.id WHERE viewHistory.user_id = ? GROUP BY blogPost.id ORDER BY last_viewed DESC");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function saveBlog($userId, $blogId) {
  global $conn;
  
  $stmt = $conn->prepare("SELECT id FROM blogPost WHERE id = ?");
  $stmt->bind_param("i", $blogId);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows === 0) {
    throw new RuntimeException('Blog post does not exist.');
  }
  
  $stmt = $conn->prepare("INSERT IGNORE INTO savedBlogs (user_id, blog_id, saved_at) VALUES (?, ?, NOW())");
  $stmt->bind_param("ii", $userId, $blogId);
  return $stmt->execute();
}

function isBlogOwner($userId, $blogId) {
  global $conn;
  $stmt = $conn->prepare("SELECT user_id FROM blogPost WHERE id = ?");
  $stmt->bind_param("i", $blogId);
  $stmt->execute();
  $result = $stmt->get_result();
  $blog = $result->fetch_assoc();
  
  return $blog && $blog['user_id'] == $userId;
}

function getCategories() {
  return [
    'Technology' => '💻',
    'Travel' => '✈️',
    'Food' => '🍔',
    'Health & Fitness' => '💪',
    'Fashion & Beauty' => '👗',
    'Business & Finance' => '💼',
    'Entertainment' => '🎬',
    'Sports' => '⚽',
    'Education' => '📚',
    'Lifestyle' => '🌟',
    'Science' => '🔬',
    'Photography' => '📷',
    'Music' => '🎵',
    'Art & Design' => '🎨',
    'Gaming' => '🎮',
    'Automotive' => '🚗',
    'Real Estate' => '🏠',
    'DIY & Crafts' => '🛠️',
    'Environment' => '🌍',
    'Other' => '📌'
  ];
}

function getBlogsByCategory($category) {
  global $conn;
  $stmt = $conn->prepare("SELECT blogPost.*, user.username AS author FROM blogPost JOIN user ON blogPost.user_id = user.id WHERE blogPost.category = ? ORDER BY created_at DESC");
  $stmt->bind_param("s", $category);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getUserTheme($userId) {
  global $conn;
  $stmt = $conn->prepare("SELECT theme FROM user WHERE id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  return $user['theme'] ?? 'light';
}

function getThemeClass() {
  if (isset($_SESSION['user_id'])) {
    $theme = getUserTheme($_SESSION['user_id']);
    if ($theme === 'dark') {
      return 'dark';
    } elseif ($theme === 'auto') {
      return 'auto';
    }
  }
  return '';
}

function getBasePath() {
  return BASE_PATH;
}
?>
