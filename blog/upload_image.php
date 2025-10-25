<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}

// Check if file was uploaded
if (!isset($_FILES['image'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'No file uploaded']);
  exit;
}

try {
  $filename = uploadThumbnail($_FILES['image']);
  
  // Get the base URL dynamically
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $baseUrl = $protocol . '://' . $host;
  
  // Calculate path from blog folder to uploads
  $url = '../uploads/' . $filename;
  
  echo json_encode([
    'success' => true,
    'url' => $url,
    'filename' => $filename
  ]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
