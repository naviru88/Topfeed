<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Testing database connection...<br>";

$host = 'localhost';
$dbname = 'topfeed';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful!<br>";
    
    // Check if blogs table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'blogs'");
    if ($stmt->rowCount() > 0) {
        echo "✅ 'blogs' table exists<br>";
    } else {
        echo "❌ 'blogs' table does NOT exist<br>";
    }
} catch(PDOException $e) {
    die("❌ Database error: " . $e->getMessage());
}
?>
