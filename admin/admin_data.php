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
    // --- 1. Fetch Core Statistics ---
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM \"user\"");
    $stmt->execute();
    $userCountResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['stats']['users'] = $userCountResult ? $userCountResult['total'] : 0;

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM pilot");
    $stmt->execute();
    $pilotCountResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['stats']['pilots'] = $pilotCountResult ? $pilotCountResult['total'] : 0;

    // Check if orders table has order_status column, filter for actually pending orders
    $stmt = $conn->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'orders' AND column_name = 'order_status'");
    $stmt->execute();
    $checkOrderStatusColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($checkOrderStatusColumn) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Pending Admin Approval'");
        $stmt->execute();
        $pendingOrdersCountResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pending_orders'] = $pendingOrdersCountResult ? $pendingOrdersCountResult['total'] : 0;
    } else {
        // If no order_status column, count orders not in success/cancel tables
        $pendingOrdersCountResult = $conn->query("
            SELECT COUNT(*) AS total FROM orders o 
            WHERE o.id NOT IN (SELECT order_id FROM userordersuccess WHERE order_id IS NOT NULL) 
            AND o.id NOT IN (SELECT order_id FROM userordercancel WHERE order_id IS NOT NULL)
        ");
        $response['stats']['pending_orders'] = $pendingOrdersCountResult ? $pendingOrdersCountResult->fetch()['total'] : 0;
    }

    $completedOrdersCountResult = $conn->query("SELECT COUNT(*) AS total FROM userordersuccess");
    $response['stats']['completed_orders'] = $completedOrdersCountResult ? $completedOrdersCountResult->fetch()['total'] : 0;

    $cancelledOrdersCountResult = $conn->query("SELECT COUNT(*) AS total FROM userordercancel");
    $response['stats']['cancelled_orders'] = $cancelledOrdersCountResult ? $cancelledOrdersCountResult->fetch()['total'] : 0;

    $walletBalanceResult = $conn->query("SELECT SUM(total_price) AS balance FROM userordersuccess");
    $response['stats']['wallet_balance'] = $walletBalanceResult ? ($walletBalanceResult->fetch()['balance'] ?? 0) : 0;

    // Additional stats for enhanced dashboard
    $totalOrdersResult = $conn->query("SELECT COUNT(*) AS total FROM orders");
    $response['stats']['total_orders'] = $totalOrdersResult ? $totalOrdersResult->fetch()['total'] : 0;

    // Check if pilot_earnings table exists
    $tableCheck = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'pilot_earnings'");
    if ($tableCheck && $tableCheck->rowCount() > 0) {
        $pilotEarningsResult = $conn->query("SELECT SUM(amount) AS total FROM pilot_earnings");
        $response['stats']['pilot_earnings'] = $pilotEarningsResult ? ($pilotEarningsResult->fetch()['total'] ?? 0) : 0;
    } else {
        $response['stats']['pilot_earnings'] = 0;
    }

    // QR Payment verification stats - Count orders with payment screenshots (pending verification)
    $checkPaymentColumn = $conn->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'orders' AND column_name = 'payment_screenshot'");
    if ($checkPaymentColumn && $checkPaymentColumn->rowCount() > 0) {
        $pendingVerificationResult = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE payment_screenshot IS NOT NULL AND (order_status = 'Payment Verification Pending' OR order_status = 'Pending Admin Approval')");
        $response['stats']['pending_verification'] = $pendingVerificationResult ? $pendingVerificationResult->fetch()['total'] : 0;
    } else {
        $response['stats']['pending_verification'] = 0;
    }

    // In-Progress Orders stats - Count orders that are currently being worked on
    $checkOrderStatusColumn2 = $conn->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'orders' AND column_name = 'order_status'");
    if ($checkOrderStatusColumn2 && $checkOrderStatusColumn2->rowCount() > 0) {
        $inProgressOrdersResult = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed', 'Cancellation Requested')");
        $response['stats']['inprogress_orders'] = $inProgressOrdersResult ? $inProgressOrdersResult->fetch()['total'] : 0;
    } else {
        $response['stats']['inprogress_orders'] = 0;
    }

    // --- 2. Fetch Data for Charts ---
    // Orders by day with date column check
    $checkDateColumn = $conn->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'userordersuccess' AND column_name = 'booking_date'");
    if ($checkDateColumn && $checkDateColumn->rowCount() > 0) {
        $ordersByDayResult = $conn->query("
            SELECT DATE(booking_date) as order_day, COUNT(*) as order_count
            FROM userordersuccess
            WHERE booking_date >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY DATE(booking_date) ORDER BY order_day ASC
        ");
        if ($ordersByDayResult) {
            while($row = $ordersByDayResult->fetch()) {
                $response['charts']['ordersByDay'][] = $row;
            }
        }
    } else {
        // Use created_at or a default if booking_date doesn't exist
        $ordersByDayResult = $conn->query("
            SELECT DATE(created_at) as order_day, COUNT(*) as order_count
            FROM userordersuccess
            WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY DATE(created_at) ORDER BY order_day ASC
        ");
        if ($ordersByDayResult) {
            while($row = $ordersByDayResult->fetch()) {
                $response['charts']['ordersByDay'][] = $row;
            }
        }
    }

    // Service distribution
    $serviceDistResult = $conn->query("SELECT service_type, COUNT(*) as count FROM userordersuccess GROUP BY service_type");
    if ($serviceDistResult) {
        while($row = $serviceDistResult->fetch()) {
            $response['charts']['serviceDistribution'][] = $row;
        }
    }

    // Monthly revenue chart data
    if ($checkDateColumn && $checkDateColumn->rowCount() > 0) {
        $monthlyRevenueResult = $conn->query("
            SELECT 
                TO_CHAR(booking_date, 'YYYY-MM') as month,
                SUM(total_price) as revenue,
                COUNT(*) as orders
            FROM userordersuccess 
            WHERE booking_date >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY TO_CHAR(booking_date, 'YYYY-MM')
            ORDER BY month ASC
        ");
    } else {
        $monthlyRevenueResult = $conn->query("
            SELECT 
                TO_CHAR(created_at, 'YYYY-MM') as month,
                SUM(total_price) as revenue,
                COUNT(*) as orders
            FROM userordersuccess 
            WHERE created_at >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY TO_CHAR(created_at, 'YYYY-MM')
            ORDER BY month ASC
        ");
    
    if ($monthlyRevenueResult) {
        while($row = $monthlyRevenueResult->fetch()) {
            $response['charts']['monthlyRevenue'][] = $row;
        }
    }

    // Pilot performance data - only if pilot_earnings table exists
    if ($tableCheck && $tableCheck->rowCount() > 0) {
        $pilotPerformanceResult = $conn->query("
            SELECT 
                p.name,
                COUNT(pe.id) as completed_jobs,
                COALESCE(SUM(pe.amount), 0) as total_earnings
            FROM pilot p
            LEFT JOIN pilot_earnings pe ON p.phone = pe.pilot_phone
            GROUP BY p.id, p.name
            ORDER BY completed_jobs DESC
            LIMIT 10
        ");
        if ($pilotPerformanceResult) {
            while($row = $pilotPerformanceResult->fetch()) {
                $response['charts']['pilotPerformance'][] = $row;
            }
        }
    }

    // --- 3. Fetch Data for Actionable Widgets ---
    // QR Payment verification requests (orders with payment screenshots pending verification)
    if ($checkPaymentColumn && $checkPaymentColumn->rowCount() > 0) {
        $paymentVerificationResult = $conn->query("
            SELECT id, name, service_type, total_price, payment_screenshot, transaction_id 
            FROM orders 
            WHERE payment_screenshot IS NOT NULL 
            AND (order_status = 'Payment Verification Pending' OR order_status IS NULL)
            ORDER BY id DESC 
            LIMIT 5
        ");
        if ($paymentVerificationResult) {
            while($row = $paymentVerificationResult->fetch()) {
                $response['pending_actions']['payment_verification'][] = $row;
            }
        }
    }

    $pendingOrdersResult = $conn->query("SELECT id, name, service_type FROM orders ORDER BY id DESC LIMIT 5");
    if ($pendingOrdersResult) {
        while($row = $pendingOrdersResult->fetch()) {
            $response['pending_actions']['pending_orders'][] = $row;
        }
    }

    // In-progress orders for activity section
    if ($checkOrderStatusColumn && $checkOrderStatusColumn->rowCount() > 0) {
        $inProgressOrdersResult = $conn->query("
            SELECT id, name, service_type, order_status 
            FROM orders 
            WHERE order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed') 
            ORDER BY id DESC 
            LIMIT 5
        ");
        if ($inProgressOrdersResult) {
            while($row = $inProgressOrdersResult->fetch()) {
                $response['pending_actions']['inprogress_orders'][] = $row;
            }
        }
    }

    // --- 4. Fetch Recent Activity Data ---
    $recentActivityResult = $conn->query("
        SELECT 'order_completed' as type, name as title, service_type as details, created_at as timestamp
        FROM userordersuccess 
        WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'
        UNION ALL
        SELECT 'order_cancelled' as type, name as title, service_type as details, created_at as timestamp
        FROM userordercancel 
        WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'
        ORDER BY timestamp DESC
        LIMIT 10
    ");
    if ($recentActivityResult) {
        while($row = $recentActivityResult->fetch()) {
            $response['recent_activity'][] = $row;
        }
    }

} catch (Exception $e) {
    // Return error in JSON format
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}

echo json_encode($response);
?>