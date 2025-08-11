<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_phone'])) {
    die("You must be logged in to make a booking.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Get All Form Data ---
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $service_type = $_POST['service_type'];
    $booking_date = $_POST['booking_date'];
    $hours = intval($_POST['hours']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_price = intval($_POST['total_price']);
    $additional_msg = $_POST['additional_msg'];
    $transaction_id = $_POST['transaction_id'];

    // --- Handle File Upload ---
    $screenshot_path = null;
    if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
        $upload_dir = 'payment_screenshots/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['payment_screenshot']['name'], PATHINFO_EXTENSION);
        $safe_phone = preg_replace('/[^0-9]/', '', $phone);
        $new_filename = 'payment_' . time() . '_' . $safe_phone . '.' . $file_ext;
        $screenshot_path = $upload_dir . $new_filename;

        if (!move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $screenshot_path)) {
            die("Error: Could not upload screenshot. Please try again.");
        }
    } else {
        die("Error: Screenshot upload is required.");
    }

    // Insert into orders table
    $stmt = $conn->prepare(
        "INSERT INTO orders (name, email, phone, address, service_type, booking_date, hours, start_time, end_time, total_price, additional_msg, transaction_id, payment_screenshot, order_status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending Admin Approval')"
    );
    
    if ($stmt->execute([
        $name, $email, $phone, $address, $service_type, $booking_date, 
        $hours, $start_time, $end_time, $total_price, $additional_msg, $transaction_id, $screenshot_path
    ])) {
        echo "<script>
                sessionStorage.removeItem('bookingDetails');
                alert('Booking Submitted Successfully! Your request is now pending admin approval.');
                window.location.href = 'my_bookings.php';
              </script>";
    } else {
        echo "Error submitting booking.";
    }
}
?>