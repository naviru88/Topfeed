<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

$categories = getCategories();
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? '';

    if (empty($title) || empty($content)) {
      throw new Exception("Title and content are required");
    }

    $thumbnail = null;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
      $thumbnail = uploadThumbnail($_FILES['thumbnail']);
    }

    $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, category, thumbnail) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $_SESSION['user_id'], $title, $content, $category, $thumbnail);
    
    if ($stmt->execute()) {
      $success = "Blog post created successfully!";
      header("Location: ../pages/personal.php");
      exit;
    } else {
      throw new Exception("Failed to create blog post");
    }
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Blog - Topfeed</title>
  <link rel="stylesheet" href="/Topfeed/assets/style.css">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <style>
    .create-container {
      max-width: 900px;
      margin: 2rem auto;
      padding: 2rem;
    }

    .create-form {
      background: var(--surface);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-header {
      margin-bottom: 2rem;
    }

    .form-header h1 {
      font-size: 2rem;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--text-primary);
      font-weight: 500;
    }

    .form-group input[type="text"],
    .form-group select {
      width: 100%;
      padding: 0.75rem;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .form-group input[type="text"]:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
    }

    .form-group input[type="file"] {
      padding: 0.5rem;
    }

    #editor-container {
      height: 400px;
      background: white;
      border-radius: 8px;
    }

    .ql-toolbar {
      border-radius: 8px 8px 0 0;
      background: #f8f9fa;
      border: 2px solid #e2e8f0 !important;
    }

    .ql-container {
      border-radius: 0 0 8px 8px;
      border: 2px solid #e2e8f0 !important;
      border-top: none !important;
      font-size: 16px;
    }

    /* Fix list alignment */
    .ql-editor ul,
    .ql-editor ol {
      padding-left: 1.5em;
    }

    .ql-editor li {
      padding-left: 0;
      text-align: left;
    }

    /* Font families in editor */
    .ql-font-arial {
      font-family: Arial, sans-serif;
    }
    .ql-font-comic-sans {
      font-family: 'Comic Sans MS', cursive;
    }
    .ql-font-courier {
      font-family: 'Courier New', monospace;
    }
    .ql-font-georgia {
      font-family: Georgia, serif;
    }
    .ql-font-helvetica {
      font-family: Helvetica, sans-serif;
    }
    .ql-font-lucida {
      font-family: 'Lucida Sans Unicode', sans-serif;
    }
    .ql-font-times {
      font-family: 'Times New Roman', serif;
    }
    .ql-font-verdana {
      font-family: Verdana, sans-serif;
    }

    /* Dropdown styling - Font picker */
    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="arial"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="arial"]::before {
      content: 'Arial';
      font-family: Arial, sans-serif !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="comic-sans"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="comic-sans"]::before {
      content: 'Comic Sans';
      font-family: 'Comic Sans MS', cursive !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="courier"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="courier"]::before {
      content: 'Courier';
      font-family: 'Courier New', monospace !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="georgia"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="georgia"]::before {
      content: 'Georgia';
      font-family: Georgia, serif !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="helvetica"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="helvetica"]::before {
      content: 'Helvetica';
      font-family: Helvetica, sans-serif !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="lucida"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="lucida"]::before {
      content: 'Lucida';
      font-family: 'Lucida Sans Unicode', sans-serif !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="times"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="times"]::before {
      content: 'Times New Roman';
      font-family: 'Times New Roman', serif !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="verdana"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="verdana"]::before {
      content: 'Verdana';
      font-family: Verdana, sans-serif !important;
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label:not([data-value])::before {
      content: 'Sans Serif';
      font-family: sans-serif !important;
    }

    /* Font size labels */
    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="10px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10px"]::before {
      content: '10px';
      font-size: 10px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12px"]::before {
      content: '12px';
      font-size: 12px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14px"]::before {
      content: '14px';
      font-size: 14px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="16px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16px"]::before {
      content: '16px';
      font-size: 16px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18px"]::before {
      content: '18px';
      font-size: 18px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="20px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="20px"]::before {
      content: '20px';
      font-size: 20px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24px"]::before {
      content: '24px';
      font-size: 24px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="32px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="32px"]::before {
      content: '32px';
      font-size: 32px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="42px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="42px"]::before {
      content: '42px';
      font-size: 42px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="54px"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="54px"]::before {
      content: '54px';
      font-size: 54px !important;
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before {
      content: 'Normal';
    }

    /* Undo/Redo button styling */
    .ql-snow .ql-toolbar button.ql-undo::before {
      content: '↶';
      font-size: 18px;
      font-weight: bold;
    }

    .ql-snow .ql-toolbar button.ql-redo::before {
      content: '↷';
      font-size: 18px;
      font-weight: bold;
    }

    .btn-group {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
      border: none;
      text-decoration: none;
      display: inline-block;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
      background: #e2e8f0;
      color: var(--text-primary);
    }

    .btn-secondary:hover {
      background: #cbd5e0;
    }

    .alert {
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
    }

    .alert-success {
      background: #c6f6d5;
      color: #22543d;
      border: 1px solid #9ae6b4;
    }

    .alert-error {
      background: #fed7d7;
      color: #742a2a;
      border: 1px solid #fc8181;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="create-container">
    <div class="create-form">
      <div class="form-header">
        <h1>✍️ Create New Blog</h1>
        <p style="color: var(--text-secondary);">Share your thoughts with the world</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Blog Title *</label>
          <input type="text" id="title" name="title" required placeholder="Enter your blog title...">
        </div>

        <div class="form-group">
          <label for="category">Category *</label>
          <select id="category" name="category" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $cat => $icon): ?>
              <option value="<?= htmlspecialchars($cat) ?>"><?= $icon ?> <?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="thumbnail">Cover Image (Optional)</label>
          <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        </div>

        <div class="form-group">
          <label>Blog Content *</label>
          <div id="editor-container"></div>
          <input type="hidden" name="content" id="content">
        </div>

        <div class="btn-group">
          <button type="submit" class="btn btn-primary">📤 Publish Blog</button>
          <a href="../pages/personal.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>

  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <script>
    // Register custom fonts
    var Font = Quill.import('formats/font');
    Font.whitelist = ['arial', 'comic-sans', 'courier', 'georgia', 'helvetica', 'lucida', 'times', 'verdana'];
    Quill.register(Font, true);

    // Register custom sizes
    var Size = Quill.import('attributors/style/size');
    Size.whitelist = ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '32px', '42px', '54px'];
    Quill.register(Size, true);

    // Custom undo/redo icons
    var icons = Quill.import('ui/icons');
    icons['undo'] = '↶';
    icons['redo'] = '↷';

    // Initialize Quill editor
    var quill = new Quill('#editor-container', {
      theme: 'snow',
      modules: {
        toolbar: {
          container: [
            [{ 'font': Font.whitelist }],
            [{ 'size': Size.whitelist }],
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['clean'],
            ['undo', 'redo']
          ],
          handlers: {
            undo: function() {
              this.quill.history.undo();
            },
            redo: function() {
              this.quill.history.redo();
            },
            image: imageHandler
          }
        },
        history: {
          delay: 1000,
          maxStack: 50,
          userOnly: true
        }
      },
      placeholder: 'Start writing your blog content...'
    });

    // Image upload handler
    function imageHandler() {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.click();

      input.onchange = async function() {
        const file = input.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await fetch('../blog/upload_image.php', {
              method: 'POST',
              body: formData
            });

            const data = await response.json();
            
            if (data.url) {
              const range = quill.getSelection(true);
              quill.insertEmbed(range.index, 'image', data.url);
              quill.setSelection(range.index + 1);
            } else {
              alert('Failed to upload image: ' + (data.error || 'Unknown error'));
            }
          } catch (error) {
            alert('Error uploading image: ' + error.message);
          }
        }
      };
    }

    // Save content before form submission
    document.querySelector('form').onsubmit = function() {
      document.getElementById('content').value = quill.root.innerHTML;
    };

    // Keyboard shortcuts for undo/redo
    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
        e.preventDefault();
        quill.history.undo();
      }
      if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.shiftKey && e.key === 'z'))) {
        e.preventDefault();
        quill.history.redo();
      }
    });
  </script>
</body>
</html>
