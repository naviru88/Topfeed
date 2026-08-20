<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Auto-detect base URL path (e.g. '/Topfeed/' or '/')
$rootDir = realpath(__DIR__ . '/..');
$docRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$basePath = substr($rootDir, strlen($docRoot));
define('BASE_PATH', rtrim($basePath, '/') . '/');

// Connect to database using .env credentials only
$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// --- CSRF Protection ---
function generateCsrfToken() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function verifyCsrfToken() {
  $token = $_POST['csrf_token'] ?? '';
  if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    die('Invalid or missing security token. Please go back and try again.');
  }
}

// --- HTML Sanitization (for Quill rich content) ---
function sanitizeHtml($html) {
  if (empty($html)) return '';

  $dom = new DOMDocument();
  libxml_use_internal_errors(true); // Wrap content in a container so multiple sibling elements
  $wrapped = '<div id="__sanitize_root__">' . $html . '</div>';
  $dom->loadHTML('<?xml encoding="UTF-8">' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();

  $xpath = new DOMXPath($dom);
  // Remove script, style, iframe, object, embed, form, input elements
  foreach (['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 'select', 'button', 'link', 'meta'] as $tag) {
    foreach ($xpath->query('//' . $tag) as $node) {
      $node->parentNode->removeChild($node);
    }
  }
  // Remove on* event attributes from all elements
  foreach ($xpath->query('//@*') as $attr) {
    if (strpos($attr->name, 'on') === 0) {
      $attr->ownerElement->removeAttributeNode($attr);
    }
  }
  // Remove javascript: hrefs
  foreach ($xpath->query('//a[@href]') as $a) {
    if (preg_match('/^\s*javascript:/i', $a->getAttribute('href'))) {
      $a->removeAttribute('href');
    }
  }

  $wrapperNodes = $xpath->query('//div[@id="__sanitize_root__"]');
  if ($wrapperNodes->length === 0) {
    return '';
  }
  $wrapper = $wrapperNodes->item(0);

  $result = '';
  foreach ($wrapper->childNodes as $child) {
    $result .= $dom->saveHTML($child);
  }
  return trim($result);
}
?>
