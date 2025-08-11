<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

// Handle form submission: Process a refund
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_refund'])) {
    $cancel_id = filter_input(INPUT_POST, 'cancel_id', FILTER_VALIDATE_INT);
    $refund_utr = trim($_POST['refund_utr']);

    if ($cancel_id && !empty($refund_utr)) {
        $stmt = $conn->prepare("UPDATE userordercancel SET refund_status = 'Completed', refund_utr = ? WHERE id = ?");
        $stmt->execute([$refund_utr, $cancel_id]);
        header("Location: refund_requests.php");
        exit();
    } else {
        echo "<script>alert('Please provide a valid UTR number.');</script>";
    }
}

// Fetch all cancelled orders
$stmt = $conn->prepare("SELECT * FROM userordercancel ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Refund Requests</title>
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
        .status { padding: 5px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status.pending { background-color: #f39c12; color: #000; }
        .status.completed { background-color: var(--mint-green); color: #000; }
        input[type="text"] { width: 150px; padding: 8px; background-color: #333; color: #fff; border: 1px solid var(--metallic-silver); border-radius: 5px; }
        button { background-color: #3498db; color: #fff; font-weight: bold; cursor: pointer; padding: 8px 12px; border: none; border-radius: 5px; }
        .back-link { color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <h1>Refund Requests & History</h1>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Service Details</th>
                    <th>Refund Amount</th>
                    <th>Cancellation Date</th>
                    <th>Refund Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result)): ?>
                    <tr><td colspan="6" style="text-align:center;">No refund requests found.</td></tr>
                <?php else: foreach ($result as $order) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['service_type']); ?></td>
                        <td>₹<?php echo number_format($order['total_price']); ?></td>
                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        <td>
                             <?php 
                                $status = htmlspecialchars($order['refund_status']);
                                $status_class = ($status === 'Pending') ? 'pending' : 'completed';
                                echo "<span class='status $status_class'>$status</span>";
                            ?>
                        </td>
                        <td>
                            <?php if ($order['refund_status'] === 'Pending'): ?>
                                <form method="POST" action="refund_requests.php">
                                    <input type="hidden" name="cancel_id" value="<?php echo $order['id']; ?>">
                                    <input type="text" name="refund_utr" placeholder="Enter Refund UTR" required>
                                    <button type="submit" name="process_refund">Complete Refund</button>
                                </form>
                            <?php else: ?>
                                <strong>UTR:</strong> <?php echo htmlspecialchars($order['refund_utr']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>