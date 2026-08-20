<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}

if (!isset($_FILES['image'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'No file uploaded']);
  exit;
}

try {
  $filename = uploadThumbnail($_FILES['image']);
  
  // Return absolute URL path (works from any page)
  $url = BASE_PATH . 'uploads/' . $filename;
  
  echo json_encode([
    'success' => true,
    'url' => $url,
    'filename' => $filename
  ]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}