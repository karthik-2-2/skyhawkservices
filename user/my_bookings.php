<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include("../config/db.php");

// --- START: NEW ROBUST AUTHENTICATION CHECK ---

// 1. Check if the user-specific session variable is set.
if (!isset($_SESSION['user_phone'])) {
    header('Location: index.php');
    exit();
}

$phone_session = $_SESSION['user_phone'];

// 2. Check if the user actually exists in the database.
$stmt_verify = $conn->prepare("SELECT id FROM \"user\" WHERE phone = ?");
$stmt_verify->execute([$phone_session]);

// 3. If no user is found, destroy the invalid session and redirect to login.
if ($stmt_verify->rowCount() === 0) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
// --- END: NEW ROBUST AUTHENTICATION CHECK ---


$message = '';
$message_type = 'success';

// --- CANCEL BOOKING LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);

    if ($booking_id) {
        $conn->beginTransaction();
        try {
            $stmt_select = $conn->prepare("SELECT * FROM orders WHERE id = ? AND phone = ?");
            $stmt_select->execute([$booking_id, $phone_session]);
            $booking_data = $stmt_select->fetch();

            if ($booking_data) {
                $stmt_insert = $conn->prepare("INSERT INTO userordercancel (name, email, phone, address, service_type, booking_date, hours, start_time, end_time, total_price, additional_msg, refund_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt_insert->execute([
                    $booking_data['name'], $booking_data['email'], $booking_data['phone'], 
                    $booking_data['address'], $booking_data['service_type'], $booking_data['booking_date'], 
                    $booking_data['hours'], $booking_data['start_time'], $booking_data['end_time'], 
                    $booking_data['total_price'], $booking_data['additional_msg']
                ]);
                
                $stmt_delete = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $stmt_delete->execute([$booking_id]);
                
                $conn->commit();
                header("Location: my_bookings.php");
                exit();
            }
        } catch (Exception $e) {
            $conn->rollback();
        }
    }
}

// --- SUBMIT REVIEW LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $review = trim($_POST['review']);

    if ($order_id && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("UPDATE userordersuccess SET rating = ?, review = ? WHERE id = ? AND phone = ?");
        if ($stmt->execute([$rating, $review, $order_id, $phone_session])) {
            $message = "Thank you for your feedback!";
            // Redirect to remove the popup
            header("Location: my_bookings.php?review_submitted=1");
            exit();
        } else {
            $message = "Error submitting review.";
            $message_type = "error";
        }
    }
}

// Check for completed orders that need reviews (for popup)
$stmt_pending_reviews = $conn->prepare("SELECT id, service_type, booking_date, total_price FROM userordersuccess WHERE phone = ? AND (rating IS NULL OR rating = 0) ORDER BY created_at DESC LIMIT 1");
$stmt_pending_reviews->execute([$phone_session]);
$pending_review = $stmt_pending_reviews->fetch();

// --- DATA FETCHING (Now guaranteed to work for a valid user) ---
// Fetch active bookings
$active_bookings = [];
$stmt_active = $conn->prepare("
    SELECT o.id, o.service_type, o.booking_date, o.hours, o.start_time, o.end_time, o.total_price, o.address, o.order_status,
           p.name as pilot_name, p.phone as pilot_phone
    FROM orders o
    LEFT JOIN pilot p ON o.pilot_phone = p.phone
    WHERE o.phone = ? ORDER BY o.booking_date DESC
");
$stmt_active->execute([$phone_session]);
while ($row = $stmt_active->fetch()) {
    $active_bookings[] = $row;
}

// Fetch past bookings
$past_bookings = [];
$stmt_past = $conn->prepare("SELECT id, service_type, booking_date, hours, start_time, end_time, total_price, rating, review FROM userordersuccess WHERE phone = ? ORDER BY booking_date DESC");
$stmt_past->execute([$phone_session]);
while ($row = $stmt_past->fetch()) {
    $past_bookings[] = $row;
}

// Fetch cancelled bookings
$cancelled_bookings = [];
$stmt_cancel = $conn->prepare("SELECT id, service_type, booking_date, hours, start_time, end_time, total_price, refund_status, refund_utr, created_at FROM userordercancel WHERE phone = ? ORDER BY created_at DESC");
$stmt_cancel->execute([$phone_session]);
while ($row = $stmt_cancel->fetch()) {
    $cancelled_bookings[] = $row;
}

function getStatusDetails($status) {
    switch ($status) {
        case 'Pending Admin Approval':
            return ['text' => 'Pending Admin Approval', 'class' => 'pending'];
        case 'Waiting for Pilot':
            return ['text' => 'Admin Approved, Waiting for Pilot', 'class' => 'approved'];
        case 'Pilot Accepted':
            return ['text' => 'Pilot Accepted', 'class' => 'assigned'];
        case 'Order Completed':
            return ['text' => 'Order Completed by Pilot', 'class' => 'completed'];
        default:
            return ['text' => $status, 'class' => 'pending'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        h2 { color: var(--mint-green); border-bottom: 2px solid var(--mint-green); padding-bottom: 10px; margin-bottom: 30px; }
        .bookings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }

        .booking-card {
            background-color: var(--dark-gray);
            border-radius: 15px;
            overflow: hidden;
            border-left: 5px solid var(--mint-green);
            opacity: 0;
            transform: translateY(30px);
            animation: slideUpFadeIn 0.5s ease forwards;
            display: flex;
            flex-direction: column;
        }
        .booking-card-header { position: relative; }
        .booking-card-img { width: 100%; height: 180px; object-fit: cover; }
        .service-name { position: absolute; bottom: 0; left: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); color: #fff; padding: 15px; width: 100%; font-size: 1.2rem; font-weight: 600; }

        .booking-card-body { padding: 20px; flex-grow: 1; }
        .detail-row { display: flex; align-items: flex-start; margin-bottom: 12px; font-size: 0.95rem; }
        .detail-row i { color: var(--mint-green); width: 25px; margin-top: 2px; }
        .status { padding: 5px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .status.pending { background-color: #f39c12; color: #000; }
        .status.approved { background-color: #2980b9; color: #fff; }
        .status.assigned { background-color: #3498db; color: #fff; }
        .status.completed { background-color: var(--mint-green); color: #000; }
        .status.cancelled { background-color: #e74c3c; color: #fff; }

        .booking-card-footer { background-color: #111; padding: 15px 20px; text-align: center; margin-top: auto; }
        .btn { padding: 8px 15px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-cancel { background-color: #c0392b; color: #fff; width: 100%; font-size: 14px; }
        .btn-review { background-color: var(--mint-green); color: var(--black); }

        .placeholder-text {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            font-style: italic;
            color: var(--metallic-silver);
        }
        .placeholder-text a { color: var(--mint-green); }

        .pilot-info {
            background-color: rgba(59, 255, 20, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.7); justify-content: center; align-items: center; }
        .modal-content { background-color: var(--dark-gray); margin: auto; padding: 20px; border: 1px solid var(--mint-green); width: 90%; max-width: 400px; border-radius: 10px; text-align: center; }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }

        .rating { display: flex; flex-direction: row-reverse; justify-content: center; padding: 15px 0; }
        .rating input { display: none; }
        .rating label { font-size: 35px; color: #444; cursor: pointer; transition: color 0.2s; padding: 0 5px;}
        .rating input:checked ~ label, .rating label:hover, .rating label:hover ~ label { color: var(--mint-green); }
        .modal-content textarea { width: calc(100% - 20px); background-color: #222; color: #fff; border: 1px solid #555; border-radius: 5px; padding: 10px; min-height: 80px; margin-top: 10px; }
        .modal-content button { margin-top: 15px; }


        @keyframes slideUpFadeIn { to { opacity: 1; transform: translateY(0); } }

        /* Modern Rating Popup Styles */
        .rating-overlay {
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

        .rating-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .rating-popup {
            background: linear-gradient(145deg, #2a3441, #3a4b5c);
            border-radius: 24px;
            padding: 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            position: relative;
            transform: scale(0.7) translateY(50px);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(62, 180, 137, 0.2);
        }

        .rating-overlay.show .rating-popup {
            transform: scale(1) translateY(0);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--mint-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: bounceIn 0.6s ease;
        }

        .success-icon i {
            font-size: 36px;
            color: #000;
        }

        .rating-popup h2 {
            color: var(--mint-green);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .rating-popup .subtitle {
            color: #B0B0B0;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .rating-popup .experience-text {
            color: #FFF;
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .rating-popup .rating-label {
            color: var(--mint-green);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .emoji-rating {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .emoji-rating .emoji {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
        }

        .emoji-rating .emoji:hover {
            transform: scale(1.2);
            background: rgba(62, 180, 137, 0.2);
            border-color: var(--mint-green);
        }

        .emoji-rating .emoji.selected {
            transform: scale(1.3);
            background: var(--mint-green);
            border-color: var(--mint-green);
            box-shadow: 0 0 20px rgba(62, 180, 137, 0.5);
        }

        .emoji-rating .emoji.selected::after {
            content: '';
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .emoji-rating .emoji.selected::before {
            content: '✓';
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            color: var(--mint-green);
            font-size: 12px;
            font-weight: bold;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 15px;
            color: #FFF;
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .review-textarea:focus {
            outline: none;
            border-color: var(--mint-green);
            background: rgba(255, 255, 255, 0.15);
        }

        .review-textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .popup-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .popup-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Outfit', sans-serif;
        }

        .popup-btn.skip {
            background: rgba(255, 255, 255, 0.1);
            color: #B0B0B0;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .popup-btn.skip:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #FFF;
        }

        .popup-btn.submit {
            background: var(--mint-green);
            color: #000;
            border: 2px solid var(--mint-green);
        }

        .popup-btn.submit:hover {
            background: transparent;
            color: var(--mint-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(62, 180, 137, 0.3);
        }

        .popup-btn.submit:disabled {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .rating-popup {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .emoji-rating .emoji {
                width: 50px;
                height: 50px;
                font-size: 28px;
            }
            
            .popup-buttons {
                flex-direction: column;
            }
            
            .popup-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" style="color: var(--mint-green); text-decoration: none; font-weight: 600; margin-bottom: 30px; display: inline-block;">&larr; Back to Dashboard</a>
        <?php if ($message): ?>
            <p style="color: <?php echo $message_type === 'success' ? '#2ecc71' : '#e74c3c'; ?>; background-color: rgba(<?php echo $message_type === 'success' ? '46, 204, 113, 0.2' : '231, 76, 60, 0.2'; ?>); padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <h2>Active Bookings</h2>
        <div class="bookings-grid">
            <?php if (empty($active_bookings)): ?>
                <p class="placeholder-text">You have no active bookings. <a href="dashboard.php">Book a new service</a>!</p>
            <?php else: foreach ($active_bookings as $index => $booking):
                $status_details = getStatusDetails($booking['order_status']);
            ?>
                <div class="booking-card" style="animation-delay: <?php echo $index * 100; ?>ms;">
                    <div class="booking-card-header">
                        <img class="booking-card-img" src="<?php echo (strpos($booking['service_type'], 'Videography') !== false) ? 'drone2.png' : 'drone1.png'; ?>" alt="Service Image">
                        <div class="service-name"><?php echo htmlspecialchars($booking['service_type']); ?></div>
                    </div>
                    <div class="booking-card-body">
                        <div class="detail-row"><i class="fas fa-calendar-alt"></i> <span><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></span></div>
                        <?php if ($booking['start_time'] && $booking['end_time']): ?>
                            <div class="detail-row"><i class="fas fa-clock"></i> <span><?php echo date('g:i A', strtotime($booking['start_time'])) . ' - ' . date('g:i A', strtotime($booking['end_time'])); ?></span></div>
                            <div class="detail-row"><i class="fas fa-hourglass-half"></i> <span><?php echo htmlspecialchars($booking['hours']); ?> Hour(s)</span></div>
                        <?php else: ?>
                            <div class="detail-row"><i class="fas fa-clock"></i> <span><?php echo htmlspecialchars($booking['hours']); ?> Hour(s)</span></div>
                        <?php endif; ?>
                        <div class="detail-row"><i class="fas fa-map-marker-alt"></i> <span><?php echo htmlspecialchars($booking['address']); ?></span></div>
                        <div class="detail-row"><i class="fas fa-wallet"></i> <span>₹<?php echo number_format($booking['total_price']); ?></span></div>
                        <div class="detail-row"><i class="fas fa-info-circle"></i>
                            <span class="status <?php echo $status_details['class']; ?>">
                                <?php echo htmlspecialchars($status_details['text']); ?>
                            </span>
                        </div>
                        <?php if ($booking['pilot_name']): ?>
                            <div class="detail-row pilot-info">
                                <i class="fas fa-helicopter"></i>
                                <div>
                                    <strong>Pilot Assigned:</strong> <?php echo htmlspecialchars($booking['pilot_name']); ?><br>
                                    <small>Phone: <?php echo htmlspecialchars($booking['pilot_phone']); ?></small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($status_details['text'] === 'Pending Admin Approval' || $status_details['text'] === 'Admin Approved, Waiting for Pilot'): ?>
                    <div class="booking-card-footer">
                        <form method="POST" action="my_bookings.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <button type="submit" name="cancel_booking" class="btn btn-cancel">Cancel Booking</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <h2 style="margin-top: 50px;">Past Bookings</h2>
        <div class="bookings-grid">
            <?php if (empty($past_bookings)): ?>
                <p class="placeholder-text">You have no completed bookings yet.</p>
            <?php else: foreach ($past_bookings as $index => $booking): ?>
                <div class="booking-card" style="animation-delay: <?php echo $index * 100; ?>ms;">
                    <div class="booking-card-header">
                        <img class="booking-card-img" src="<?php echo (strpos($booking['service_type'], 'Videography') !== false) ? 'drone2.png' : 'drone1.png'; ?>" alt="Service Image">
                        <div class="service-name"><?php echo htmlspecialchars($booking['service_type']); ?></div>
                    </div>
                    <div class="booking-card-body">
                        <div class="detail-row"><i class="fas fa-calendar-alt"></i> <span><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></span></div>
                        <?php if ($booking['start_time'] && $booking['end_time']): ?>
                            <div class="detail-row"><i class="fas fa-clock"></i> <span><?php echo date('g:i A', strtotime($booking['start_time'])) . ' - ' . date('g:i A', strtotime($booking['end_time'])); ?></span></div>
                            <div class="detail-row"><i class="fas fa-hourglass-half"></i> <span><?php echo htmlspecialchars($booking['hours']); ?> Hour(s)</span></div>
                        <?php else: ?>
                            <div class="detail-row"><i class="fas fa-clock"></i> <span><?php echo htmlspecialchars($booking['hours']); ?> Hour(s)</span></div>
                        <?php endif; ?>
                        <div class="detail-row"><i class="fas fa-check-circle"></i> <span class="status completed">Completed</span></div>
                        <div class="detail-row"><i class="fas fa-wallet"></i> <span>₹<?php echo number_format($booking['total_price']); ?></span></div>
                        <?php if($booking['rating']): ?>
                             <div class="detail-row"><i class="fas fa-star"></i> <span>You rated: <?php echo $booking['rating']; ?>/5</span></div>
                             <div class="detail-row"><i class="fas fa-comment"></i> <span>"<?php echo htmlspecialchars($booking['review']); ?>"</span></div>
                        <?php endif; ?>
                    </div>
                     <div class="booking-card-footer">
                        <?php if(!$booking['rating']): ?>
                            <button class="btn btn-review" onclick="openReviewModal('<?php echo $booking['id']; ?>', '<?php echo htmlspecialchars(addslashes($booking['service_type'])); ?>')">Rate & Review</button>
                        <?php else: ?>
                            <p style="font-style: italic;">Thank you for your feedback!</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <h2 style="margin-top: 50px;">Cancelled Bookings</h2>
        <div class="bookings-grid">
             <?php if (empty($cancelled_bookings)): ?>
                <p class="placeholder-text">You have no cancelled bookings.</p>
            <?php else: foreach ($cancelled_bookings as $index => $booking): ?>
                <div class="booking-card" style="animation-delay: <?php echo $index * 100; ?>ms;">
                    <div class="booking-card-header">
                        <img class="booking-card-img" src="<?php echo (strpos($booking['service_type'], 'Videography') !== false) ? 'drone2.png' : 'drone1.png'; ?>" alt="Service Image">
                        <div class="service-name"><?php echo htmlspecialchars($booking['service_type']); ?></div>
                    </div>
                    <div class="booking-card-body">
                        <div class="detail-row"><i class="fas fa-calendar-alt"></i> <span><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></span></div>
                        <?php if ($booking['start_time'] && $booking['end_time']): ?>
                            <div class="detail-row"><i class="fas fa-clock"></i> <span><?php echo date('g:i A', strtotime($booking['start_time'])) . ' - ' . date('g:i A', strtotime($booking['end_time'])); ?></span></div>
                            <div class="detail-row"><i class="fas fa-hourglass-half"></i> <span><?php echo htmlspecialchars($booking['hours']); ?> Hour(s)</span></div>
                        <?php else: ?>
                            <div class="detail-row"><i class="fas fa-clock"></i> <span><?php echo htmlspecialchars($booking['hours']); ?> Hour(s)</span></div>
                        <?php endif; ?>
                        <div class="detail-row"><i class="fas fa-times-circle"></i> <span class="status cancelled">Cancelled</span></div>
                        <div class="detail-row"><i class="fas fa-wallet"></i> <span>Amount: ₹<?php echo number_format($booking['total_price']); ?></span></div>
                        <div class="detail-row"><i class="fas fa-undo-alt"></i> <span>Refund: <?php echo isset($booking['refund_status']) ? htmlspecialchars($booking['refund_status']) : 'Pending'; ?></span></div>
                        <?php if(isset($booking['refund_utr']) && $booking['refund_utr']): ?>
                            <div class="detail-row"><i class="fas fa-receipt"></i> <span>UTR: <?php echo htmlspecialchars($booking['refund_utr']); ?></span></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Old modal - keeping for backward compatibility but hidden -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeReviewModal()">&times;</span>
            <h2>Rate Your Service</h2>
            <h3 id="review-service-name" style="color: var(--metallic-silver); font-weight: normal; margin-bottom: 15px;"></h3>
            <form method="POST">
                <input type="hidden" name="order_id" id="review-order-id">
                <div class="rating">
                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                </div>
                <textarea name="review" placeholder="Write an optional review..."></textarea>
                <button type="submit" name="submit_review" class="btn btn-review">Submit</button>
            </form>
        </div>
    </div>

<script>
    function openReviewModal(orderId, serviceName) {
        // Set the order details for the manual rating popup
        document.getElementById('manual-order-id').value = orderId;
        document.getElementById('manual-service-name').innerText = serviceName;
        
        // Reset the rating
        document.getElementById('manualSelectedRating').value = '';
        document.getElementById('manual-rating-label').innerText = 'Select Rating';
        document.getElementById('manualSubmitBtn').disabled = true;
        
        // Clear previous selections
        document.querySelectorAll('#manualEmojiRating .emoji').forEach(emoji => {
            emoji.classList.remove('selected');
        });
        
        // Show the manual rating popup
        const manualRatingOverlay = document.getElementById('manualRatingOverlay');
        if (manualRatingOverlay) {
            manualRatingOverlay.style.display = 'flex';
            manualRatingOverlay.classList.add('show');
        }
    }
    
    function closeManualRating() {
        const manualRatingOverlay = document.getElementById('manualRatingOverlay');
        if (manualRatingOverlay) {
            manualRatingOverlay.classList.remove('show');
            setTimeout(() => {
                manualRatingOverlay.style.display = 'none';
            }, 300);
        }
    }
    
    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == document.getElementById('reviewModal')) {
            closeReviewModal();
        }
        if (event.target == document.getElementById('manualRatingOverlay')) {
            closeManualRating();
        }
    }

    // Modern rating popup functionality
    document.addEventListener('DOMContentLoaded', function() {
        const ratingOverlay = document.getElementById('ratingOverlay');
        const emojis = document.querySelectorAll('.emoji');
        const selectedRatingInput = document.getElementById('selectedRating');
        const submitBtn = document.getElementById('submitReviewBtn');
        const ratingLabel = document.querySelector('.rating-label');
        
        const ratingLabels = {
            1: 'Very Poor',
            2: 'Poor', 
            3: 'Average',
            4: 'Good',
            5: 'Excellent'
        };

        // Show popup if there's a pending review
        if (ratingOverlay) {
            setTimeout(() => {
                ratingOverlay.classList.add('show');
            }, 1000);
        }

        // Handle emoji selection
        emojis.forEach(emoji => {
            emoji.addEventListener('click', function() {
                // Remove selected class from all emojis
                emojis.forEach(e => e.classList.remove('selected'));
                
                // Add selected class to clicked emoji
                this.classList.add('selected');
                
                // Update rating value and enable submit button
                const rating = this.dataset.rating;
                selectedRatingInput.value = rating;
                submitBtn.disabled = false;
                
                // Update rating label
                ratingLabel.textContent = ratingLabels[rating];
                
                // Add bounce animation
                this.style.animation = 'bounceIn 0.6s ease';
                setTimeout(() => {
                    this.style.animation = '';
                }, 600);
            });
        });

        // Handle form submission
        if (document.getElementById('modernRatingForm')) {
            document.getElementById('modernRatingForm').addEventListener('submit', function(e) {
                if (!selectedRatingInput.value) {
                    e.preventDefault();
                    alert('Please select a rating before submitting!');
                }
            });
        }
    });

    function skipReview() {
        document.getElementById('ratingOverlay').classList.remove('show');
        // Mark as reviewed to prevent showing again
        window.location.href = 'my_bookings.php?review_submitted=1';
    }

    // Close popup when clicking outside
    document.addEventListener('click', function(e) {
        const ratingOverlay = document.getElementById('ratingOverlay');
        const ratingPopup = document.querySelector('.rating-popup');
        
        if (ratingOverlay && e.target === ratingOverlay) {
            skipReview();
        }
    });

    // Manual rating popup functionality
    document.addEventListener('DOMContentLoaded', function() {
        const manualEmojis = document.querySelectorAll('#manualEmojiRating .emoji');
        const manualSelectedRatingInput = document.getElementById('manualSelectedRating');
        const manualSubmitBtn = document.getElementById('manualSubmitBtn');
        const manualRatingLabel = document.getElementById('manual-rating-label');
        
        const manualRatingLabels = {
            1: 'Very Poor',
            2: 'Poor', 
            3: 'Average',
            4: 'Good',
            5: 'Excellent'
        };

        manualEmojis.forEach(emoji => {
            emoji.addEventListener('click', function() {
                // Remove selected class from all emojis
                manualEmojis.forEach(e => e.classList.remove('selected'));
                
                // Add selected class to clicked emoji
                this.classList.add('selected');
                
                // Update rating value and enable submit button
                const rating = this.dataset.rating;
                manualSelectedRatingInput.value = rating;
                manualSubmitBtn.disabled = false;
                
                // Update rating label
                manualRatingLabel.textContent = manualRatingLabels[rating];
                
                // Add bounce animation
                this.style.animation = 'bounceIn 0.6s ease';
                setTimeout(() => {
                    this.style.animation = '';
                }, 600);
            });
        });

        // Handle manual form submission
        if (document.getElementById('manualRatingForm')) {
            document.getElementById('manualRatingForm').addEventListener('submit', function(e) {
                if (!manualSelectedRatingInput.value) {
                    e.preventDefault();
                    alert('Please select a rating before submitting!');
                }
            });
        }
    });
</script>

<!-- Modern Rating Popup for Auto-display -->
<?php if ($pending_review && !isset($_GET['review_submitted'])): ?>
<div class="rating-overlay" id="ratingOverlay">
    <div class="rating-popup">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h2>Congratulations!</h2>
        <p class="subtitle">Your order was successful!</p>
        <p class="experience-text">How was your experience?</p>
        <p class="rating-label">Excellent</p>
        
        <form method="POST" id="modernRatingForm">
            <input type="hidden" name="order_id" value="<?php echo $pending_review['id']; ?>">
            <input type="hidden" name="rating" id="selectedRating" value="">
            
            <div class="emoji-rating">
                <div class="emoji" data-rating="1" title="Very Poor">😞</div>
                <div class="emoji" data-rating="2" title="Poor">😕</div>
                <div class="emoji" data-rating="3" title="Average">😐</div>
                <div class="emoji" data-rating="4" title="Good">😊</div>
                <div class="emoji" data-rating="5" title="Excellent">😍</div>
            </div>
            
            <textarea name="review" class="review-textarea" placeholder="Tell us about your experience... (optional)"></textarea>
            
            <div class="popup-buttons">
                <button type="button" class="popup-btn skip" onclick="skipReview()">Skip</button>
                <button type="submit" name="submit_review" class="popup-btn submit" id="submitReviewBtn" disabled>Submit Review</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Universal Rating Popup for Manual Reviews -->
<div class="rating-overlay" id="manualRatingOverlay" style="display: none;">
    <div class="rating-popup">
        <div class="close-btn" onclick="closeManualRating()" style="position: absolute; top: 15px; right: 20px; cursor: pointer; font-size: 24px; color: var(--metallic-silver);">&times;</div>
        <div class="success-icon">
            <i class="fas fa-star"></i>
        </div>
        <h2>Rate Your Service</h2>
        <p class="subtitle" id="manual-service-name">Service Name</p>
        <p class="experience-text">How was your experience?</p>
        <p class="rating-label" id="manual-rating-label">Excellent</p>
        
        <form method="POST" id="manualRatingForm">
            <input type="hidden" name="order_id" id="manual-order-id" value="">
            <input type="hidden" name="rating" id="manualSelectedRating" value="">
            
            <div class="emoji-rating" id="manualEmojiRating">
                <div class="emoji" data-rating="1" title="Very Poor">😞</div>
                <div class="emoji" data-rating="2" title="Poor">😕</div>
                <div class="emoji" data-rating="3" title="Average">😐</div>
                <div class="emoji" data-rating="4" title="Good">😊</div>
                <div class="emoji" data-rating="5" title="Excellent">😍</div>
            </div>
            
            <textarea 
                name="review" 
                placeholder="Write an optional review..."
                style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-color); color: var(--text-color); font-family: 'Outfit', sans-serif; resize: vertical; min-height: 80px; margin: 20px 0;"
            ></textarea>
            
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="popup-btn skip" onclick="closeManualRating()">Cancel</button>
                <button type="submit" name="submit_review" class="popup-btn" id="manualSubmitBtn" disabled>Submit Review</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>