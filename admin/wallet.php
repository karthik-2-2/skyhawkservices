<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

// Handle Accept/Cancel actions for wallet requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id']);

    if ($action === 'accept') {
        $conn->beginTransaction();
        try {
            $stmt = $conn->prepare("SELECT phone, amount, txn_id FROM userwallet WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if ($data) {
                // Insert into main wallet table
                $stmt_insert = $conn->prepare("INSERT INTO wallet (phone, date, amount, utr, additional_msg) VALUES (?, CURRENT_DATE, ?, ?, 'Payment Accepted')");
                $stmt_insert->execute([$data['phone'], $data['amount'], $data['txn_id']]);

                // Delete from pending userwallet table
                $stmt_delete = $conn->prepare("DELETE FROM userwallet WHERE id = ?");
                $stmt_delete->execute([$id]);
                
                $conn->commit();
            }
        } catch (Exception $e) {
            $conn->rollback();
        }

    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("DELETE FROM userwallet WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: wallet.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); padding: 20px; }
        .container { max-width: 1400px; margin: auto; }
        h1 { color: var(--mint-green); }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background-color: var(--dark-gray);
            padding: 25px;
            border-radius: 15px;
            border-bottom: 4px solid var(--mint-green);
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFadeIn 0.5s ease forwards;
        }
        .stat-card .value { font-size: 2rem; font-weight: 700; color: #fff; }
        .stat-card .title { font-size: 1rem; }
        
        .chart-container { background-color: var(--dark-gray); padding: 30px; border-radius: 15px; margin-bottom: 40px; }
        h2 { color: var(--mint-green); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background-color: var(--dark-gray); }
        th, td { padding: 12px 15px; border: 1px solid #333; }
        th { background-color: var(--mint-green); color: var(--black); }
        .btn { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-accept { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #dc3545; color: white; }
        .back-link { color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px; }
        @keyframes slideUpFadeIn { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Main Dashboard</a>
        <h1>Financial & Wallet Dashboard</h1>

        <div class="stats-grid">
            <div class="stat-card" style="animation-delay: 0.2s;">
                <div class="value" id="total-revenue">₹--</div>
                <div class="title">Total Revenue</div>
            </div>
            <div class="stat-card" style="animation-delay: 0.4s;">
                <div class="value" id="total-payout">₹--</div>
                <div class="title">Total Payout to Pilots</div>
            </div>
            <div class="stat-card" style="animation-delay: 0.6s;">
                <div class="value" id="net-profit">₹--</div>
                <div class="title">Net Profit</div>
            </div>
        </div>

        <div class="chart-container">
            <h2>Revenue (Last 30 Days)</h2>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('wallet_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Populate Stats Cards
            document.getElementById('total-revenue').textContent = `₹${parseFloat(data.stats.total_revenue).toLocaleString('en-IN')}`;
            document.getElementById('total-payout').textContent = `₹${parseFloat(data.stats.total_payout).toLocaleString('en-IN')}`;
            document.getElementById('net-profit').textContent = `₹${parseFloat(data.stats.net_profit).toLocaleString('en-IN')}`;

            // Render Revenue Chart
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.chart_data.map(d => new Date(d.date).toLocaleDateString('en-GB', {day: 'numeric', month: 'short'})),
                    datasets: [{
                        label: 'Daily Revenue',
                        data: data.chart_data.map(d => d.daily_revenue),
                        backgroundColor: 'rgba(59, 255, 20, 0.2)',
                        borderColor: 'rgba(59, 255, 20, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
        })
        .catch(error => console.error('Error fetching wallet data:', error));
});
</script>
</body>
</html>