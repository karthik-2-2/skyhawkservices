<?php
// --- Use this file to create a new admin account, then DELETE IT for security ---

// Include the database connection
require_once '../config/db.php';

$message = '';
$message_type = 'info'; // Can be 'info', 'success', or 'error'

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (!empty($phone) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if admin exists
        $stmt_check = $conn->prepare("SELECT id FROM admin WHERE phone = ?");
        $stmt_check->execute([$phone]);

        if ($stmt_check->rowCount() > 0) {
            $message = "Error: An admin with this phone number already exists.";
            $message_type = 'error';
        } else {
            // Insert new admin
            $stmt_insert = $conn->prepare("INSERT INTO admin (phone, password) VALUES (?, ?)");
            
            if ($stmt_insert->execute([$phone, $hashed_password])) {
                $message = "Admin account created successfully!<br>Phone: " . htmlspecialchars($phone) . "<br>Password: " . htmlspecialchars($password);
                $message_type = 'success';
            } else {
                $message = "Error: Could not create the admin account.";
                $message_type = 'error';
            }
        }
    } else {
        $message = "Please fill in both the phone and password fields.";
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .container { background-color: var(--dark-gray); padding: 40px; border-radius: 15px; border-top: 5px solid var(--mint-green); width: 100%; max-width: 500px; text-align: center; }
        h1 { color: var(--mint-green); margin-bottom: 10px; }
        p { margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input { width: 100%; padding: 12px; background-color: #333; border: 1px solid var(--metallic-silver); color: #fff; border-radius: 8px; box-sizing: border-box; }
        button { background-color: var(--mint-green); color: var(--black); padding: 15px; border: none; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; font-size: 16px; }
        .message { padding: 15px; margin-top: 20px; border-radius: 8px; font-weight: bold; }
        .message.info { background-color: rgba(52, 152, 219, 0.2); color: #3498db; }
        .message.success { background-color: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .message.error { background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        .warning { color: #f39c12; font-weight: bold; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Account Creator</h1>
        <p>Use this form to add a new admin to the database.</p>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="create_admin.php">
            <div class="form-group">
                <label for="phone">Admin Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Create Admin</button>
        </form>

        <p class="warning">IMPORTANT: For security, please delete this file (`create_admin.php`) from your server after you have created your account.</p>
    </div>
</body>
</html>