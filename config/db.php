<?php
// Supabase PostgreSQL connection
$host = 'db.gnrnkefylcjpgcbzjxif.supabase.co';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres';
$password = 'skyhawksservices123db'; // IMPORTANT: Get this from your Supabase project settings

try {
    // The connection string is now "pgsql:" for PostgreSQL
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>