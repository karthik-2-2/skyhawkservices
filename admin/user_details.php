<?php
session_start();

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$user_id = $_GET['user_id'] ?? 0;

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM \"user\" WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}

// Fetch user statistics
// Total bookings from all tables
$stmt = $conn->prepare("
    SELECT 
        (SELECT COUNT(*) FROM orders WHERE phone = ?) +
        (SELECT COUNT(*) FROM userordersuccess WHERE phone = ?) +
        (SELECT COUNT(*) FROM userordercancel WHERE phone = ?) as total_bookings
");
$stmt->execute([$user['phone'], $user['phone'], $user['phone']]);
$total_bookings = $stmt->fetchColumn();

// Completed bookings (from userordersuccess table)
$stmt = $conn->prepare("SELECT COUNT(*) as completed_bookings FROM userordersuccess WHERE phone = ?");
$stmt->execute([$user['phone']]);
$completed_bookings = $stmt->fetchColumn();

// Pending bookings (from orders table with pending status)
$stmt = $conn->prepare("SELECT COUNT(*) as pending_bookings FROM orders WHERE phone = ? AND order_status LIKE '%Pending%'");
$stmt->execute([$user['phone']]);
$pending_bookings = $stmt->fetchColumn();

// Cancelled bookings
$stmt = $conn->prepare("SELECT COUNT(*) as cancelled_bookings FROM userordercancel WHERE phone = ?");
$stmt->execute([$user['phone']]);
$cancelled_bookings = $stmt->fetchColumn();

// In progress bookings
$stmt = $conn->prepare("SELECT COUNT(*) as inprogress_bookings FROM orders WHERE phone = ? AND order_status = 'in_progress'");
$stmt->execute([$user['phone']]);
$inprogress_bookings = $stmt->fetchColumn();

// Total amount spent (from completed orders)
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total_spent FROM userordersuccess WHERE phone = ?");
$stmt->execute([$user['phone']]);
$total_spent = $stmt->fetchColumn();

// Average booking amount
$average_booking = $completed_bookings > 0 ? $total_spent / $completed_bookings : 0;

// Recent bookings from all tables
$recent_bookings = [];

// Get from orders table
$stmt = $conn->prepare("SELECT 'pending' as table_source, service_type, created_at, total_price, order_status as status FROM orders WHERE phone = ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$user['phone']]);
$recent_bookings = array_merge($recent_bookings, $stmt->fetchAll());

// Get from userordersuccess table
$stmt = $conn->prepare("SELECT 'completed' as table_source, service_type, created_at, total_price, 'Completed' as status FROM userordersuccess WHERE phone = ? ORDER BY created_at DESC LIMIT 2");
$stmt->execute([$user['phone']]);
$recent_bookings = array_merge($recent_bookings, $stmt->fetchAll());

// Get from userordercancel table
$stmt = $conn->prepare("SELECT 'cancelled' as table_source, service_type, created_at, total_price, 'Cancelled' as status FROM userordercancel WHERE phone = ? ORDER BY created_at DESC LIMIT 2");
$stmt->execute([$user['phone']]);
$recent_bookings = array_merge($recent_bookings, $stmt->fetchAll());

// Sort by date
usort($recent_bookings, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$recent_bookings = array_slice($recent_bookings, 0, 5);

// Wallet balance (from userwallet table)
try {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as wallet_balance FROM userwallet WHERE phone = ?");
    $stmt->execute([$user['phone']]);
    $wallet_balance = $stmt->fetchColumn() ?? 0;
} catch (Exception $e) {
    $wallet_balance = 0; // Default if wallet table doesn't exist
}

// Calculate completion rate
$completion_rate = $total_bookings > 0 ? ($completed_bookings / $total_bookings) * 100 : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - <?php echo htmlspecialchars($user['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root {
            --black: #000;
            --mint-green: #3EB489;
            --metallic-silver: #c0c0c0;
            --dark-gray: #1a1a1a;
            --light-gray: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--black);
            color: var(--metallic-silver);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--mint-green);
        }

        .back-link {
            color: var(--mint-green);
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            background: var(--dark-gray);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid var(--mint-green);
            margin-bottom: 30px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            background: var(--mint-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--black);
            font-size: 32px;
            font-weight: bold;
        }

        .user-details h2 {
            color: var(--mint-green);
            margin-bottom: 8px;
        }

        .user-details p {
            margin: 4px 0;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--dark-gray);
            border: 1px solid var(--light-gray);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            border-color: var(--mint-green);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(57, 255, 20, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--mint-green);
        }

        .stat-icon {
            font-size: 32px;
            color: var(--mint-green);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: var(--mint-green);
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        .recent-bookings {
            background: var(--dark-gray);
            border: 1px solid var(--light-gray);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .recent-bookings h3 {
            color: var(--mint-green);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .booking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid var(--mint-green);
        }

        .booking-item:last-child {
            margin-bottom: 0;
        }

        .booking-info {
            flex: 1;
        }

        .booking-service {
            font-weight: 600;
            color: var(--mint-green);
        }

        .booking-date {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 4px;
        }

        .booking-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-completed {
            background: rgba(57, 255, 20, 0.2);
            color: var(--mint-green);
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .status-in_progress {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
        }

        .status-cancelled {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .booking-amount {
            font-weight: bold;
            color: var(--mint-green);
            margin-left: 15px;
        }

        .progress-bar {
            background: var(--light-gray);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            background: var(--mint-green);
            height: 100%;
            transition: width 0.3s ease;
        }

        .no-data {
            text-align: center;
            opacity: 0.6;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .user-info {
                flex-direction: column;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .booking-item {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="users.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Users List
            </a>
            <h1>User Analytics Dashboard</h1>
        </div>

        <div class="user-info">
            <div class="avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <div class="user-details">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($user['address']); ?></p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-number"><?php echo $total_bookings; ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number"><?php echo $completed_bookings; ?></div>
                <div class="stat-label">Completed Orders</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number"><?php echo $pending_bookings; ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div class="stat-number"><?php echo $inprogress_bookings; ?></div>
                <div class="stat-label">In Progress</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-number"><?php echo $cancelled_bookings; ?></div>
                <div class="stat-label">Cancelled Orders</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                <div class="stat-number">₹<?php echo number_format($total_spent, 2); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                <div class="stat-number">₹<?php echo number_format($wallet_balance, 2); ?></div>
                <div class="stat-label">Wallet Balance</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-number"><?php echo number_format($completion_rate, 1); ?>%</div>
                <div class="stat-label">Completion Rate</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $completion_rate; ?>%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                <div class="stat-number">₹<?php echo $completed_bookings > 0 ? number_format($average_booking, 2) : '0.00'; ?></div>
                <div class="stat-label">Average Order Value</div>
            </div>
        </div>

        <div class="recent-bookings">
            <h3><i class="fas fa-history"></i> Recent Bookings</h3>
            <?php if (count($recent_bookings) > 0): ?>
                <?php foreach ($recent_bookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-info">
                            <div class="booking-service"><?php echo htmlspecialchars($booking['service_type']); ?></div>
                            <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></div>
                        </div>
                        <div class="booking-status status-<?php echo strtolower(str_replace(' ', '_', $booking['status'])); ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </div>
                        <div class="booking-amount">₹<?php echo number_format($booking['total_price'], 2); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
                    <p>No bookings found for this user</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
