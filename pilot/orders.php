<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['pilot_phone'])) {
    header("Location: index.php");
    exit();
}
$pilot_phone = $_SESSION['pilot_phone'];
$message = '';
$message_type = 'success';

// Handle Pilot Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);

    // Pilot accepts an order (no changes here)
    if (isset($_POST['accept_order']) && $order_id) {
        $stmt = $conn->prepare("UPDATE orders SET pilot_phone = ?, order_status = 'Pilot Accepted' WHERE id = ? AND pilot_phone IS NULL");
        if ($stmt->execute([$pilot_phone, $order_id])) {
            if ($stmt->rowCount() > 0) {
                $message = "Order #" . $order_id . " accepted successfully!";
            } else {
                $message = "Sorry, this order was just taken by another pilot.";
                $message_type = "error";
            }
        }
    }

    // Pilot completes an order (no changes here)
    if (isset($_POST['complete_order']) && $order_id) {
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Order Completed' WHERE id = ? AND pilot_phone = ?");
        $stmt->execute([$order_id, $pilot_phone]);
        header("Location: orders.php");
        exit();
    }

    // NEW: Handle cancellation request from pilot
    if (isset($_POST['request_cancellation']) && $order_id) {
        $reason = trim($_POST['cancellation_reason']);
        if (!empty($reason)) {
            // Update the order status and add the reason for the admin to review
            $stmt = $conn->prepare("UPDATE orders SET order_status = 'Cancellation Requested', cancellation_reason = ? WHERE id = ? AND pilot_phone = ?");
            if ($stmt->execute([$reason, $order_id, $pilot_phone])) {
                $message = "Cancellation request sent to admin for review.";
            } else {
                 $message = "Error submitting request.";
                 $message_type = "error";
            }
        } else {
            $message = "A reason is required to request a cancellation.";
            $message_type = "error";
        }
    }
}

// Fetching logic remains the same
$available_orders = [];
$stmt = $conn->prepare("SELECT * FROM orders WHERE pilot_phone IS NULL AND order_status = 'Waiting for Pilot' ORDER BY booking_date DESC");
$stmt->execute();
$available_orders = $stmt->fetchAll();

$active_orders = [];
$stmt_active = $conn->prepare("SELECT * FROM orders WHERE pilot_phone = ? AND order_status = 'Pilot Accepted' ORDER BY booking_date DESC");
$stmt_active->execute([$pilot_phone]);
$active_orders = $stmt_active->fetchAll();

$completed_orders = [];
$stmt_completed = $conn->prepare("SELECT * FROM orders WHERE pilot_phone = ? AND order_status = 'Order Completed' ORDER BY booking_date DESC");
$stmt_completed->execute([$pilot_phone]);
$completed_orders = $stmt_completed->fetchAll();

// NEW: Fetch finalized completed orders with earnings
$finalized_orders = [];
$stmt_finalized = $conn->prepare("
    SELECT uo.*, pe.amount as pilot_earnings, pe.transaction_date as earning_date,
           'Completed Successfully' as completion_status
    FROM userordersuccess uo
    LEFT JOIN pilot_earnings pe ON uo.id = pe.order_id
    WHERE pe.pilot_phone = ? 
    ORDER BY pe.transaction_date DESC
");
$stmt_finalized->execute([$pilot_phone]);
$finalized_orders = $stmt_finalized->fetchAll();

// NEW: Fetch cancelled orders with compensation
$cancelled_orders = [];
$stmt_cancelled = $conn->prepare("
    SELECT uc.*, pe.amount as compensation_amount, pe.transaction_date as compensation_date
    FROM userordercancel uc
    LEFT JOIN pilot_earnings pe ON uc.id = pe.order_id
    WHERE pe.pilot_phone = ?
    ORDER BY pe.transaction_date DESC
");
$stmt_cancelled->execute([$pilot_phone]);
$cancelled_orders = $stmt_cancelled->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { 
            --black: #000; 
            --mint-green: #3EB489; 
            --metallic-silver: #B0B0B0; 
            --dark-gray: #1a1a1a; 
        }
        
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: var(--black); 
            color: var(--metallic-silver); 
            padding: 20px; 
        }
        
        .container { 
            max-width: 1200px; 
            margin: auto; 
        }
        
        h2 { 
            color: var(--mint-green); 
            border-bottom: 3px solid var(--mint-green); 
            padding-bottom: 10px; 
            margin: 40px 0 20px 0; 
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .order-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 25px; 
            margin-bottom: 30px;
        }
        
        .order-card { 
            background-color: var(--dark-gray); 
            border-radius: 15px; 
            border-left: 5px solid var(--mint-green); 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            opacity: 0; 
            animation: fadeInUp 0.6s ease forwards; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(62, 180, 137, 0.2);
        }
        
        .order-card-body { 
            padding: 25px; 
        }
        
        .detail-item { 
            display: flex; 
            align-items: center; 
            margin-bottom: 12px; 
            font-size: 1rem; 
        }
        
        .detail-item i { 
            color: var(--mint-green); 
            width: 25px; 
            margin-right: 10px;
        }
        
        .customer-name { 
            font-size: 1.3rem; 
            font-weight: 700; 
            color: #fff; 
            margin-bottom: 20px; 
        }
        
        .order-card-footer { 
            background-color: #111; 
            padding: 20px; 
            text-align: center; 
            margin-top: auto; 
        }
        
        .btn { 
            padding: 12px 24px; 
            border: none; 
            border-radius: 10px; 
            font-weight: 600; 
            cursor: pointer; 
            width: 100%; 
            font-size: 14px; 
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }
        
        .btn:last-child {
            margin-bottom: 0;
        }
        
        .btn-accept { 
            background-color: var(--mint-green); 
            color: var(--black); 
        }
        
        .btn-accept:hover {
            background-color: #35a074;
            transform: translateY(-2px);
        }
        
        .btn-complete { 
            background-color: #3498db; 
            color: #fff; 
        }
        
        .btn-complete:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-cancel-req { 
            background-color: #e74c3c; 
            color: #fff; 
        }
        
        .btn-cancel-req:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .placeholder { 
            font-style: italic; 
            color: #777; 
            grid-column: 1 / -1; 
            text-align: center; 
            padding: 40px; 
            background-color: var(--dark-gray);
            border-radius: 15px;
            border: 2px dashed #333;
        }
        
        .message { 
            text-align: center; 
            padding: 15px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            background-color: var(--mint-green); 
            color: var(--black); 
            font-weight: 600; 
        }
        
        .message.error { 
            background-color: #e74c3c; 
            color: #fff;
        }
        
        .earnings-card {
            border-left-color: #f39c12;
        }
        
        .cancelled-card {
            border-left-color: #e74c3c;
        }
        
        .completed-card {
            border-left-color: #27ae60;
        }
        
        .earning-amount {
            color: var(--mint-green);
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .compensation-amount {
            color: #f39c12;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .status-completed {
            background-color: #27ae60;
            color: #fff;
        }
        
        .status-cancelled {
            background-color: #e74c3c;
            color: #fff;
        }
        
        .back-link {
            color: var(--mint-green);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .back-link:hover {
            text-decoration: underline;
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

        /* Modal Styles */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.8); 
            justify-content: center; 
            align-items: center; 
        }
        
        .modal-content { 
            background-color: var(--dark-gray); 
            padding: 30px; 
            border-radius: 15px; 
            border-top: 5px solid var(--mint-green); 
            width: 90%; 
            max-width: 500px; 
            text-align: center; 
            position: relative; 
        }
        
        .close-btn { 
            position: absolute; 
            top: 15px; 
            right: 20px; 
            font-size: 24px; 
            color: var(--metallic-silver); 
            cursor: pointer; 
            transition: color 0.3s ease;
        }
        
        .close-btn:hover {
            color: var(--mint-green);
        }
        
        .modal-content h2 { 
            margin-bottom: 15px; 
            color: var(--mint-green);
        }
        
        .modal-content p { 
            margin-bottom: 20px; 
            font-size: 1rem; 
            line-height: 1.5;
        }
        
        .modal-content textarea { 
            width: 100%; 
            min-height: 120px; 
            padding: 15px; 
            background-color: #111; 
            border: 2px solid var(--metallic-silver); 
            color: #fff; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            font-family: 'Outfit', sans-serif;
            resize: vertical;
        }
        
        .modal-content textarea:focus {
            outline: none;
            border-color: var(--mint-green);
        }
        
        @media (max-width: 768px) {
            .order-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .container {
                padding: 0 10px;
            }
            
            h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <h2><i class="fas fa-box-open"></i> Available Orders (First Come, First Served)</h2>
    <div class="order-grid">
        <?php if (empty($available_orders)): ?>
            <p class="placeholder">No new orders are available for you to accept right now.</p>
        <?php else: foreach ($available_orders as $order): ?>
            <div class="order-card">
                <div class="order-card-body">
                    <div class="customer-name"><?php echo htmlspecialchars($order['name']); ?></div>
                    <div class="detail-item"><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($order['service_type']); ?></div>
                    <div class="detail-item"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($order['booking_date'])); ?></div>
                    <?php if ($order['start_time'] && $order['end_time']): ?>
                        <div class="detail-item"><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($order['start_time'])) . ' - ' . date('g:i A', strtotime($order['end_time'])); ?></div>
                        <div class="detail-item"><i class="fas fa-hourglass-half"></i> <?php echo htmlspecialchars($order['hours']); ?> Hour(s)</div>
                    <?php else: ?>
                        <div class="detail-item"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($order['hours']); ?> Hour(s)</div>
                    <?php endif; ?>
                    <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <div class="detail-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['address']); ?></div>
                    <div class="detail-item"><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($order['total_price']); ?></div>
                </div>
                <div class="order-card-footer">
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="accept_order" class="btn btn-accept">
                            <i class="fas fa-check"></i> Accept Order
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <h2><i class="fas fa-clock"></i> Your Active Orders</h2>
    <div class="order-grid">
         <?php if (empty($active_orders)): ?>
            <p class="placeholder">You have no active orders.</p>
        <?php else: foreach ($active_orders as $order): ?>
            <div class="order-card" style="border-left-color: #3498db;">
                <div class="order-card-body">
                    <div class="customer-name"><?php echo htmlspecialchars($order['name']); ?></div>
                    <div class="detail-item"><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($order['service_type']); ?></div>
                    <div class="detail-item"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($order['booking_date'])); ?></div>
                    <?php if ($order['start_time'] && $order['end_time']): ?>
                        <div class="detail-item"><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($order['start_time'])) . ' - ' . date('g:i A', strtotime($order['end_time'])); ?></div>
                        <div class="detail-item"><i class="fas fa-hourglass-half"></i> <?php echo htmlspecialchars($order['hours']); ?> Hour(s)</div>
                    <?php else: ?>
                        <div class="detail-item"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($order['hours']); ?> Hour(s)</div>
                    <?php endif; ?>
                    <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <div class="detail-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['address']); ?></div>
                    <div class="detail-item"><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($order['total_price']); ?></div>
                </div>
                <div class="order-card-footer">
                    <form method="POST" style="margin-bottom: 0;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" name="complete_order" class="btn btn-complete">
                            <i class="fas fa-check-circle"></i> Mark as Complete
                        </button>
                    </form>
                    <button class="btn btn-cancel-req" onclick="openCancelModal('<?php echo $order['id']; ?>')">
                        <i class="fas fa-times-circle"></i> Request Cancellation
                    </button>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <h2><i class="fas fa-hourglass-half"></i> Completed Orders (Awaiting Admin Finalization)</h2>
    <div class="order-grid">
        <?php if (empty($completed_orders)): ?>
            <p class="placeholder">You have no orders awaiting admin finalization.</p>
        <?php else: foreach ($completed_orders as $order): ?>
            <div class="order-card" style="border-left-color: #f39c12;">
                <div class="order-card-body">
                    <div class="customer-name"><?php echo htmlspecialchars($order['name']); ?></div>
                    <div class="detail-item"><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($order['service_type']); ?></div>
                    <div class="detail-item"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($order['booking_date'])); ?></div>
                    <?php if ($order['start_time'] && $order['end_time']): ?>
                        <div class="detail-item"><i class="fas fa-clock"></i> <?php echo date('g:i A', strtotime($order['start_time'])) . ' - ' . date('g:i A', strtotime($order['end_time'])); ?></div>
                        <div class="detail-item"><i class="fas fa-hourglass-half"></i> <?php echo htmlspecialchars($order['hours']); ?> Hour(s)</div>
                    <?php else: ?>
                        <div class="detail-item"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($order['hours']); ?> Hour(s)</div>
                    <?php endif; ?>
                    <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <div class="detail-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['address']); ?></div>
                    <div class="detail-item"><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($order['total_price']); ?></div>
                </div>
                <div class="order-card-footer">
                    <div style="background-color: #f39c12; color: #000; padding: 10px; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-clock"></i> Waiting for Admin Approval
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <h2 id="completed"><i class="fas fa-trophy"></i> Finalized Orders & Earnings</h2>
    <div class="order-grid">
        <?php if (empty($finalized_orders)): ?>
            <p class="placeholder">No finalized orders yet. Complete some orders to see your earnings here!</p>
        <?php else: foreach ($finalized_orders as $order): ?>
            <div class="order-card earnings-card completed-card">
                <div class="order-card-body">
                    <span class="status-badge status-completed"><?php echo htmlspecialchars($order['completion_status']); ?></span>
                    <div class="customer-name"><?php echo htmlspecialchars($order['name']); ?></div>
                    <div class="detail-item"><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($order['service_type']); ?></div>
                    <div class="detail-item"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($order['booking_date'])); ?></div>
                    <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <div class="detail-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['address']); ?></div>
                    <div class="detail-item"><i class="fas fa-rupee-sign"></i> Order Value: ₹<?php echo number_format($order['total_price']); ?></div>
                    <div class="detail-item earning-amount"><i class="fas fa-money-bill-wave"></i> Your Earning: ₹<?php echo number_format($order['pilot_earnings'], 2); ?></div>
                    <div class="detail-item"><i class="fas fa-clock"></i> Earned on: <?php echo date('d M Y', strtotime($order['earning_date'])); ?></div>
                </div>
                <div class="order-card-footer">
                    <div style="background-color: #27ae60; color: #fff; padding: 10px; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-check-double"></i> Payment Completed
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <?php if (!empty($cancelled_orders)): ?>
    <h2><i class="fas fa-exclamation-triangle"></i> Cancelled Orders with Compensation</h2>
    <div class="order-grid">
        <?php foreach ($cancelled_orders as $order): ?>
            <div class="order-card cancelled-card">
                <div class="order-card-body">
                    <span class="status-badge status-cancelled">Cancelled on User Request</span>
                    <div class="customer-name"><?php echo htmlspecialchars($order['name']); ?></div>
                    <div class="detail-item"><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($order['service_type']); ?></div>
                    <div class="detail-item"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($order['booking_date'])); ?></div>
                    <div class="detail-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <div class="detail-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['address']); ?></div>
                    <div class="detail-item"><i class="fas fa-rupee-sign"></i> Order Value: ₹<?php echo number_format($order['total_price']); ?></div>
                    <div class="detail-item compensation-amount"><i class="fas fa-gift"></i> Compensation: ₹<?php echo number_format($order['compensation_amount'], 2); ?></div>
                    <div class="detail-item"><i class="fas fa-clock"></i> Compensated on: <?php echo date('d M Y', strtotime($order['compensation_date'])); ?></div>
                </div>
                <div class="order-card-footer">
                    <div style="background-color: #f39c12; color: #000; padding: 10px; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-handshake"></i> Compensation Paid
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeCancelModal()">&times;</span>
        <h2><i class="fas fa-exclamation-triangle"></i> Request Order Cancellation</h2>
        <p>Please provide a clear reason for cancelling this order. This will be sent to the admin for approval before the order is moved to the refund section.</p>
        <form method="POST">
            <input type="hidden" name="order_id" id="cancel-order-id">
            <textarea name="cancellation_reason" placeholder="e.g., Customer cancelled upon arrival at the location, weather conditions unsafe, equipment malfunction, etc..." required></textarea>
            <button type="submit" name="request_cancellation" class="btn btn-cancel-req">
                <i class="fas fa-paper-plane"></i> Submit Cancellation Request
            </button>
        </form>
    </div>
</div>

<script>
    function openCancelModal(orderId) {
        document.getElementById('cancel-order-id').value = orderId;
        document.getElementById('cancelModal').style.display = 'flex';
    }
    
    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('cancelModal');
        if (event.target == modal) {
            closeCancelModal();
        }
    }
    
    // Add animation delays for cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.order-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = (index * 0.1) + 's';
        });
    });
</script>
</body>
</html>