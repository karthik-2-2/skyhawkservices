<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

// Logic for finalizing a completed order (no changes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_order'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $pilot_phone = $_POST['pilot_phone'] ?? '';
    $pilot_earnings = filter_input(INPUT_POST, 'pilot_earnings', FILTER_VALIDATE_FLOAT);

    if ($order_id && !empty($pilot_phone) && $pilot_earnings >= 0) {
        $conn->beginTransaction();
        try {
            $stmt_select = $conn->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt_select->execute([$order_id]);
            $orderData = $stmt_select->fetch();

            if ($orderData) {
                $insertSuccessStmt = $conn->prepare("INSERT INTO userordersuccess (name, email, phone, address, service_type, booking_date, hours, start_time, end_time, total_price, additional_msg) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insertSuccessStmt->execute([$orderData['name'], $orderData['email'], $orderData['phone'], $orderData['address'], $orderData['service_type'], $orderData['booking_date'], $orderData['hours'], $orderData['start_time'], $orderData['end_time'], $orderData['total_price'], $orderData['additional_msg']]);
                $completed_order_id = $conn->lastInsertId();

                $insertEarningsStmt = $conn->prepare("INSERT INTO pilot_earnings (order_id, pilot_phone, amount) VALUES (?, ?, ?)");
                $insertEarningsStmt->execute([$completed_order_id, $pilot_phone, $pilot_earnings]);

                $deleteStmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $deleteStmt->execute([$order_id]);

                $conn->commit();
                header("Location: inprogress_orders.php");
                exit();
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid details. Please ensure an amount is entered.');</script>";
    }
}
// NEW: Handle cancellation approval/denial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancellation_action'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $action = $_POST['cancellation_action'];

    if ($order_id) {
        if ($action === 'approve') {
            $conn->beginTransaction();
            try {
                $stmt_select = $conn->prepare("SELECT * FROM orders WHERE id = ?");
                $stmt_select->execute([$order_id]);
                $orderData = $stmt_select->fetch();
                
                if ($orderData) {
                    $stmt_insert = $conn->prepare("INSERT INTO userordercancel (name, email, phone, address, service_type, booking_date, hours, start_time, end_time, total_price, additional_msg, refund_status, cancellation_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)");
                    $stmt_insert->execute([
                        $orderData['name'], $orderData['email'], $orderData['phone'], 
                        $orderData['address'], $orderData['service_type'], $orderData['booking_date'], 
                        $orderData['hours'], $orderData['start_time'], $orderData['end_time'], $orderData['total_price'], $orderData['additional_msg'], $orderData['cancellation_reason']
                    ]);

                    $stmt_delete = $conn->prepare("DELETE FROM orders WHERE id = ?");
                    $stmt_delete->execute([$order_id]);
                    $conn->commit();
                }
            } catch (Exception $e) {
                $conn->rollback();
            }
        } elseif ($action === 'deny') {
            $stmt = $conn->prepare("UPDATE orders SET order_status = 'Pilot Accepted', cancellation_reason = NULL WHERE id = ?");
            $stmt->execute([$order_id]);
        }
        header("Location: inprogress_orders.php");
        exit();
    }
}

// Updated Query to fetch all relevant in-progress statuses
$stmt = $conn->prepare("
    SELECT o.*, p.name as pilot_name, p.phone as pilot_contact_phone
    FROM orders o LEFT JOIN pilot p ON o.pilot_phone = p.phone
    WHERE o.order_status IN ('Waiting for Pilot', 'Pilot Accepted', 'Order Completed', 'Cancellation Requested')
    ORDER BY CASE o.order_status 
        WHEN 'Cancellation Requested' THEN 1
        WHEN 'Order Completed' THEN 2
        WHEN 'Pilot Accepted' THEN 3
        WHEN 'Waiting for Pilot' THEN 4
    END, o.booking_date ASC
");
$stmt->execute();
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>In-Progress Orders</title>
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
        .status.waiting { background-color: #f39c12; color: #000; }
        .status.accepted { background-color: #3498db; color: #fff; }
        .status.completed { background-color: var(--mint-green); color: #000; }
        .status.cancelled { background-color: #e74c3c; color: #fff; } /* NEW */
        input[type="number"] { width: 100px; padding: 8px; background-color: #333; color: #fff; border: 1px solid var(--metallic-silver); border-radius: 5px; }
        button { font-weight: bold; cursor: pointer; padding: 8px 12px; border: none; border-radius: 5px; }
        .btn-finalize { background-color: var(--mint-green); color: var(--black); }
        .btn-approve { background-color: #28a745; color: white; } /* NEW */
        .btn-deny { background-color: #95a5a6; color: #000; } /* NEW */
        .reason { font-style: italic; color: #f1c40f; margin-top: 5px; font-size: 0.9em; max-width: 250px; } /* NEW */
        .back-link { color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <h1>In-Progress & Actionable Orders</h1>
        <table>
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Service Details</th>
                    <th>Price & Hours</th>
                    <th>Assigned Pilot</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="6" style="text-align:center;">No orders are currently in progress.</td></tr>
                <?php else: foreach ($orders as $order) : ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($order['name']); ?></strong><br>
                            <?php echo htmlspecialchars($order['phone']); ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($order['service_type']); ?></strong><br>
                            Date: <?php echo date('d M Y', strtotime($order['booking_date'])); ?><br>
                            <?php if ($order['start_time'] && $order['end_time']): ?>
                                Time: <?php echo date('g:i A', strtotime($order['start_time'])) . ' - ' . date('g:i A', strtotime($order['end_time'])); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong>₹<?php echo number_format($order['total_price']); ?></strong><br>
                            <small><?php echo htmlspecialchars($order['hours']); ?> Hour(s)</small>
                        </td>
                        <td>
                            <?php if ($order['pilot_name']): ?>
                                <strong><?php echo htmlspecialchars($order['pilot_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($order['pilot_contact_phone']); ?></small>
                            <?php else: ?>
                                <span style="font-style: italic; color: #BDBDBD;">Waiting for Pilot</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $status = htmlspecialchars($order['order_status']);
                                $status_class = 'waiting';
                                if ($status === 'Pilot Accepted') $status_class = 'accepted';
                                if ($status === 'Order Completed') $status_class = 'completed';
                                if ($status === 'Cancellation Requested') $status_class = 'cancelled';
                                echo "<span class='status $status_class'>$status</span>";
                            ?>
                        </td>
                        <td>
                            <?php if ($order['order_status'] === 'Cancellation Requested'): ?>
                                <div class="reason">Reason: "<?php echo htmlspecialchars($order['cancellation_reason']); ?>"</div>
                                <form method="POST" style="display:inline-block; margin-top:10px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="cancellation_action" value="approve" class="btn-approve">Approve Cancel</button>
                                </form>
                                <form method="POST" style="display:inline-block; margin-top:10px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="cancellation_action" value="deny" class="btn-deny">Deny</button>
                                </form>
                            <?php elseif ($order['order_status'] === 'Order Completed'): ?>
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="pilot_phone" value="<?php echo htmlspecialchars($order['pilot_phone']); ?>">
                                    <input type="number" name="pilot_earnings" placeholder="Earnings ₹" step="0.01" required>
                                    <button type="submit" name="finalize_order" class="btn-finalize">Finalize</button>
                                </form>
                            <?php else: ?>
                                <span>No action required.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>