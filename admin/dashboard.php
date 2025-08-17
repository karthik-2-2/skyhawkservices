<?php
session_start();
require_once '../config/db.php';

// FIX: Changed the session check and the redirect location
if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php'); // Changed from login.php
    exit();
}
$adminPhone = $_SESSION['admin_phone'];

// Check for notifications
try {
    // Check for pending orders (Payment Verification Pending)
    $pending_orders_stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE order_status = 'Pending Admin Approval' OR order_status = 'Payment Verification Pending'");
    $pending_orders_stmt->execute();
    $pending_orders_result = $pending_orders_stmt->fetch(PDO::FETCH_ASSOC);
    $pending_orders_count = $pending_orders_result['count'];

    // Check for completed orders awaiting admin finalization
    $completed_orders_stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE order_status = 'Order Completed'");
    $completed_orders_stmt->execute();
    $completed_orders_result = $completed_orders_stmt->fetch(PDO::FETCH_ASSOC);
    $completed_orders_count = $completed_orders_result['count'];
    
    // Total notifications count
    $total_notifications = $pending_orders_count + $completed_orders_count;
} catch (Exception $e) {
    $pending_orders_count = 0;
    $completed_orders_count = 0;
    $total_notifications = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Skyhawk Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    :root {
      --black: #000000;
      --mint-green: #3EB489;
      --metallic-silver: #B0B0B0;
      --dark-gray: #1a1a1a;
      --card-color: #2a2a2a;
      --text-color: #E0E0E0;
      --text-secondary: #BDBDBD;
      --border-color: #404040;
      --success: #28a745;
      --warning: #ffc107;
      --danger: #dc3545;
      --info: #17a2b8;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(135deg, var(--black) 0%, var(--dark-gray) 100%);
      color: var(--text-color);
      overflow-x: hidden;
    }
    
    .grid-container {
      display: grid;
      grid-template-columns: 280px 1fr;
      grid-template-rows: auto 1fr;
      grid-template-areas:
        "sidebar header"
        "sidebar main";
      min-height: 100vh;
    }
    
    .header {
      grid-area: header;
      background: linear-gradient(90deg, var(--black) 0%, var(--dark-gray) 100%);
      backdrop-filter: blur(10px);
      border-bottom: 2px solid var(--mint-green);
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .header h1 {
      color: var(--mint-green);
      font-weight: 700;
      font-size: 1.8rem;
      margin: 0;
      text-shadow: 0 0 10px rgba(62, 180, 137, 0.3);
    }
    
    .header .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .logout-btn {
      background: linear-gradient(135deg, var(--mint-green), #2d8659);
      color: var(--black);
      padding: 10px 20px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(62, 180, 137, 0.3);
    }
    
    .logout-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(62, 180, 137, 0.5);
    }
    
    .sidebar {
      grid-area: sidebar;
      background: linear-gradient(180deg, var(--dark-gray) 0%, var(--card-color) 100%);
      backdrop-filter: blur(10px);
      border-right: 2px solid var(--mint-green);
      padding: 30px 0;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      box-shadow: 4px 0 20px rgba(0,0,0,0.3);
    }
    
    .sidebar-title {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 40px;
      padding: 0 20px;
    }
    
    .sidebar-title .fa-drone {
      color: var(--mint-green);
      font-size: 32px;
      margin-right: 12px;
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .sidebar-title h2 {
      color: var(--mint-green);
      font-size: 28px;
      font-weight: 700;
      text-shadow: 0 0 15px rgba(62, 180, 137, 0.4);
    }
    
    .sidebar-menu {
      padding: 0 10px;
    }
    
    .sidebar-menu a {
      display: flex;
      align-items: center;
      color: var(--text-secondary);
      padding: 18px 20px;
      text-decoration: none;
      margin: 5px 0;
      border-radius: 15px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .sidebar-menu a::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(62, 180, 137, 0.1), transparent);
      transition: left 0.5s ease;
    }
    
    .sidebar-menu a:hover::before,
    .sidebar-menu a.active::before {
      left: 100%;
    }
    
    .sidebar-menu a i {
      font-size: 18px;
      margin-right: 12px;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-menu a:hover,
    .sidebar-menu a.active {
      background: linear-gradient(135deg, var(--mint-green), #2d8659);
      color: var(--black);
      font-weight: 600;
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(62, 180, 137, 0.3);
    }
    
    .main-content {
      grid-area: main;
      padding: 40px;
      overflow-y: auto;
      background: var(--black);
      width: 100%;
      min-height: 100vh;
    }

    #dashboard-content {
      width: 100%;
      height: auto;
      min-height: auto;
    }
    
    /* Global chart expansion prevention */
    .chart-container, .analytics-card, [id*="Chart"], canvas {
      box-sizing: border-box !important;
    }
    
    .chart-container *, .analytics-card * {
      max-height: inherit !important;
    }
    
    .dashboard-header {
      margin-bottom: 40px;
      opacity: 0;
      animation: slideInDown 0.8s ease forwards;
    }
    
    @keyframes slideInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .dashboard-header h1 {
      font-size: 3rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--mint-green), var(--metallic-silver));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
    }
    
    .dashboard-header p {
      color: var(--text-secondary);
      font-size: 1.2rem;
      font-weight: 300;
    }
    
    .stats-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      margin-bottom: 40px;
      width: 100%;
      min-height: auto;
      overflow: visible;
      align-items: stretch;
      justify-content: stretch;
    }
    
    .stat-card {
      background: linear-gradient(135deg, var(--card-color) 0%, var(--dark-gray) 100%);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      opacity: 0;
      animation: fadeInUp 0.8s ease forwards;
      display: block !important;
      visibility: visible !important;
      min-height: 120px;
      box-sizing: border-box;
      flex: 1 1 calc(33.333% - 17px);
      min-width: 280px;
    }
    
    /* Stronger override to prevent any JavaScript from hiding cards */
    .stat-card[style*="display: none"],
    .stat-card[style*="visibility: hidden"] {
      display: block !important;
      visibility: visible !important;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    .stat-card:nth-child(5) { animation-delay: 0.5s; }
    .stat-card:nth-child(6) { animation-delay: 0.6s; }
    
    /* Force 6 cards in 2 rows on larger screens */
    @media (min-width: 1400px) {
      .stat-card {
        flex: 1 1 calc(33.333% - 17px);
      }
    }
    
    @media (max-width: 1399px) and (min-width: 900px) {
      .stat-card {
        flex: 1 1 calc(50% - 12.5px);
      }
    }
    
    @media (max-width: 899px) {
      .stat-card {
        flex: 1 1 100%;
        min-width: 100%;
      }
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--mint-green), var(--metallic-silver));
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(62, 180, 137, 0.2);
    }
    
    .stat-card .icon {
      font-size: 2.5rem;
      margin-bottom: 15px;
      background: linear-gradient(135deg, var(--mint-green), var(--metallic-silver));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .stat-card .title {
      font-size: 0.9rem;
      color: var(--text-secondary);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 500;
      margin-bottom: 8px;
    }
    
    .stat-card .value {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--text-color);
      margin-bottom: 10px;
    }
    
    .stat-card .change {
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .change.positive { color: var(--success); }
    .change.negative { color: var(--danger); }
    
    .charts-section {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 30px;
      margin-bottom: 40px;
    }
    
    .chart-container {
      background: linear-gradient(135deg, var(--card-color) 0%, var(--dark-gray) 100%);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      position: relative;
      overflow: hidden;
      opacity: 0;
      animation: fadeInUp 1s ease forwards 0.7s;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      height: 400px; /* Fixed height to prevent infinite expansion */
      min-height: 400px;
      max-height: 400px; /* Enforce maximum height */
    }
    
    .chart-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(62, 180, 137, 0.1);
    }
    
    .chart-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--mint-green), var(--metallic-silver));
    }
    
    .chart-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--mint-green);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .chart-title i {
      font-size: 1.2rem;
    }
    
    .chart-container canvas,
    .analytics-card canvas {
      max-width: 100% !important;
      max-height: 300px !important;
      height: 300px !important;
      width: 100% !important;
    }
    
    .analytics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }
    
    .analytics-card {
      background: linear-gradient(135deg, var(--card-color) 0%, var(--dark-gray) 100%);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      opacity: 0;
      animation: fadeInUp 1s ease forwards 0.9s;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      height: 400px; /* Fixed height to prevent infinite expansion */
      min-height: 400px;
      max-height: 400px; /* Enforce maximum height */
      overflow: hidden; /* Prevent content overflow */
      position: relative;
    }
    
    .analytics-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(62, 180, 137, 0.1);
    }
    
    .analytics-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--metallic-silver), var(--mint-green));
    }
    
    .quick-actions {
      background: linear-gradient(135deg, var(--card-color) 0%, var(--dark-gray) 100%);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      opacity: 0;
      animation: fadeInUp 1s ease forwards 1.1s;
      margin-bottom: 40px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }
    
    .quick-actions:hover {
      box-shadow: 0 15px 40px rgba(62, 180, 137, 0.05);
    }
    
    .action-buttons {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    
    .action-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 15px 20px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      position: relative;
      overflow: hidden;
    }
    
    .action-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
      transition: left 0.5s ease;
    }
    
    .action-btn:hover::before {
      left: 100%;
    }
    
    .action-btn.primary {
      background: linear-gradient(135deg, var(--mint-green), #2d8659);
      color: var(--black);
    }
    
    .action-btn.secondary {
      background: linear-gradient(135deg, var(--metallic-silver), #8a8a8a);
      color: var(--black);
    }
    
    .action-btn.warning {
      background: linear-gradient(135deg, var(--warning), #e0a800);
      color: var(--black);
    }
    
    .action-btn.info {
      background: linear-gradient(135deg, var(--info), #138496);
      color: white;
    }
    
    .action-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .recent-activity {
      background: linear-gradient(135deg, var(--card-color) 0%, var(--dark-gray) 100%);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid var(--border-color);
      margin-bottom: 40px;
      opacity: 0;
      animation: fadeInUp 1s ease forwards 1.3s;
    }
    
    .activity-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px 0;
      border-bottom: 1px solid var(--border-color);
    }
    
    .activity-item:last-child {
      border-bottom: none;
    }
    
    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    
    .activity-icon.success { background: rgba(40, 167, 69, 0.2); color: var(--success); }
    .activity-icon.warning { background: rgba(255, 193, 7, 0.2); color: var(--warning); }
    .activity-icon.info { background: rgba(23, 162, 184, 0.2); color: var(--info); }
    
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid var(--border-color);
      border-radius: 50%;
      border-top-color: var(--mint-green);
      animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    @keyframes pulse {
      0%, 100% { 
        box-shadow: 0 10px 30px rgba(62, 180, 137, 0.2);
        transform: scale(1);
      }
      50% { 
        box-shadow: 0 15px 40px rgba(62, 180, 137, 0.4);
        transform: scale(1.02);
      }
    }
    
    /* Loading Indicator Styles */
    .loading-indicator {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }
    
    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 4px solid var(--border-color);
      border-top: 4px solid var(--mint-green);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    .stat-card.urgent {
      animation: pulse 2s ease-in-out infinite;
      display: block !important;
      visibility: visible !important;
    }
    
        .error-message {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }

        /* Admin Notification Popup Styles */
        .admin-notification-overlay {
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

        .admin-notification-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .admin-notification-popup {
            background: linear-gradient(145deg, #2a3441, #3a4b5c);
            border-radius: 24px;
            padding: 40px;
            max-width: 520px;
            width: 90%;
            text-align: center;
            position: relative;
            transform: scale(0.7) translateY(50px);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(62, 180, 137, 0.3);
        }

        .admin-notification-overlay.show .admin-notification-popup {
            transform: scale(1) translateY(0);
        }

        .admin-notification-icon {
            width: 80px;
            height: 80px;
            background: var(--mint-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse-admin-notification 2s infinite;
        }

        .admin-notification-icon i {
            font-size: 36px;
            color: #000;
        }

        @keyframes pulse-admin-notification {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(62, 180, 137, 0.7);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 20px rgba(62, 180, 137, 0);
            }
        }

        .admin-notification-popup h2 {
            color: var(--mint-green);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .admin-notification-popup .subtitle {
            color: #B0B0B0;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .admin-notification-popup .main-text {
            color: #FFF;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
            line-height: 1.4;
        }

        .notification-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .notification-stat-card {
            background: rgba(62, 180, 137, 0.1);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(62, 180, 137, 0.3);
        }

        .notification-stat-card .count {
            display: block;
            font-size: 32px;
            font-weight: bold;
            color: var(--mint-green);
            margin-bottom: 5px;
        }

        .notification-stat-card .label {
            font-size: 14px;
            color: #B0B0B0;
            font-weight: 500;
        }

        .admin-notification-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .admin-notification-btn {
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

        .admin-notification-btn.primary {
            background: var(--mint-green);
            color: #000;
        }

        .admin-notification-btn.primary:hover {
            background: #35a074;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(62, 180, 137, 0.3);
        }

        .admin-notification-btn.secondary {
            background: rgba(255, 193, 7, 0.9);
            color: #000;
        }

        .admin-notification-btn.secondary:hover {
            background: rgba(255, 193, 7, 1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .admin-notification-btn.dismiss {
            background: rgba(255, 255, 255, 0.1);
            color: #B0B0B0;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .admin-notification-btn.dismiss:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFF;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .admin-notification-popup {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .notification-stats {
                grid-template-columns: 1fr;
            }
            
            .admin-notification-buttons {
                flex-direction: column;
            }
            
            .admin-notification-btn {
                width: 100%;
                justify-content: center;
            }
        }    @media (max-width: 1200px) {
      .grid-container {
        grid-template-columns: 1fr;
        grid-template-areas:
          "header"
          "main";
      }
      
      .sidebar {
        display: none;
      }
      
      .charts-section {
        grid-template-columns: 1fr;
      }
      
      .chart-container,
      .analytics-card {
        height: 350px; /* Smaller height for mobile */
        min-height: 350px;
        max-height: 350px; /* Enforce maximum height for mobile */
        overflow: hidden; /* Prevent content overflow on mobile */
      }
      
      .chart-container canvas,
      .analytics-card canvas {
        max-height: 250px !important;
        height: 250px !important;
      }
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 20px;
      }
      
      .dashboard-header h1 {
        font-size: 2rem;
      }
      
      .chart-container,
      .analytics-card {
        height: 300px;
        min-height: 300px;
        max-height: 300px; /* Enforce maximum height for small mobile */
        overflow: hidden; /* Prevent content overflow on small mobile */
        padding: 20px;
      }
      
      .chart-container canvas,
      .analytics-card canvas {
        max-height: 200px !important;
        height: 200px !important;
      }
    }
  </style>
</head>
<body>
    <div class="grid-container">
        <aside class="sidebar">
            <div class="sidebar-title">
                <i class="fas fa-drone"></i>
                <h2>Skyhawk</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="pilots.php"><i class="fas fa-helicopter"></i> Pilots</a>
                <a href="pending_orders.php"><i class="fas fa-hourglass-half"></i> Pending Orders</a>
                <a href="inprogress_orders.php"><i class="fas fa-cogs"></i> In-Progress Orders</a>
                <a href="completed_orders.php"><i class="fas fa-check-circle"></i> Completed Orders</a>
                <a href="refund_requests.php"><i class="fas fa-undo-alt"></i> Refund Requests</a>
                <a href="wallet.php"><i class="fas fa-wallet"></i> Financial Dashboard</a>
            </nav>
        </aside>

        <header class="header">
            <h1><i class="fas fa-chart-line"></i> Admin Control Center</h1>
            <div class="user-info">
                <span><i class="fas fa-user-shield"></i> Administrator</span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <main class="main-content">
            <div class="dashboard-header">
                <h1>Welcome Back, Admin!</h1>
                <p>Monitor your drone services platform performance and manage operations efficiently.</p>
            </div>

            <!-- Loading State -->
            <div id="loading-state" style="text-align: center; padding: 40px;">
                <div class="loading-indicator">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 15px; color: var(--text-secondary);">Loading dashboard data...</p>
                </div>
            </div>

            <!-- Error State -->
            <div id="error-state" style="display: none;">
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="error-text">Failed to load dashboard data. Please refresh the page.</span>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div id="dashboard-content" style="display: none;">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <div class="title">Total Users</div>
                        <div class="value" id="total-users">0</div>
                        <div class="change positive" id="users-change">+0% from last month</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon"><i class="fas fa-helicopter"></i></div>
                        <div class="title">Active Pilots</div>
                        <div class="value" id="total-pilots">0</div>
                        <div class="change positive" id="pilots-change">+0% from last month</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                        <div class="title">Pending Orders</div>
                        <div class="value" id="pending-orders">0</div>
                        <div class="change" id="pending-change">Needs attention</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <div class="title">Completed Orders</div>
                        <div class="value" id="completed-orders">0</div>
                        <div class="change positive" id="completed-change">+0% this week</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon"><i class="fas fa-cogs"></i></div>
                        <div class="title">In-Progress Orders</div>
                        <div class="value" id="inprogress-orders">0</div>
                        <div class="change" id="inprogress-change">Active orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon"><i class="fas fa-rupee-sign"></i></div>
                        <div class="title">Total Revenue</div>
                        <div class="value" id="total-revenue">₹0</div>
                        <div class="change positive" id="revenue-change">+0% this month</div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-section">
                    <div class="chart-container">
                        <div class="chart-title">
                            <i class="fas fa-chart-line"></i>
                            Orders Trend (Last 7 Days)
                        </div>
                        <canvas id="ordersChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <div class="chart-title">
                            <i class="fas fa-chart-pie"></i>
                            Service Distribution
                        </div>
                        <canvas id="servicesPieChart"></canvas>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="chart-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </div>
                    <div class="action-buttons">
                        <a href="pending_orders.php" class="action-btn primary">
                            <i class="fas fa-hourglass-half"></i>
                            Review Pending Orders
                        </a>
                        <a href="inprogress_orders.php" class="action-btn secondary">
                            <i class="fas fa-cogs"></i>
                            In-Progress Orders
                        </a>
                        <a href="refund_requests.php" class="action-btn warning">
                            <i class="fas fa-undo"></i>
                            Refund Requests
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="recent-activity">
                    <div class="chart-title">
                        <i class="fas fa-clock"></i>
                        Recent Activity
                    </div>
                    <div id="recent-activity-list">
                        <!-- Activity items will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Admin Notification Popup -->
    <?php if ($total_notifications > 0): ?>
    <div class="admin-notification-overlay" id="adminNotificationOverlay">
        <div class="admin-notification-popup">
            <div class="admin-notification-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2>Admin Action Required!</h2>
            <p class="subtitle">Important tasks are waiting for your attention</p>
            <p class="main-text">
                You have pending administrative tasks that require immediate review and approval.
            </p>
            
            <div class="notification-stats">
                <?php if ($pending_orders_count > 0): ?>
                <div class="notification-stat-card">
                    <span class="count"><?php echo $pending_orders_count; ?></span>
                    <span class="label">Pending Orders</span>
                </div>
                <?php endif; ?>
                
                <?php if ($completed_orders_count > 0): ?>
                <div class="notification-stat-card">
                    <span class="count"><?php echo $completed_orders_count; ?></span>
                    <span class="label">Orders to Finalize</span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="admin-notification-buttons">
                <?php if ($pending_orders_count > 0): ?>
                <a href="pending_orders.php" class="admin-notification-btn primary">
                    <i class="fas fa-hourglass-half"></i> Review Pending
                </a>
                <?php endif; ?>
                
                <?php if ($completed_orders_count > 0): ?>
                <a href="inprogress_orders.php" class="admin-notification-btn secondary">
                    <i class="fas fa-check-circle"></i> Finalize Orders
                </a>
                <?php endif; ?>
                
                <button type="button" class="admin-notification-btn dismiss" onclick="dismissAdminNotification()">
                    <i class="fas fa-times"></i> Later
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('error-state').style.display = 'block';
        document.getElementById('error-text').textContent = 'Chart library failed to load. Please refresh the page.';
        return;
    }
    
    const loadingState = document.getElementById('loading-state');
    const errorState = document.getElementById('error-state');
    const dashboardContent = document.getElementById('dashboard-content');
    
    // Show loading state initially
    loadingState.style.display = 'block';

    // Admin notification popup logic
    const adminNotificationOverlay = document.getElementById('adminNotificationOverlay');
    
    // Show admin notification if there are pending tasks
    if (adminNotificationOverlay) {
        setTimeout(() => {
            adminNotificationOverlay.classList.add('show');
        }, 2000); // Show after page loads and animations complete
    }
    
    
    // Optimize fetch with timeout and better error handling
    const fetchTimeout = new Promise((_, reject) =>
        setTimeout(() => reject(new Error('Request timeout')), 10000) // 10 second timeout
    );
    
    Promise.race([
        fetch('admin_data.php', {
            method: 'GET',
            headers: {
                'Cache-Control': 'no-cache'
            }
        }),
        fetchTimeout
    ])
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Hide loading, show content
            loadingState.style.display = 'none';
            dashboardContent.style.display = 'block';
            
            // Populate Stats Cards with animations
            animateCountUp('total-users', data.stats.users || 0);
            animateCountUp('total-pilots', data.stats.pilots || 0);
            animateCountUp('pending-orders', data.stats.pending_orders || 0);
            animateCountUp('completed-orders', data.stats.completed_orders || 0);
            animateCountUp('inprogress-orders', data.stats.inprogress_orders || 0);
            animateCountUp('total-revenue', data.stats.wallet_balance || 0, '₹');
            
            // Add/remove urgent animation for pending orders
            const pendingCard = document.querySelector('#pending-orders').closest('.stat-card');
            if (data.stats.pending_orders > 0) {
                pendingCard.classList.add('urgent');
            } else {
                pendingCard.classList.remove('urgent');
            }
            
            // Add/remove urgent animation for in-progress orders if there are many
            const inprogressCard = document.querySelector('#inprogress-orders').closest('.stat-card');
            if (data.stats.inprogress_orders > 5) {
                inprogressCard.classList.add('urgent');
            } else {
                inprogressCard.classList.remove('urgent');
            }
            
            // Update change indicators
            updateChangeIndicators(data.stats);
            
            // Ensure all stat cards are visible (fix for disappearing cards issue)
            ensureAllCardsVisible();
            
            // Create Charts
            try {
                createOrdersTrendChart(data.charts.ordersByDay || []);
                createServiceDistributionChart(data.charts.serviceDistribution || []);
                
                // Enforce size constraints after chart creation
                setTimeout(() => {
                    enforceChartContainerSize();
                    window.dispatchEvent(new Event('resize'));
                }, 500);
            } catch (error) {
                console.error('Chart creation error:', error);
                // Continue without charts if there's an error
            }
            
            // Populate Recent Activity
            populateRecentActivity(data.pending_actions || {});
            
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
            loadingState.style.display = 'none';
            errorState.style.display = 'block';
            document.getElementById('error-text').textContent = 'Failed to load dashboard data: ' + error.message;
        });
});

// Add resize listener for charts
window.addEventListener('resize', function() {
    // Chart.js automatically handles resize when maintainAspectRatio is false
    setTimeout(() => {
        if (window.Chart && window.Chart.instances) {
            Object.values(window.Chart.instances).forEach(chart => {
                if (chart && chart.resize) {
                    chart.resize();
                }
            });
        }
    }, 100);
});

// Enforce chart container size constraints
function enforceChartContainerSize() {
    const chartContainers = document.querySelectorAll('.chart-container, .analytics-card');
    chartContainers.forEach(container => {
        const canvas = container.querySelector('canvas');
        if (canvas) {
            // Force container height
            container.style.height = getComputedStyle(container).height;
            container.style.overflow = 'hidden';
            
            // Force canvas size
            canvas.style.maxHeight = '300px';
            canvas.style.height = '300px';
        }
    });
}

function animateCountUp(elementId, finalValue, prefix = '') {
    const element = document.getElementById(elementId);
    const duration = 2000; // 2 seconds
    const steps = 60;
    const increment = finalValue / steps;
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= finalValue) {
            current = finalValue;
            clearInterval(timer);
        }
        
        if (prefix === '₹') {
            element.textContent = prefix + Math.floor(current).toLocaleString('en-IN');
        } else {
            element.textContent = prefix + Math.floor(current);
        }
    }, duration / steps);
}

function updateChangeIndicators(stats) {
    // Update in-progress orders indicator
    if (stats.inprogress_orders > 0) {
        document.getElementById('inprogress-change').textContent = `${stats.inprogress_orders} orders in progress`;
        document.getElementById('inprogress-change').className = 'change positive';
    } else {
        document.getElementById('inprogress-change').textContent = 'No active orders';
        document.getElementById('inprogress-change').className = 'change';
    }
    
    if (stats.pending_orders > 0) {
        document.getElementById('pending-change').textContent = 'Needs attention';
        document.getElementById('pending-change').className = 'change negative';
    } else {
        document.getElementById('pending-change').textContent = 'All caught up!';
        document.getElementById('pending-change').className = 'change positive';
    }
}

function ensureAllCardsVisible() {
    // Force all stat cards to be visible regardless of their content
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.style.display = 'block';
        card.style.visibility = 'visible';
        card.style.opacity = '1';
    });
}

function createOrdersTrendChart(ordersByDay) {
    const ctx = document.getElementById('ordersChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ordersByDay.map(d => new Date(d.order_day).toLocaleDateString('en-IN', { 
                month: 'short', 
                day: 'numeric' 
            })),
            datasets: [{
                label: 'Completed Orders',
                data: ordersByDay.map(d => d.order_count),
                borderColor: '#3EB489',
                backgroundColor: 'rgba(62, 180, 137, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3EB489',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 2,
            plugins: {
                legend: {
                    labels: { color: '#E0E0E0' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#BDBDBD' },
                    grid: { color: 'rgba(64, 64, 64, 0.3)' }
                },
                x: {
                    ticks: { color: '#BDBDBD' },
                    grid: { color: 'rgba(64, 64, 64, 0.3)' }
                }
            },
            layout: {
                padding: {
                    top: 20,
                    bottom: 20
                }
            }
        }
    });
}

function createServiceDistributionChart(serviceDistribution) {
    const ctx = document.getElementById('servicesPieChart').getContext('2d');
    
    const colors = ['#3EB489', '#B0B0B0', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'];
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: serviceDistribution.map(s => s.service_type),
            datasets: [{
                data: serviceDistribution.map(s => s.count),
                backgroundColor: colors.slice(0, serviceDistribution.length),
                borderColor: '#000000',
                borderWidth: 2,
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        color: '#E0E0E0',
                        padding: 20,
                        usePointStyle: true
                    }
                }
            },
            layout: {
                padding: {
                    top: 20,
                    bottom: 20
                }
            }
        }
    });
}

function populateRecentActivity(pendingActions) {
    const activityList = document.getElementById('recent-activity-list');
    activityList.innerHTML = '';
    
    // Track order IDs to prevent duplicates
    const seenOrderIds = new Set();
    
    // Add pending orders activity first (higher priority)
    if (pendingActions.pending_orders && pendingActions.pending_orders.length > 0) {
        pendingActions.pending_orders.slice(0, 3).forEach(order => {
            if (!seenOrderIds.has(order.id)) {
                seenOrderIds.add(order.id);
                const activityItem = document.createElement('div');
                activityItem.className = 'activity-item';
                activityItem.innerHTML = `
                    <div class="activity-icon warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div style="flex: 1;">
                        <strong>New Order Pending</strong><br>
                        <small style="color: var(--text-secondary);">${order.name} - ${order.service_type}</small>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.85rem;">
                        Just now
                    </div>
                `;
                activityList.appendChild(activityItem);
            }
        });
    }
    
    // Add in-progress orders activity only if not already shown as pending
    if (pendingActions.inprogress_orders && pendingActions.inprogress_orders.length > 0) {
        pendingActions.inprogress_orders.slice(0, 2).forEach(order => {
            if (!seenOrderIds.has(order.id)) {
                seenOrderIds.add(order.id);
                const activityItem = document.createElement('div');
                activityItem.className = 'activity-item';
                activityItem.innerHTML = `
                    <div class="activity-icon info">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div style="flex: 1;">
                        <strong>Order In Progress</strong><br>
                        <small style="color: var(--text-secondary);">${order.name} - ${order.service_type}</small>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.85rem;">
                        Active
                    </div>
                `;
                activityList.appendChild(activityItem);
            }
        });
    }
    
    // Add a default message if no activity
    if (activityList.children.length === 0) {
        activityList.innerHTML = `
            <div class="activity-item">
                <div class="activity-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <div style="flex: 1;">
                    <strong>All Caught Up!</strong><br>
                    <small style="color: var(--text-secondary);">No pending activities at the moment</small>
                </div>
            </div>
        `;
    }
}

// Admin notification popup functions
function dismissAdminNotification() {
    const overlay = document.getElementById('adminNotificationOverlay');
    overlay.style.animation = 'fadeOut 0.5s ease';
    setTimeout(() => {
        overlay.style.display = 'none';
    }, 500);
    
    // Store current timestamp for temporary dismissal (5 minutes)
    const dismissTime = Date.now();
    sessionStorage.setItem('adminNotificationDismissedAt', dismissTime.toString());
}

// Auto-refresh notification data every 2 minutes
setInterval(function() {
    const dismissedAt = sessionStorage.getItem('adminNotificationDismissedAt');
    const currentTime = Date.now();
    const fiveMinutes = 5 * 60 * 1000;
    
    // Only refresh if not dismissed recently
    if (!dismissedAt || (currentTime - parseInt(dismissedAt)) >= fiveMinutes) {
        location.reload();
    }
}, 120000); // 2 minutes

// Handle escape key to close notification
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const overlay = document.getElementById('adminNotificationOverlay');
        if (overlay && overlay.style.display !== 'none') {
            dismissAdminNotification();
        }
    }
});

// Auto-show notification popup after page load
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('adminNotificationOverlay');
    const dismissedAt = sessionStorage.getItem('adminNotificationDismissedAt');
    const currentTime = Date.now();
    const fiveMinutes = 5 * 60 * 1000;
    
    // Show popup if not dismissed recently
    if (overlay && (!dismissedAt || (currentTime - parseInt(dismissedAt)) >= fiveMinutes)) {
        // Clear old dismissal timestamp
        sessionStorage.removeItem('adminNotificationDismissedAt');
        setTimeout(() => {
            overlay.style.display = 'flex';
            overlay.style.animation = 'fadeIn 0.5s ease';
        }, 1000); // Show after 1 second
    }
});
</script>

</body>
</html>