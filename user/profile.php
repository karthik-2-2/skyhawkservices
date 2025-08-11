<?php
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
$message_type = 'success'; // can be 'success' or 'error'

// Handle form submission for updating details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE \"user\" SET name = ?, email = ?, address = ? WHERE phone = ?");
    if ($stmt->execute([$name, $email, $address, $phone_session])) {
        $message = "Profile updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating profile. Please try again.";
        $message_type = "error";
    }
}

// Fetch current user details
$stmt = $conn->prepare("SELECT name, email, address FROM \"user\" WHERE phone = ?");
$stmt->execute([$phone_session]);
$user = $stmt->fetch();

// Create a simple initial for the avatar
$initial = !empty($user['name']) ? strtoupper(substr($user['name'], 0, 1)) : '?';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root {
            --black: #000;
            --mint-green: #3EB489;
            --metallic-silver: #B0B0B0;
            --dark-gray: #1a1a1a;
            --light-gray: #333;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--black);
            color: var(--metallic-silver);
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('logo.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
            opacity: 0.05;
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            background-color: rgba(26, 26, 26, 0.7);
            border-radius: 15px;
            border-top: 5px solid var(--mint-green);
            overflow: hidden;
            opacity: 0;
            transform: perspective(1000px) scale(0.95);
            animation: fadeInScaleUp 0.6s ease forwards;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        @keyframes fadeInScaleUp {
            to {
                opacity: 1;
                transform: perspective(1000px) scale(1);
            }
        }

        .profile-grid {
            display: flex;
        }

        .avatar-section {
            width: 300px;
            background-color: rgba(0, 0, 0, 0.3);
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--mint-green);
            color: var(--black);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 60px;
            font-weight: 700;
            margin-bottom: 20px;
            border: 4px solid var(--dark-gray);
            box-shadow: 0 0 20px rgba(59, 255, 20, 0.2);
        }

        .avatar-section h3 {
            color: #fff;
            margin-bottom: 5px;
        }
        
        .form-section {
            padding: 40px;
            flex-grow: 1;
        }

        h2 {
            color: var(--mint-green);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            opacity: 0;
            transform: translateX(20px);
            animation: slideInFade 0.5s ease forwards;
        }
        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.3s; }
        .form-group:nth-child(3) { animation-delay: 0.4s; }
        .form-group:nth-child(4) { animation-delay: 0.5s; }

        @keyframes slideInFade {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        label {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-weight: 600;
        }
        label i {
            margin-right: 10px;
            color: var(--mint-green);
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            background-color: var(--light-gray);
            border: 1px solid var(--metallic-silver);
            color: #fff;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--mint-green);
            box-shadow: 0 0 10px rgba(59, 255, 20, 0.3);
        }

        input[disabled] {
            background-color: #222;
            cursor: not-allowed;
        }

        button {
            background-color: var(--mint-green);
            color: var(--black);
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s, transform 0.2s;
        }
        button:hover {
            background-color: #2c7a68;
            transform: scale(1.02);
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }
        .message.success {
            background-color: rgba(59, 255, 20, 0.1);
            color: var(--mint-green);
        }
        .message.error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .back-link {
            color: var(--mint-green); text-decoration: none; display: inline-block; margin-bottom: 20px;
        }

        @media(max-width: 768px) {
            .profile-grid { flex-direction: column; }
            .avatar-section { width: 100%; border-right: none; border-bottom: 1px solid var(--light-gray); }
            body { padding: 0; }
            .container { border-radius: 0; }
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="profile-grid">
            <div class="avatar-section">
                <div class="avatar-circle">
                    <?php echo $initial; ?>
                </div>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><?php echo htmlspecialchars($phone_session); ?></p>
            </div>
            <div class="form-section">
                <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
                <h2>My Profile</h2>
                <?php if ($message): ?>
                    <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i>Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i>Phone (Cannot be changed)</label>
                        <input type="text" value="<?php echo htmlspecialchars($phone_session); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i>Address</label>
                        <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_profile">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>