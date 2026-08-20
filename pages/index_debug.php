<?php
// Enable all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Step 1: Script started<br>";

session_start();
echo "Step 2: Session started<br>";

// Check if files exist before including
$db_file = '../includes/db.php';
$func_file = '../includes/functions.php';

if (!file_exists($db_file)) {
    die("ERROR: db.php not found at: " . realpath($db_file));
}
echo "Step 3: db.php exists<br>";

if (!file_exists($func_file)) {
    die("ERROR: functions.php not found at: " . realpath($func_file));
}
echo "Step 4: functions.php exists<br>";

require_once $db_file;
echo "Step 5: db.php included<br>";

require_once $func_file;
echo "Step 6: functions.php included<br>";

// Test if function exists
if (function_exists('getAllBlogs')) {
    echo "Step 7: getAllBlogs() function exists<br>";
} else {
    die("ERROR: getAllBlogs() function not found");
}

$blogs = getAllBlogs();
echo "Step 8: Blogs fetched successfully<br>";

echo "ALL GOOD! No errors found.";
?>
