<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// FIX: Changed the session check to the correct admin-specific variable
if (!isset($_SESSION['admin_phone'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$response = [
    'stats' => [],
    'charts' => [
        'ordersByDay' => [],
        'serviceDistribution' => [],
        'monthlyRevenue' => [],
        'pilotPerformance' => []
    ],
    'pending_actions' => [
        'payment_verification' => [],
        'pending_orders' => []
    ],
    'recent_activity' => []
];

try {
    // --- 1. Optimize Core Statistics with Combined Queries ---
    
    // Get all basic counts in one query for better performance
    $statsQuery = "
        SELECT 
            (SELECT COUNT(*) FROM \"user\") as user_count,
            (SELECT COUNT(*) FROM pilot) as pilot_count,
            (SELECT COUNT(*) FROM userordersuccess) as completed_count,
            (SELECT COUNT(*) FROM userordercancel) as cancelled_count,
            (SELECT COUNT(*) FROM orders) as total_orders,
            (SELECT COALESCE(SUM(total_price), 0) FROM userordersuccess) as wallet_balance
    ";
    $stmt = $conn->prepare($statsQuery);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $response['stats']['users'] = (int)$stats['user_count'];
    $response['stats']['pilots'] = (int)$stats['pilot_count'];
    $response['stats']['completed_orders'] = (int)$stats['completed_count'];
    $response['stats']['cancelled_orders'] = (int)$stats['cancelled_count'];
    $response['stats']['total_orders'] = (int)$stats['total_orders'];
    $response['stats']['wallet_balance'] = (float)$stats['wallet_balance'];

    // Quick check for order_status column existence and get pending orders
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Pending Admin Approval'");
        $stmt->execute();
        $pendingResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pending_orders'] = (int)$pendingResult['total'];
        
        // Get verification pending count
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Payment Verification Pending'");
        $stmt->execute();
        $verificationResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pending_verification'] = (int)$verificationResult['total'];
        
        // Get in-progress orders
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed', 'Cancellation Requested')");
        $stmt->execute();
        $inProgressResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['stats']['inprogress_orders'] = (int)$inProgressResult['total'];
        
    } catch (Exception $e) {
        // Fallback if order_status column doesn't exist
        $response['stats']['pending_orders'] = 0;
        $response['stats']['pending_verification'] = 0;
        $response['stats']['inprogress_orders'] = 0;
    }

    // Quick pilot earnings check
    try {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM pilot_earnings");
        $stmt->execute();
        $earningsResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pilot_earnings'] = (float)$earningsResult['total'];
    } catch (Exception $e) {
        $response['stats']['pilot_earnings'] = 0;
    }

    // --- 2. Optimized Chart Data ---
    
    // Orders by day - simplified query
    try {
        $ordersByDayResult = $conn->prepare("
            SELECT DATE(booking_date) as order_day, COUNT(*) as order_count
            FROM userordersuccess
            WHERE booking_date >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY DATE(booking_date) ORDER BY order_day ASC
        ");
        $ordersByDayResult->execute();
        $response['charts']['ordersByDay'] = $ordersByDayResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback to created_at if booking_date doesn't exist
        try {
            $ordersByDayResult = $conn->prepare("
                SELECT DATE(created_at) as order_day, COUNT(*) as order_count
                FROM userordersuccess
                WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'
                GROUP BY DATE(created_at) ORDER BY order_day ASC
            ");
            $ordersByDayResult->execute();
            $response['charts']['ordersByDay'] = $ordersByDayResult->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e2) {
            $response['charts']['ordersByDay'] = [];
        }
    }

    // Service distribution - simple query
    try {
        $serviceDistResult = $conn->prepare("SELECT service_type, COUNT(*) as count FROM userordersuccess GROUP BY service_type LIMIT 10");
        $serviceDistResult->execute();
        $response['charts']['serviceDistribution'] = $serviceDistResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response['charts']['serviceDistribution'] = [];
    }

    // Monthly revenue - optimized
    try {
        $monthlyRevenueResult = $conn->prepare("
            SELECT 
                TO_CHAR(booking_date, 'YYYY-MM') as month,
                SUM(total_price) as revenue,
                COUNT(*) as orders
            FROM userordersuccess 
            WHERE booking_date >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY TO_CHAR(booking_date, 'YYYY-MM')
            ORDER BY month ASC
            LIMIT 6
        ");
        $monthlyRevenueResult->execute();
        $response['charts']['monthlyRevenue'] = $monthlyRevenueResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response['charts']['monthlyRevenue'] = [];
    }

    // --- 3. Quick Pending Actions ---
    
    // Payment verification requests - simplified
    try {
        $paymentVerificationResult = $conn->prepare("
            SELECT id, name, service_type, total_price
            FROM orders 
            WHERE order_status = 'Payment Verification Pending'
            ORDER BY id DESC 
            LIMIT 3
        ");
        $paymentVerificationResult->execute();
        $response['pending_actions']['payment_verification'] = $paymentVerificationResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response['pending_actions']['payment_verification'] = [];
    }

    // Recent pending orders
    try {
        $pendingOrdersResult = $conn->prepare("
            SELECT id, name, service_type 
            FROM orders 
            WHERE order_status = 'Pending Admin Approval'
            ORDER BY id DESC 
            LIMIT 3
        ");
        $pendingOrdersResult->execute();
        $response['pending_actions']['pending_orders'] = $pendingOrdersResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response['pending_actions']['pending_orders'] = [];
    }

    // In-progress orders for activity
    try {
        $inProgressOrdersResult = $conn->prepare("
            SELECT id, name, service_type, order_status 
            FROM orders 
            WHERE order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed') 
            ORDER BY id DESC 
            LIMIT 3
        ");
        $inProgressOrdersResult->execute();
        $response['pending_actions']['inprogress_orders'] = $inProgressOrdersResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response['pending_actions']['inprogress_orders'] = [];
    }

    // --- 4. Simplified Recent Activity ---
    try {
        $recentActivityResult = $conn->prepare("
            SELECT 'order_completed' as type, name as title, service_type as details, created_at as timestamp
            FROM userordersuccess 
            WHERE created_at >= CURRENT_DATE - INTERVAL '3 days'
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $recentActivityResult->execute();
        $response['recent_activity'] = $recentActivityResult->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response['recent_activity'] = [];
    }

} catch (Exception $e) {
    // Return minimal error for faster debugging
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit();
}

echo json_encode($response);
?>