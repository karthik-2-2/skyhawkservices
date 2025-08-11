<?php
// MySQL connection for hosting providers that don't support PostgreSQL
$host = "localhost";  // Usually localhost on shared hosting
$dbname = "your_database_name";  // Get this from your hosting cPanel
$username = "your_db_username";  // Get this from your hosting cPanel  
$password = "your_db_password";  // Get this from your hosting cPanel

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
