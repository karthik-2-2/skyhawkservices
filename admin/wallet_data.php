<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['admin_phone'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$response = [
    'stats' => [],
    'chart_data' => [],
    'pending_requests' => []
];

// --- 1. Fetch Core Statistics ---
try {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) AS total FROM userordersuccess");
    $stmt->execute();
    $total_revenue_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['stats']['total_revenue'] = $total_revenue_result ? $total_revenue_result['total'] : 0;

    // Check if pilot_earnings table exists, if not use pilot_wallet table
    $stmt = $conn->prepare("SELECT table_name FROM information_schema.tables WHERE table_name = 'pilot_earnings'");
    $stmt->execute();
    $check_pilot_earnings = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($check_pilot_earnings) {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM pilot_earnings");
        $stmt->execute();
        $total_payout_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['stats']['total_payout'] = $total_payout_result ? $total_payout_result['total'] : 0;
    } else {
        // Try pilot_wallet table instead
        $stmt = $conn->prepare("SELECT table_name FROM information_schema.tables WHERE table_name = 'pilot_wallet'");
        $stmt->execute();
        $check_pilot_wallet = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($check_pilot_wallet) {
            $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM pilot_wallet WHERE amount > 0");
            $stmt->execute();
            $total_payout_result = $stmt->fetch(PDO::FETCH_ASSOC);
            $response['stats']['total_payout'] = $total_payout_result ? $total_payout_result['total'] : 0;
        } else {
            $response['stats']['total_payout'] = 0;
        }
    }

    $response['stats']['net_profit'] = $response['stats']['total_revenue'] - $response['stats']['total_payout'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM userwallet");
    $stmt->execute();
    $pending_count_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['stats']['pending_requests'] = $pending_count_result ? $pending_count_result['total'] : 0;
} catch (Exception $e) {
    $response['stats']['total_revenue'] = 0;
    $response['stats']['total_payout'] = 0;
    $response['stats']['net_profit'] = 0;
    $response['stats']['pending_requests'] = 0;
}

// --- 2. Fetch Data for Chart (Revenue last 30 days) ---
try {
    $chart_result = $conn->query("
        SELECT 
            DATE(created_at) as date, 
            SUM(total_price) as daily_revenue
        FROM userordersuccess
        WHERE created_at >= CURDATE() - INTERVAL 30 DAY
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $chart_data = [];
    if ($chart_result) {
        while($row = $chart_result->fetch_assoc()) {
            $chart_data[] = $row;
        }
    }
    $response['chart_data'] = $chart_data;
} catch (Exception $e) {
    $response['chart_data'] = [];
}

// --- 3. Fetch Pending Wallet Top-up Requests ---
try {
    $pending_result = $conn->query("SELECT id, user_name, phone, amount, txn_id, created_at FROM userwallet ORDER BY created_at DESC");
    $pending_requests = [];
    if ($pending_result) {
        while($row = $pending_result->fetch_assoc()) {
            $pending_requests[] = $row;
        }
    }
    $response['pending_requests'] = $pending_requests;
} catch (Exception $e) {
    $response['pending_requests'] = [];
}


echo json_encode($response);
$conn->close();
?>