<?php
// Supabase PostgreSQL connection using the Transaction Pooler
$host = 'aws-0-ap-south-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.gnrnkefylcjpgcbzjxif'; // Note: The user is different for the pooler
$password = 'skyhawksservices123db'; // IMPORTANT: Get this from your Supabase project settings

try {
    // The connection string is "pgsql:" for PostgreSQL
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>