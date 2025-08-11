<?php
session_start();
include("../config/db.php");

// FIX: Using the correct, pilot-specific session variable.
if (!isset($_SESSION['pilot_phone'])) {
    header('Location: index.php');
    exit();
}
$pilot_phone = $_SESSION['pilot_phone'];

// --- FETCH PILOT EARNINGS DATA ---

// 1. Fetch Key Statistics
$stats_stmt = $conn->prepare("
    SELECT 
        COALESCE(SUM(amount), 0) as total_earned, 
        COALESCE(COUNT(*), 0) as jobs_completed 
    FROM pilot_earnings 
    WHERE pilot_phone = ?
");
$stats_stmt->execute([$pilot_phone]);
$stats_result = $stats_stmt->fetch(PDO::FETCH_ASSOC);
$total_earned = $stats_result['total_earned'];
$jobs_completed = $stats_result['jobs_completed'];
$avg_per_job = ($jobs_completed > 0) ? ($total_earned / $jobs_completed) : 0;

// 2. Fetch Detailed Earnings History
$earnings_history = [];
$history_stmt = $conn->prepare("
    SELECT pe.amount, pe.transaction_date, uo.service_type 
    FROM pilot_earnings pe
    JOIN userordersuccess uo ON pe.order_id = uo.id
    WHERE pe.pilot_phone = ?
    ORDER BY pe.transaction_date DESC
");
$history_stmt->execute([$pilot_phone]);
$earnings_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);


// 3. Fetch Data for Charts (Last 30 Days) - Updated for PostgreSQL
$chart_data = [];
$chart_stmt = $conn->prepare("
    SELECT 
        DATE(transaction_date) as date, 
        SUM(amount) as daily_total
    FROM pilot_earnings
    WHERE pilot_phone = ? AND transaction_date >= CURRENT_DATE - INTERVAL '30 days'
    GROUP BY DATE(transaction_date)
    ORDER BY date ASC
");
$chart_stmt->execute([$pilot_phone]);
$chart_data = $chart_stmt->fetchAll(PDO::FETCH_ASSOC);

// No need to close PDO connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .header { text-align: center; margin-bottom: 40px; }
        h1 { color: var(--mint-green); font-size: 2.5rem; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background-color: var(--dark-gray);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            border-bottom: 4px solid var(--mint-green);
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFadeIn 0.5s ease forwards;
        }
        .stat-card .icon { font-size: 2.5rem; color: var(--mint-green); margin-bottom: 15px; }
        .stat-card .value { font-size: 2rem; font-weight: 700; color: #fff; }
        .stat-card .title { font-size: 1rem; }
        
        .chart-container {
            background-color: var(--dark-gray);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
        }
        .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .chart-header h2 { color: var(--mint-green); margin: 0; }
        .time-buttons button { background: #333; color: var(--metallic-silver); border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; }
        .time-buttons button.active { background-color: var(--mint-green); color: var(--black); font-weight: bold; }

        .history-table { width: 100%; border-collapse: collapse; }
        .history-table th, .history-table td { padding: 15px; border-bottom: 1px solid #333; text-align: left; }
        .history-table th { color: var(--mint-green); }
        .history-table tr:last-child td { border-bottom: none; }
        .history-table tr { opacity: 0; animation: fadeIn 0.5s ease forwards; }

        @keyframes slideUpFadeIn { to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { to { opacity: 1; } }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" style="color: var(--mint-green); text-decoration: none; font-weight: 600; margin-bottom: 30px; display: inline-block;">&larr; Back to Dashboard</a>
        <div class="header">
            <h1>My Wallet & Earnings</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card" style="animation-delay: 0.2s;">
                <div class="icon"><i class="fas fa-wallet"></i></div>
                <div class="value">₹<?php echo number_format($total_earned, 2); ?></div>
                <div class="title">Total Earned</div>
            </div>
            <div class="stat-card" style="animation-delay: 0.4s;">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="value"><?php echo $jobs_completed; ?></div>
                <div class="title">Jobs Completed</div>
            </div>
            <div class="stat-card" style="animation-delay: 0.6s;">
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <div class="value">₹<?php echo number_format($avg_per_job, 2); ?></div>
                <div class="title">Average Per Job</div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h2>Earnings Overview</h2>
                <div class="time-buttons">
                    <button id="btn7Days">Last 7 Days</button>
                    <button id="btn30Days" class="active">Last 30 Days</button>
                </div>
            </div>
            <canvas id="earningsChart"></canvas>
        </div>

        <div class="history-container">
            <h2>Earnings History</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service Type</th>
                        <th>Amount Earned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($earnings_history)): ?>
                        <tr><td colspan="3" style="text-align: center;">No earnings recorded yet.</td></tr>
                    <?php else: foreach ($earnings_history as $index => $item): ?>
                        <tr style="animation-delay: <?php echo $index * 100; ?>ms">
                            <td><?php echo date('d M Y', strtotime($item['transaction_date'])); ?></td>
                            <td><?php echo htmlspecialchars($item['service_type']); ?></td>
                            <td>₹<?php echo number_format($item['amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawData = <?php echo json_encode($chart_data); ?>;
        
        const processData = (days) => {
            const labels = [];
            const dataPoints = [];
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - (days - 1));

            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                labels.push(d.toLocaleDateString('en-CA')); // YYYY-MM-DD format
            }

            labels.forEach(label => {
                const found = rawData.find(item => item.date === label);
                dataPoints.push(found ? found.daily_total : 0);
            });
            
            return {
                labels: labels.map(l => new Date(l).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })),
                data: dataPoints
            };
        };

        const ctx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Daily Earnings',
                    data: [],
                    backgroundColor: 'rgba(59, 255, 20, 0.3)',
                    borderColor: 'rgba(59, 255, 20, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                    barThickness: 'flex'
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });

        const updateChart = (days) => {
            const { labels, data } = processData(days);
            earningsChart.data.labels = labels;
            earningsChart.data.datasets[0].data = data;
            earningsChart.update();

            document.getElementById('btn7Days').classList.toggle('active', days === 7);
            document.getElementById('btn30Days').classList.toggle('active', days === 30);
        };

        document.getElementById('btn7Days').addEventListener('click', () => updateChart(7));
        document.getElementById('btn30Days').addEventListener('click', () => updateChart(30));

        // Initial chart load
        updateChart(30);
    });
</script>
</body>
</html>