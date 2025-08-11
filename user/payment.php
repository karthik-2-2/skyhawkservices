<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_phone'])) {
    header('Location: index.php');
    exit();
}

// Fetch the logged-in user's details to pre-fill the form
$phone_session = $_SESSION['user_phone'];
$stmt_user = $conn->prepare("SELECT name, email, address FROM \"user\" WHERE phone = ?");
$stmt_user->execute([$phone_session]);
$user_result = $stmt_user->fetch();

// If no user is found for the session phone number, destroy the session and redirect to login.
if (!$user_result) {
    session_destroy();
    header('Location: index.php');
    exit();
}

$user_name = $user_result['name'];
$user_email = $user_result['email'];
$user_address = $user_result['address'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --black: #000000;
            --mint-green: #3EB489;
            --metallic-silver: #B0B0B0;
            --dark-gray: #1a1a1a;
            --light-gray: #333;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--black);
            color: var(--metallic-silver);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .payment-container {
            background-color: var(--dark-gray);
            padding: 40px;
            border-radius: 15px;
            border-top: 5px solid var(--mint-green);
            width: 100%;
            max-width: 1000px;
        }
        .payment-grid {
            display: flex;
            flex-wrap: wrap; 
            gap: 40px;
        }
        .left-column {
            flex: 1;
            min-width: 300px;
        }
        .right-column {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column; 
        }
        h2, h3 {
            color: var(--mint-green);
            margin-bottom: 20px;
        }
        
        .highlights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .highlight-card {
            background-color: var(--black);
            padding: 20px;
            border-radius: 10px;
            border-left: 3px solid var(--mint-green);
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFadeIn 0.5s ease forwards;
        }
        .highlight-card .icon { font-size: 24px; color: var(--mint-green); margin-bottom: 10px; }
        .highlight-card .title { font-size: 14px; color: var(--metallic-silver); margin-bottom: 5px; }
        .highlight-card .value { font-size: 18px; font-weight: 600; color: #fff; }
        #total-price-card .value { font-size: 24px; color: var(--mint-green); }

        .highlight-card:nth-child(1) { animation-delay: 0.2s; }
        .highlight-card:nth-child(2) { animation-delay: 0.3s; }
        .highlight-card:nth-child(3) { animation-delay: 0.4s; }
        .highlight-card:nth-child(4) { animation-delay: 0.5s; }

        @keyframes slideUpFadeIn {
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            background-color: var(--light-gray);
            border: 1px solid var(--metallic-silver);
            color: #fff;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--mint-green);
            box-shadow: 0 0 10px rgba(59, 255, 20, 0.3);
        }
        .form-group input[disabled] {
            background-color: #222;
            cursor: not-allowed;
            color: var(--metallic-silver);
        }
        .qr-code { 
            text-align: center; 
            background-color: var(--black); 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px;
        }
        .qr-code img { 
            max-width: 250px; 
            width: 100%;
            border: 4px solid var(--mint-green); 
            border-radius: 10px; 
            margin: 15px auto 0 auto;
        }
        
        .submit-btn {
            background-color: var(--mint-green); color: var(--black);
            padding: 15px; border: none; border-radius: 8px;
            width: 100%; font-weight: bold; cursor: pointer; font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
            margin-top: auto; 
        }
        .submit-btn:hover { background-color: #2c7a68; transform: scale(1.02); }
        
        @media (max-width: 800px) {
            .payment-grid {
                flex-direction: column;
            }
            .payment-container {
                padding: 20px;
            }
            .highlights-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="payment-container">
    <form action="submit_booking.php" method="POST" enctype="multipart/form-data" id="main-form">
        <input type="hidden" name="service_type" id="form-service">
        <input type="hidden" name="booking_date" id="form-date">
        <input type="hidden" name="hours" id="form-hours">
        <input type="hidden" name="start_time" id="form-start-time">
        <input type="hidden" name="end_time" id="form-end-time">
        <input type="hidden" name="total_price" id="form-price">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($user_name); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone_session); ?>">

        <h2>Confirm Booking & Payment</h2>
        <div class="payment-grid">
            <div class="left-column">
                <h3>Booking Highlights <a href="dashboard.php" style="font-size: 14px; color: #3EB489; text-decoration: none;">(Edit)</a></h3>
                <div class="highlights-grid">
                    <div class="highlight-card">
                        <div class="icon"><i class="fas fa-camera"></i></div>
                        <div class="title">Service</div>
                        <div class="value" id="summary-service">--</div>
                    </div>
                    <div class="highlight-card">
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="title">Date</div>
                        <div class="value" id="summary-date">--</div>
                    </div>
                    <div class="highlight-card">
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <div class="title">Time Schedule</div>
                        <div class="value" id="summary-time">--</div>
                    </div>
                    <div class="highlight-card">
                        <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                        <div class="title">Duration</div>
                        <div class="value" id="summary-duration">--</div>
                    </div>
                    <div class="highlight-card" id="total-price-card">
                        <div class="icon"><i class="fas fa-wallet"></i></div>
                        <div class="title">Total Amount</div>
                        <div class="value">₹<span id="summary-price">--</span></div>
                    </div>
                </div>
                
                <h3>Contact Information</h3>
                <div class="form-group">
                    <label for="phone">Contact Phone (Registered)</label>
                    <input type="tel" id="phone" value="<?php echo htmlspecialchars($phone_session); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="address">Service Address</label>
                    <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($user_address); ?></textarea>
                </div>
                 <div class="form-group">
                    <label for="additional_msg">Additional Message (Optional)</label>
                    <textarea id="additional_msg" name="additional_msg" rows="2"></textarea>
                </div>
            </div>

            <div class="right-column">
                <h3>Payment Details</h3>
                <div class="qr-code">
                    <p>1. Scan to Pay using any UPI App</p>
                    <img src="qr_code1.png" alt="Payment QR Code">
                </div>
                
                <div class="form-group">
                    <label for="transaction_id">2. Enter UPI Transaction ID</label>
                    <input type="text" id="transaction_id" name="transaction_id" required>
                </div>
                <div class="form-group">
                    <label for="payment_screenshot">3. Upload Payment Screenshot</label>
                    <input type="file" id="payment_screenshot" name="payment_screenshot" accept="image/*" required>
                </div>
                <button type="submit" class="submit-btn">Submit for Approval</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookingDetails = JSON.parse(sessionStorage.getItem('bookingDetails'));

        if (!bookingDetails) {
            alert('Booking details not found. Please start over.');
            window.location.href = 'dashboard.php';
            return;
        }

        document.getElementById('summary-service').innerText = bookingDetails.service_type;
        document.getElementById('summary-date').innerText = bookingDetails.booking_date;
        
        // Format time display
        const startTime = bookingDetails.start_time;
        const endTime = bookingDetails.end_time;
        document.getElementById('summary-time').innerText = `${startTime} - ${endTime}`;
        document.getElementById('summary-duration').innerText = `${bookingDetails.hours} Hour(s)`;
        document.getElementById('summary-price').innerText = bookingDetails.total_price.toLocaleString('en-IN');

        document.getElementById('form-service').value = bookingDetails.service_type;
        document.getElementById('form-date').value = bookingDetails.booking_date;
        document.getElementById('form-hours').value = bookingDetails.hours;
        document.getElementById('form-start-time').value = bookingDetails.start_time;
        document.getElementById('form-end-time').value = bookingDetails.end_time;
        document.getElementById('form-price').value = bookingDetails.total_price;
    });
</script>

</body>
</html>