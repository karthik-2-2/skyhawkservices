<?php
include('../config/db.php');
include('../config/security_questions.php');

$step = isset($_GET['step']) ? $_GET['step'] : 1;
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Step 1: Verify phone number
        $phone = trim($_POST['phone']);
        
        if (userExists($conn, $phone)) {
            $security_question = getUserSecurityQuestion($conn, $phone);
            if ($security_question) {
                header("Location: forgot_password.php?step=2&phone=" . urlencode($phone) . "&qid=" . $security_question['question_id']);
                exit();
            } else {
                $error_message = "No security questions found for this account.";
            }
        } else {
            $error_message = "Phone number not found!";
        }
    } elseif ($step == 2) {
        // Step 2: Verify security answer
        $phone = $_GET['phone'];
        $question_id = $_GET['qid'];
        $answer = trim($_POST['answer']);
        
        if (verifyUserSecurityAnswer($conn, $phone, $question_id, $answer)) {
            header("Location: forgot_password.php?step=3&phone=" . urlencode($phone));
            exit();
        } else {
            $error_message = "Incorrect answer. Please try again.";
        }
    } elseif ($step == 3) {
        // Step 3: Reset password
        $phone = $_GET['phone'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $error_message = "Passwords do not match!";
        } elseif (strlen($new_password) < 6) {
            $error_message = "Password must be at least 6 characters long!";
        } else {
            if (updateUserPassword($conn, $phone, $new_password)) {
                $success_message = "Password reset successfully! You can now login with your new password.";
            } else {
                $error_message = "Failed to reset password. Please try again.";
            }
        }
    }
}

// Get security question for step 2
if ($step == 2 && isset($_GET['qid'])) {
    $question_query = "SELECT question FROM security_questions WHERE id = ?";
    $stmt = $conn->prepare($question_query);
    $stmt->execute([$_GET['qid']]);
    $current_question = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - User</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            background-color: #000;
            color: #c0c0c0;
            min-height: 100vh;
            overflow: auto;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            min-width: 320px;
        }

        .forgot-password-box {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 20px #39ff14;
            background-color: #000;
            padding: 40px;
            border: 1px solid #39ff14;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #39ff14;
        }

        .step-indicator {
            text-align: center;
            margin-bottom: 20px;
            color: #c0c0c0;
            font-size: 14px;
        }

        .input-box {
            position: relative;
            margin-bottom: 25px;
        }

        .input-box input {
            width: 100%;
            padding: 12px 10px;
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #c0c0c0;
            color: #c0c0c0;
            font-size: 16px;
            outline: none;
        }

        .input-box label {
            display: block;
            margin-bottom: 8px;
            color: #c0c0c0;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: linear-gradient(135deg, #39ff14, #c0c0c0);
            color: black;
            font-weight: bold;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 15px;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px #39ff14;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #39ff14;
            text-decoration: none;
            font-size: 14px;
        }

        .success-message {
            background: rgba(57, 255, 20, 0.1);
            border: 1px solid #39ff14;
            color: #39ff14;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff0000;
            color: #ff6666;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .forgot-password-box {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-password-box">
            <h2>Reset Password</h2>
            
            <div class="step-indicator">
                Step <?php echo $step; ?> of 3
            </div>

            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
                <div class="back-link">
                    <a href="index.php">← Back to Login</a>
                </div>
            <?php else: ?>

                <?php if ($step == 1): ?>
                    <!-- Step 1: Enter Phone Number -->
                    <form method="POST">
                        <div class="input-box">
                            <label>Enter your phone number:</label>
                            <input type="tel" name="phone" placeholder="Phone Number" required />
                        </div>
                        <button type="submit">Continue</button>
                    </form>

                <?php elseif ($step == 2): ?>
                    <!-- Step 2: Answer Security Question -->
                    <form method="POST">
                        <div class="input-box">
                            <label>Security Question:</label>
                            <p style="color: #39ff14; font-weight: bold; margin-bottom: 15px; padding: 10px; background: rgba(57, 255, 20, 0.1); border-radius: 5px;">
                                <?php echo htmlspecialchars($current_question); ?>
                            </p>
                            <input type="text" name="answer" placeholder="Your answer" required />
                        </div>
                        <button type="submit">Verify Answer</button>
                    </form>

                <?php elseif ($step == 3): ?>
                    <!-- Step 3: Set New Password -->
                    <form method="POST">
                        <div class="input-box">
                            <label>New Password:</label>
                            <input type="password" name="new_password" placeholder="Enter new password" required />
                        </div>
                        <div class="input-box">
                            <label>Confirm Password:</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password" required />
                        </div>
                        <button type="submit">Reset Password</button>
                    </form>
                <?php endif; ?>

                <div class="back-link">
                    <a href="index.php">← Back to Login</a>
                </div>

            <?php endif; ?>
        </div>
    </div>
</body>
</html>
