<?php
session_start();
if (!isset($_SESSION['admin_phone'])) {
    header("Location: index.php");
    exit();
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $amount = floatval($_POST['amount']);
    $action = $_POST['action'];

    if ($amount <= 0) {
        die("Amount must be greater than zero.");
    }

    $stmt = $conn->prepare("SELECT amount FROM pilotwallet WHERE phone = ?");
    $stmt->execute([$phone]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO pilotwallet (phone, amount) VALUES (?, ?)");
            $stmt->execute([$phone, $amount]);
        } else {
            die("Wallet not found. Cannot subtract.");
        }
    } else {
        $current = floatval($result['amount']);

        if ($action === 'add') {
            $newAmount = $current + $amount;
        } elseif ($action === 'subtract') {
            if ($amount > $current) {
                die("Cannot subtract more than current balance.");
            }
            $newAmount = $current - $amount;
        } else {
            die("Invalid action.");
        }

        $stmt = $conn->prepare("UPDATE pilotwallet SET amount = ? WHERE phone = ?");
        $stmt->execute([$newAmount, $phone]);
    }

    header("Location: pilots.php");
    exit();
}
