<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_phone'])) {
    header('Location: index.php');
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include DB connection (MySQLi version expected)
include("../config/db.php");

if (!$conn) {
    die("Database connection is not established.");
}

try {
    $name   = $_POST['name'] ?? '';
    $phone  = $_POST['phone'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $txn_id = $_POST['txn_id'] ?? '';

    if (empty($name) || empty($phone) || empty($amount) || empty($txn_id)) {
        throw new Exception("All fields are required.");
    }

    $name   = filter_var($name, FILTER_SANITIZE_STRING);
    $phone  = filter_var($phone, FILTER_SANITIZE_STRING);
    $amount = filter_var($amount, FILTER_SANITIZE_NUMBER_INT);
    $txn_id = filter_var($txn_id, FILTER_SANITIZE_STRING);

    if ($amount <= 0) {
        throw new Exception("Amount must be a positive number.");
    }

    // Prepare statement (PDO style)
    $stmt = $conn->prepare("INSERT INTO userwallet (user_name, phone, amount, txn_id, created_at) 
                            VALUES (?, ?, ?, ?, NOW())");

    if (!$stmt->execute([$name, $phone, $amount, $txn_id])) {
        throw new Exception("Execute failed");
    }

    header("Location: dashboard.php");
    exit();

} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo '<p>Redirecting back in 3 seconds...</p>';
    echo '<meta http-equiv="refresh" content="3;url=userwallet.php">';
}
?>
