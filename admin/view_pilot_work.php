<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

// Get the pilot's phone number from the URL
$pilot_phone = $_GET['phone'] ?? '';
if (empty($pilot_phone)) {
    die("No pilot selected.");
}

// Fetch Pilot's details
$pilot_stmt = $conn->prepare("SELECT name FROM pilot WHERE phone = ?");
$pilot_stmt->execute([$pilot_phone]);
$pilot = $pilot_stmt->fetch(PDO::FETCH_ASSOC);

if (!$pilot) {
    die("Pilot not found.");
}

// Fetch Pilot's earnings stats
$stats_stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total_earned, COUNT(*) as jobs_completed FROM pilot_earnings WHERE pilot_phone = ?");
$stats_stmt->execute([$pilot_phone]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Pilot's earnings history
$history_stmt = $conn->prepare("
    SELECT pe.amount, pe.transaction_date, uo.service_type, uo.name as customer_name
    FROM pilot_earnings pe
    JOIN userordersuccess uo ON pe.order_id = uo.id
    WHERE pe.pilot_phone = ?
    ORDER BY pe.transaction_date DESC
");
$history_stmt->execute([$pilot_phone]);
$history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pilot Work History</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        h1 { color: var(--mint-green); }
        h1 small { font-size: 1.2rem; color: var(--metallic-silver); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .stat-card { background-color: var(--dark-gray); padding: 25px; border-radius: 15px; text-align: center; }
        .stat-card .value { font-size: 2rem; font-weight: 700; color: #fff; }
        .stat-card .title { font-size: 1rem; }
        table { width: 100%; border-collapse: collapse; background-color: var(--dark-gray); }
        th, td { padding: 12px 15px; border: 1px solid #333; text-align: left; }
        th { background-color: var(--mint-green); color: var(--black); }
        .back-link { color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="pilots.php" class="back-link">&larr; Back to Pilots List</a>
        <h1>Work History <small>for <?php echo htmlspecialchars($pilot['name']); ?></small></h1>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="value">₹<?php echo number_format($stats['total_earned'], 2); ?></div>
                <div class="title">Total Earned</div>
            </div>
            <div class="stat-card">
                <div class="value"><?php echo $stats['jobs_completed']; ?></div>
                <div class="title">Jobs Completed</div>
            </div>
        </div>

        <h2>Earnings History</h2>
        <table>
            <thead>
                <tr>
                    <th>Completion Date</th>
                    <th>Customer Name</th>
                    <th>Service Type</th>
                    <th>Amount Earned</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr><td colspan="4" style="text-align: center;">This pilot has no completed jobs.</td></tr>
                <?php else: foreach ($history as $item): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($item['transaction_date'])); ?></td>
                        <td><?php echo htmlspecialchars($item['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['service_type']); ?></td>
                        <td>₹<?php echo number_format($item['amount'], 2); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>