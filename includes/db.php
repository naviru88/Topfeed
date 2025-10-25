<?php
require_once('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

<?php
// InfinityFree Database Configuration
$host = 'sql303.infinityfree.com';
$dbname = 'if0_40222143_topfeed_db';
$username = 'if0_40222143';
$password = 'Ndb2222#';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
