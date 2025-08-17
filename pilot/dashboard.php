<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['pilot_phone'])) {
    header("Location: index.php");
    exit();
}

$pilot_phone = $_SESSION['pilot_phone'];

// Fetch pilot details
$stmt = $conn->prepare("SELECT name, phone FROM pilot WHERE phone = ?");
$stmt->execute([$pilot_phone]);
$pilot_result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pilot_result) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$pilot_name = $pilot_result['name'];
$pilot_phone_display = $pilot_result['phone'];

// Fetch pilot earnings for completed orders
$earnings_stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total_earned, COUNT(*) as completed_orders FROM pilot_earnings WHERE pilot_phone = ?");
$earnings_stmt->execute([$pilot_phone]);
$earnings_result = $earnings_stmt->fetch(PDO::FETCH_ASSOC);

$total_earned = $earnings_result['total_earned'];
$completed_orders_count = $earnings_result['completed_orders'];

// Check for new available orders
$new_orders_stmt = $conn->prepare("SELECT COUNT(*) as new_orders_count FROM orders WHERE pilot_phone IS NULL AND order_status = 'Waiting for Pilot'");
$new_orders_stmt->execute();
$new_orders_result = $new_orders_stmt->fetch(PDO::FETCH_ASSOC);
$new_orders_count = $new_orders_result['new_orders_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pilot Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root {
            --black: #000;
            --mint-green: #3EB489;
            --metallic-silver: #B0B0B0;
            --dark-gray: #1a1a1a;
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
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            opacity: 0;
            transform: translateY(-30px);
            animation: fadeInDown 0.8s 0.2s ease forwards;
        }
        
        .header h1 {
            font-size: 3rem;
            color: var(--mint-green);
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .pilot-info {
            background-color: var(--dark-gray);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 30px;
            border: 2px solid var(--mint-green);
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFadeIn 0.6s 0.4s ease forwards;
        }
        
        .pilot-info h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .pilot-info p {
            font-size: 1.1rem;
            color: var(--metallic-silver);
            margin-bottom: 8px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background-color: var(--dark-gray);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            border-bottom: 5px solid var(--mint-green);
            opacity: 0;
            transform: translateY(30px);
            animation: slideUpFadeIn 0.6s ease forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.6s; }
        .stat-card:nth-child(2) { animation-delay: 0.8s; }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(62, 180, 137, 0.3);
        }
        
        .stat-card .icon {
            font-size: 3.5rem;
            color: var(--mint-green);
            margin-bottom: 20px;
        }
        
        .stat-card .value {
            font-size: 2.5rem;
            color: #fff;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .stat-card .title {
            font-size: 1.2rem;
            color: var(--metallic-silver);
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .option-card {
            background-color: var(--dark-gray);
            border-radius: 20px;
            padding: 35px;
            border-bottom: 5px solid var(--mint-green);
            text-decoration: none;
            color: var(--metallic-silver);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
            transform: translateY(30px);
            animation: slideUpFadeIn 0.6s ease forwards;
            position: relative;
            overflow: hidden;
        }
        
        .option-card:nth-child(1) { animation-delay: 1s; }
        .option-card:nth-child(2) { animation-delay: 1.2s; }
        .option-card:nth-child(3) { animation-delay: 1.4s; }
        
        .option-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(62, 180, 137, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .option-card:hover::before {
            left: 100%;
        }
        
        .option-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(62, 180, 137, 0.2);
        }
        
        .option-card .icon {
            font-size: 4rem;
            color: var(--mint-green);
            margin-bottom: 25px;
            display: block;
        }
        
        .option-card h2 {
            color: #fff;
            font-size: 1.6rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .option-card p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .logout-link {
            display: inline-block;
            margin-top: 30px;
            padding: 15px 30px;
            background-color: var(--mint-green);
            color: var(--black);
            text-decoration: none;
            font-weight: 600;
            border-radius: 50px;
            opacity: 0;
            animation: fadeIn 1s 1.6s ease forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .logout-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(62, 180, 137, 0.3);
        }
        
        .section-title {
            text-align: center;
            font-size: 2rem;
            color: var(--mint-green);
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        @keyframes fadeInDown { 
            to { opacity: 1; transform: translateY(0); } 
        }
        
        @keyframes slideUpFadeIn { 
            to { opacity: 1; transform: translateY(0); } 
        }
        
        @keyframes fadeIn { 
            to { opacity: 1; } 
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 0 10px;
            }
            
            .header h1 {
                font-size: 2.5rem;
            }
            
            .pilot-info h2 {
                font-size: 1.5rem;
            }
            
            .pilot-info p {
                font-size: 1rem;
            }
            
            .options-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        /* Order Notification Popup Styles */
        .order-notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .order-notification-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .order-notification-popup {
            background: linear-gradient(145deg, #2a3441, #3a4b5c);
            border-radius: 24px;
            padding: 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            position: relative;
            transform: scale(0.7) translateY(50px);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(62, 180, 137, 0.3);
        }

        .order-notification-overlay.show .order-notification-popup {
            transform: scale(1) translateY(0);
        }

        .notification-icon {
            width: 80px;
            height: 80px;
            background: var(--mint-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse-notification 2s infinite;
        }

        .notification-icon i {
            font-size: 36px;
            color: #000;
        }

        @keyframes pulse-notification {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(62, 180, 137, 0.7);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 20px rgba(62, 180, 137, 0);
            }
        }

        .order-notification-popup h2 {
            color: var(--mint-green);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .order-notification-popup .subtitle {
            color: #B0B0B0;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .order-notification-popup .main-text {
            color: #FFF;
            font-size: 20px;
            margin-bottom: 30px;
            font-weight: 600;
            line-height: 1.4;
        }

        .order-count {
            display: inline-block;
            background: var(--mint-green);
            color: #000;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 18px;
            margin: 0 5px;
        }

        .notification-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .notification-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Outfit', sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .notification-btn.primary {
            background: var(--mint-green);
            color: #000;
        }

        .notification-btn.primary:hover {
            background: #35a074;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(62, 180, 137, 0.3);
        }

        .notification-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #B0B0B0;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .notification-btn.secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFF;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .order-notification-popup {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .notification-buttons {
                flex-direction: column;
            }
            
            .notification-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Pilot Dashboard</h1>
        </div>
        
        <div class="pilot-info">
            <h2><i class="fas fa-user-pilot"></i> Welcome, <?php echo htmlspecialchars($pilot_name); ?>!</h2>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($pilot_phone_display); ?></p>
            <p><strong>Status:</strong> Active Pilot</p>
        </div>
        
        <div class="section-title">Your Performance</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><b>Wallet</b></div>
                <div class="value">₹<?php echo number_format($total_earned, 2); ?></div>
                <div class="title">Total Earned</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="value"><?php echo $completed_orders_count; ?></div>
                <div class="title">Orders Completed</div>
            </div>
        </div>
        
        <div class="section-title">Quick Actions</div>
        <div class="options-grid">
            <a href="orders.php" class="option-card">
                <div class="icon"><i class="fas fa-box-open"></i></div>
                <h2>View My Orders</h2>
                <p>See available orders, active assignments, and completed jobs with earnings.</p>
            </a>
            <a href="pilotwallet.php" class="option-card">
                <div class="icon"><i class="fas fa-wallet"></i></div>
                <h2>My Wallet</h2>
                <p>Track your earnings, payment history, and view detailed performance analytics.</p>
            </a>
        </div>
        
        <div style="text-align: center;">
            <a href="../index.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Order Notification Popup -->
    <?php if ($new_orders_count > 0): ?>
    <div class="order-notification-overlay" id="orderNotificationOverlay">
        <div class="order-notification-popup">
            <div class="notification-icon">
                <i class="fas fa-bell"></i>
            </div>
            <h2>Hey! New Order Available!</h2>
            <p class="subtitle">Great opportunity waiting for you</p>
            <p class="main-text">
                <?php if ($new_orders_count == 1): ?>
                    There is <span class="order-count">1</span> new order available for pickup!
                <?php else: ?>
                    There are <span class="order-count"><?php echo $new_orders_count; ?></span> new orders available for pickup!
                <?php endif; ?>
            </p>
            <p class="subtitle">Don't miss out - orders are assigned on first-come, first-served basis!</p>
            
            <div class="notification-buttons">
                <a href="orders.php" class="notification-btn primary">
                    <i class="fas fa-eye"></i> Check Orders
                </a>
                <button type="button" class="notification-btn secondary" onclick="dismissNotification()">
                    <i class="fas fa-times"></i> Maybe Later
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationOverlay = document.getElementById('orderNotificationOverlay');
            
            // Show notification if there are new orders
            if (notificationOverlay) {
                setTimeout(() => {
                    notificationOverlay.classList.add('show');
                }, 1500); // Show after page loads and animations complete
            }
        });

        function dismissNotification() {
            const notificationOverlay = document.getElementById('orderNotificationOverlay');
            if (notificationOverlay) {
                notificationOverlay.classList.remove('show');
                
                // Store dismissal in sessionStorage to prevent showing again during this session
                sessionStorage.setItem('orderNotificationDismissed', 'true');
            }
        }

        // Close popup when clicking outside
        document.addEventListener('click', function(e) {
            const notificationOverlay = document.getElementById('orderNotificationOverlay');
            const notificationPopup = document.querySelector('.order-notification-popup');
            
            if (notificationOverlay && e.target === notificationOverlay) {
                dismissNotification();
            }
        });

        // Check if notification was dismissed in this session
        document.addEventListener('DOMContentLoaded', function() {
            if (sessionStorage.getItem('orderNotificationDismissed') === 'true') {
                const notificationOverlay = document.getElementById('orderNotificationOverlay');
                if (notificationOverlay) {
                    notificationOverlay.style.display = 'none';
                }
            }
        });

        // Auto-refresh page every 30 seconds to check for new orders (optional)
        setInterval(function() {
            // Only refresh if notification is not currently shown and not dismissed
            const notificationOverlay = document.getElementById('orderNotificationOverlay');
            const isDismissed = sessionStorage.getItem('orderNotificationDismissed') === 'true';
            
            if (!notificationOverlay && !isDismissed) {
                location.reload();
            }
        }, 30000); // 30 seconds
    </script>
</body>
</html>