<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

// --- NEW, MORE ROBUST LOGIC FOR APPROVAL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_order'])) {
    
    // Get the Order ID from the button that was clicked. The key of the array is the ID.
    $orderId = key($_POST['approve_order']);
    // --- DEBUGGING LINE 14 ---
    echo "<script>console.log('DEBUG: Value from key(\$_POST[\'approve_order\']): " . addslashes($orderId) . "');</script>";
    $orderId = (int)$orderId;
    // --- DEBUGGING LINE 15 ---
    echo "<script>console.log('DEBUG: Value of orderId after casting to int: " . $orderId . "');</script>";

    if ($orderId > 0) {
        // This logic is now correct: it simply updates the status.
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Waiting for Pilot' WHERE id = ? AND order_status = 'Pending Admin Approval'");
        
        if ($stmt->execute([$orderId])) {
            header("Location: pending_orders.php");
            exit();
        } else {
            echo "<script>alert('Error: Could not approve the order.');</script>";
        }
    } else {
        echo "<script>alert('An invalid Order ID was received. Please try again.');</script>";
    }
}

// Fetch pending orders directly from the 'orders' table
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_status = 'Pending Admin Approval' ORDER BY id ASC");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pending Orders Approval</title>
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
        .screenshot { max-width: 100px; border-radius: 8px; cursor: pointer; transition: transform 0.2s; }
        .screenshot:hover { transform: scale(2.5); z-index: 10; }
        button { background-color: var(--mint-green); color: var(--black); font-weight: bold; cursor: pointer; padding: 8px 12px; border: none; border-radius: 5px; }
        .back-link { color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <h1>Pending Orders - Verification</h1>
        
        <form method="POST" action="pending_orders.php">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Service Details</th>
                        <th>Payment Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result)): ?>
                        <tr><td colspan="4" style="text-align:center;">No pending orders to verify.</td></tr>
                    <?php else: foreach ($result as $order) : ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($order['name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['phone']); ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($order['service_type']); ?></strong><br>
                                Date: <?php echo date('d M Y', strtotime($order['booking_date'])); ?><br>
                                <?php if ($order['start_time'] && $order['end_time']): ?>
                                    Time: <?php echo date('g:i A', strtotime($order['start_time'])) . ' - ' . date('g:i A', strtotime($order['end_time'])); ?><br>
                                    Duration: <?php echo htmlspecialchars($order['hours']); ?> Hour(s)<br>
                                <?php else: ?>
                                    Duration: <?php echo htmlspecialchars($order['hours']); ?> Hour(s)<br>
                                <?php endif; ?>
                                Price: ₹<?php echo number_format($order['total_price']); ?>
                            </td>
                            <td>
                                <strong>Txn ID:</strong> <?php echo htmlspecialchars($order['transaction_id']); ?><br>
                                <a href="../user/<?php echo htmlspecialchars($order['payment_screenshot']); ?>" target="_blank">
                                    <img src="../user/<?php echo htmlspecialchars($order['payment_screenshot']); ?>" class="screenshot" alt="Payment Screenshot">
                                </a>
                            </td>
                            <td>
                                <button type="submit" name="approve_order[<?php echo $order['id']; ?>]">Approve for Pilots</button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>