<?php
session_start();
require_once '../config/db.php';

// FIX: Using the correct, admin-specific session variable.
if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

// FIX: This query is rewritten to be simpler and more reliable.
// It correctly joins the three tables to get all the necessary information.
$stmt = $conn->prepare("
    SELECT
        uo.*,
        p.name AS pilot_name,
        pe.pilot_phone,
        pe.amount AS pilot_earnings
    FROM
        userordersuccess uo
    LEFT JOIN
        pilot_earnings pe ON uo.id = pe.order_id
    LEFT JOIN
        pilot p ON pe.pilot_phone = p.phone
    ORDER BY
        uo.created_at DESC
");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Completed Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); padding: 20px; }
        .container { max-width: 1400px; margin: auto; }
        h1 { color: var(--mint-green); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background-color: var(--dark-gray); }
        th, td { padding: 12px 15px; border: 1px solid #333; text-align: left; vertical-align: middle; }
        th { background-color: var(--mint-green); color: var(--black); }
        tr:hover { background-color: #2a2a2a; }
        .back-link { color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <h1>Completed Orders History</h1>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Service Type</th>
                    <th>Total Price</th>
                    <th>Completed By (Pilot)</th>
                    <th>Pilot Earnings</th>
                    <th>Completion Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result)): ?>
                    <tr><td colspan="7" style="text-align:center;">No orders have been completed yet.</td></tr>
                <?php else: foreach ($result as $order) : ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['service_type']); ?></td>
                        <td>₹<?php echo number_format($order['total_price']); ?></td>
                        <td>
                            <?php if ($order['pilot_name']): ?>
                                <strong><?php echo htmlspecialchars($order['pilot_name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['pilot_phone']); ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>₹<?php echo number_format($order['pilot_earnings'] ?? 0); ?></td>
                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>