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
</body>
</html>