<?php
// MySQL connection for hosting providers that don't support PostgreSQL
$host = "localhost";  // Usually localhost on shared hosting
$dbname = "skyhawk_db";  // Change this to your actual database name from cPanel
$username = "your_username";  // Change this to your actual database username from cPanel
$password = "your_password";  // Change this to your actual database password from cPanel

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>