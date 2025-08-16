<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include('../config/db.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$phone]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_phone'] = $admin['phone'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = "Invalid phone or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <style>
        /* Your existing CSS from this file... */
	* {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    html, body {
      background-color: #000;
      color: #c0c0c0; /* Changed from #fff to metallic silver */
      min-height: 100vh; /* Changed from height: 100% */
      overflow: auto;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center; /* Keep center for admin */
      padding: 20px;
    }

    .container {
      width: 100%;
      max-width: 700px; /* Reduced from 850px to bring panels closer */
      min-width: 320px;
    }

    .login-box {
      display: flex;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 20px #39ff14;
      flex-wrap: nowrap;
      background-color: #000;
      gap: 0; /* Remove any gap between panels */
    }

    .left-panel,
    .right-panel {
      padding: 40px 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .left-panel {
      margin-left:25px;
      width: 40%;
      background-color: #000;
      z-index: 1;
    }

    .left-panel h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      color: #39ff14;
    }

    .input-box {
      position: relative;
      margin-bottom: 25px;
    }

    .input-box input {
      width: 100%;
      padding: 12px 40px 12px 10px;
      background-color: transparent;
      border: none;
      border-bottom: 1px solid #c0c0c0; /* Changed from #ccc to metallic silver */
      color: #c0c0c0; /* Changed from #fff to metallic silver */
      font-size: 16px;
      outline: none;
    }

    .input-box i {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #c0c0c0; /* Changed from #ccc to metallic silver */
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
    }

    button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px #39ff14;
    }

    .signup-text {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
      white-space: nowrap;
    }

    .signup-text a {
      color: #39ff14;
      text-decoration: none;
      margin-left: 4px;
    }

    .right-panel {
      width: 60%;
      background: linear-gradient(-45deg, #39ff14, #c0c0c0, #000, #39ff14);
      background-size: 400% 400%;
      animation: gradientBG 10s ease infinite;
      color: white;
      text-align: left;
      display: flex;
      align-items: center;
      justify-content: flex-start; /* Changed to align content to left side */
      padding: 20px 40px; /* Reduced padding to bring content closer */
      position: relative;
    }

    /* Add clip-path for larger screens only */
    @media (min-width: 769px) {
      .right-panel {
        clip-path: polygon(0 0, 100% 0, 100% 100%, 60% 100%);
        justify-content: flex-start; /* Keep text on left side of the panel */
        padding-left: 30px; /* Ensure proper spacing from left edge */
      }
    }

    .right-panel h1 {
      font-size: 28px; /* Slightly reduced */
      line-height: 1.1;
      text-shadow: 0 0 10px rgba(61, 201, 36, 0.3);
      margin: 0; /* Remove default margin */
      text-align: left;
    }

    .right-panel h1 span {
      font-weight: bold;
      display: block;
    }

    .right-panel p {
      margin-top: 20px;
      font-size: 14px;
      opacity: 0.9;
    }

    @keyframes gradientBG {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    @media (max-width: 768px) {
      .container {
        width: 100%; /* Ensure full width on mobile */
        max-width: none; /* Remove max-width constraint */
        margin: 0; /* Remove any margins */
      }

      .login-box {
        flex-direction: column; /* Stack vertically on mobile */
        overflow-x: visible;
        width: 100%; /* Ensure full width */
        margin: 0; /* Remove any margins */
      }

      .left-panel, .right-panel {
        padding: 20px;
        width: 100%;
      }

      /* Welcome panel on top, remove clip-path for mobile */
      .right-panel {
        order: -1; /* Move to top */
        clip-path: none; /* Remove diagonal cut */
        padding: 30px 20px;
        text-align: center;
        justify-content: center;
      }

      .signup-text {
        font-size: 13px;
        white-space: normal;
      }

      .right-panel h1 {
        font-size: 24px;
      }

      .right-panel p {
        font-size: 14px;
      }
    }

    @media (max-width: 480px) {
      .login-box {
        flex-direction: column;
        margin: 10px;
      }

      .left-panel, .right-panel {
        padding: 15px;
      }

      .left-panel h2 {
        font-size: 20px;
      }

      .right-panel h1 {
        font-size: 20px;
      }

      .right-panel p {
        font-size: 12px;
      }

      .signup-text {
        font-size: 12px;
        white-space: nowrap;
      }
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="left-panel">
                <h2>Admin Login</h2>
                <?php if (!empty($error_message)): ?>
                    <p style="color: red; text-align: center; margin-bottom: 15px;"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="input-box">
                        <input type="tel" name="phone" placeholder="Phone Number" required />
                    </div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Password" required />
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
            <div class="right-panel">
                <div>
                    <h1>WELCOME<br /><span>BACK!</span></h1>
                </div>
            </div>
        </div>
    </div>
</body>
</html>