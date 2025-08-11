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
        $pendingOrdersCountResult = $conn->prepare("
            SELECT COUNT(*) AS total FROM orders o 
            WHERE o.id NOT IN (SELECT order_id FROM userordersuccess WHERE order_id IS NOT NULL) 
            AND o.id NOT IN (SELECT order_id FROM userordercancel WHERE order_id IS NOT NULL)
        ");
        $pendingOrdersCountResult->execute();
        $pendingOrdersCountData = $pendingOrdersCountResult->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pending_orders'] = $pendingOrdersCountData ? $pendingOrdersCountData['total'] : 0;
    }

    $completedOrdersCountResult = $conn->prepare("SELECT COUNT(*) AS total FROM userordersuccess");
    $completedOrdersCountResult->execute();
    $completed = $completedOrdersCountResult->fetch(PDO::FETCH_ASSOC);
    $response['stats']['completed_orders'] = $completed ? $completed['total'] : 0;

    $cancelledOrdersCountResult = $conn->prepare("SELECT COUNT(*) AS total FROM userordercancel");
    $cancelledOrdersCountResult->execute();
    $cancelled = $cancelledOrdersCountResult->fetch(PDO::FETCH_ASSOC);
    $response['stats']['cancelled_orders'] = $cancelled ? $cancelled['total'] : 0;

    $walletBalanceResult = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) AS balance FROM userordersuccess");
    $walletBalanceResult->execute();
    $wallet = $walletBalanceResult->fetch(PDO::FETCH_ASSOC);
    $response['stats']['wallet_balance'] = $wallet ? $wallet['balance'] : 0;

    // Additional stats for enhanced dashboard
    $totalOrdersResult = $conn->prepare("SELECT COUNT(*) AS total FROM orders");
    $totalOrdersResult->execute();
    $total = $totalOrdersResult->fetch(PDO::FETCH_ASSOC);
    $response['stats']['total_orders'] = $total ? $total['total'] : 0;

    // Check if pilot_earnings table exists
    $tableCheck = $conn->prepare("SELECT table_name FROM information_schema.tables WHERE table_name = 'pilot_earnings'");
    $tableCheck->execute();
    $pilotEarningsTable = $tableCheck->fetch(PDO::FETCH_ASSOC);
    if ($pilotEarningsTable) {
        $pilotEarningsResult = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM pilot_earnings");
        $pilotEarningsResult->execute();
        $earnings = $pilotEarningsResult->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pilot_earnings'] = $earnings ? $earnings['total'] : 0;
    } else {
        $response['stats']['pilot_earnings'] = 0;
    }

    // QR Payment verification stats - Count orders with payment screenshots (pending verification)
    $checkPaymentColumn = $conn->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'orders' AND column_name = 'payment_screenshot'");
    $checkPaymentColumn->execute();
    $paymentColumn = $checkPaymentColumn->fetch(PDO::FETCH_ASSOC);
    if ($paymentColumn) {
        $pendingVerificationResult = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE payment_screenshot IS NOT NULL AND (order_status = 'Payment Verification Pending' OR order_status = 'Pending Admin Approval')");
        $pendingVerificationResult->execute();
        $pending = $pendingVerificationResult->fetch(PDO::FETCH_ASSOC);
        $response['stats']['pending_verification'] = $pending ? $pending['total'] : 0;
    } else {
        $response['stats']['pending_verification'] = 0;
    }

    // In-Progress Orders stats - Count orders that are currently being worked on
    $checkOrderStatusColumn2 = $conn->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'orders' AND column_name = 'order_status'");
    $checkOrderStatusColumn2->execute();
    $statusColumn = $checkOrderStatusColumn2->fetch(PDO::FETCH_ASSOC);
    if ($statusColumn) {
        $inProgressOrdersResult = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed', 'Cancellation Requested')");
        $inProgressOrdersResult->execute();
        $inProgress = $inProgressOrdersResult->fetch(PDO::FETCH_ASSOC);
        $response['stats']['inprogress_orders'] = $inProgress ? $inProgress['total'] : 0;
    } else {
        $response['stats']['inprogress_orders'] = 0;
    }

    // --- 2. Fetch Data for Charts ---
    // Orders by day with date column check
    $checkDateColumn = $conn->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'userordersuccess' AND column_name = 'booking_date'");
    $checkDateColumn->execute();
    $dateColumn = $checkDateColumn->fetch(PDO::FETCH_ASSOC);
    if ($dateColumn) {
        $ordersByDayResult = $conn->prepare("
            SELECT DATE(booking_date) as order_day, COUNT(*) as order_count
            FROM userordersuccess
            WHERE booking_date >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY DATE(booking_date) ORDER BY order_day ASC
        ");
        $ordersByDayResult->execute();
        $ordersByDay = $ordersByDayResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($ordersByDay as $row) {
            $response['charts']['ordersByDay'][] = $row;
        }
    } else {
        // Use created_at or a default if booking_date doesn't exist
        $ordersByDayResult = $conn->prepare("
            SELECT DATE(created_at) as order_day, COUNT(*) as order_count
            FROM userordersuccess
            WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY DATE(created_at) ORDER BY order_day ASC
        ");
        $ordersByDayResult->execute();
        $ordersByDay = $ordersByDayResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($ordersByDay as $row) {
            $response['charts']['ordersByDay'][] = $row;
        }
    }

    // Service distribution
    $serviceDistResult = $conn->prepare("SELECT service_type, COUNT(*) as count FROM userordersuccess GROUP BY service_type");
    $serviceDistResult->execute();
    $serviceDist = $serviceDistResult->fetchAll(PDO::FETCH_ASSOC);
    foreach ($serviceDist as $row) {
        $response['charts']['serviceDistribution'][] = $row;
    }

    // Monthly revenue chart data
    if ($dateColumn) {
        $monthlyRevenueResult = $conn->prepare("
            SELECT 
                TO_CHAR(booking_date, 'YYYY-MM') as month,
                SUM(total_price) as revenue,
                COUNT(*) as orders
            FROM userordersuccess 
            WHERE booking_date >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY TO_CHAR(booking_date, 'YYYY-MM')
            ORDER BY month ASC
        ");
        $monthlyRevenueResult->execute();
        $monthlyRevenue = $monthlyRevenueResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($monthlyRevenue as $row) {
            $response['charts']['monthlyRevenue'][] = $row;
        }
    } else {
        $monthlyRevenueResult = $conn->prepare("
            SELECT 
                TO_CHAR(created_at, 'YYYY-MM') as month,
                SUM(total_price) as revenue,
                COUNT(*) as orders
            FROM userordersuccess 
            WHERE created_at >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY TO_CHAR(created_at, 'YYYY-MM')
            ORDER BY month ASC
        ");
        $monthlyRevenueResult->execute();
        $monthlyRevenue = $monthlyRevenueResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($monthlyRevenue as $row) {
            $response['charts']['monthlyRevenue'][] = $row;
        }
    }

    // Pilot performance data - only if pilot_earnings table exists
    if ($pilotEarningsTable) {
        $pilotPerformanceResult = $conn->prepare("
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
        $pilotPerformanceResult->execute();
        $pilotPerformance = $pilotPerformanceResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pilotPerformance as $row) {
            $response['charts']['pilotPerformance'][] = $row;
        }
    }

    // --- 3. Fetch Data for Actionable Widgets ---
    // QR Payment verification requests (orders with payment screenshots pending verification)
    if ($paymentColumn) {
        $paymentVerificationResult = $conn->prepare("
            SELECT id, name, service_type, total_price, payment_screenshot, transaction_id 
            FROM orders 
            WHERE payment_screenshot IS NOT NULL 
            AND (order_status = 'Payment Verification Pending' OR order_status IS NULL)
            ORDER BY id DESC 
            LIMIT 5
        ");
        $paymentVerificationResult->execute();
        $paymentVerification = $paymentVerificationResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($paymentVerification as $row) {
            $response['pending_actions']['payment_verification'][] = $row;
        }
    }

    $pendingOrdersResult = $conn->prepare("SELECT id, name, service_type FROM orders ORDER BY id DESC LIMIT 5");
    $pendingOrdersResult->execute();
    $pendingOrders = $pendingOrdersResult->fetchAll(PDO::FETCH_ASSOC);
    foreach ($pendingOrders as $row) {
        $response['pending_actions']['pending_orders'][] = $row;
    }

    // In-progress orders for activity section
    if ($statusColumn) {
        $inProgressOrdersResult = $conn->prepare("
            SELECT id, name, service_type, order_status 
            FROM orders 
            WHERE order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed') 
            ORDER BY id DESC 
            LIMIT 5
        ");
        $inProgressOrdersResult->execute();
        $inProgressOrders = $inProgressOrdersResult->fetchAll(PDO::FETCH_ASSOC);
        foreach ($inProgressOrders as $row) {
            $response['pending_actions']['inprogress_orders'][] = $row;
        }
    }

    // --- 4. Fetch Recent Activity Data ---
    $recentActivityResult = $conn->prepare("
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
    $recentActivityResult->execute();
    $recentActivity = $recentActivityResult->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentActivity as $row) {
        $response['recent_activity'][] = $row;
    }

} catch (Exception $e) {
    // Return error in JSON format
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}

echo json_encode($response);
?>