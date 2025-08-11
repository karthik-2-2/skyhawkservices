<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../config/db.php");

$balance = 0;
$user = null;
$wallet = [];
$phone = '';
$balanceChecked = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone'])) {
    $phone = trim($_POST['phone']);

    // 1. Check if user exists
    $stmt = $conn->prepare("SELECT name FROM \"user\" WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $balanceChecked = true;

        // 2. Sum total balance from wallet table
        $stmt = $conn->prepare("SELECT SUM(amount) as total_balance FROM wallet WHERE phone = ?");
        $stmt->execute([$phone]);
        $balanceRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $balance = $balanceRow['total_balance'] ?? 0;

        // 3. Get wallet transaction history
        $stmt = $conn->prepare("
            SELECT amount, utr, additional_msg, created_at 
            FROM wallet 
            WHERE phone = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$phone]);
        $wallet = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
// No need to close PDO connection
?>

<!DOCTYPE html>
<html>
<head>
  <title>Check Wallet Balance</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --mint-green: #3EB489;
      --metallic-silver: #B0B0B0;
      --dark-gray: #1a1a1a;
      --darker-gray: #222;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Outfit', Arial, sans-serif;
      background: var(--dark-gray);
      color: var(--metallic-silver);
      padding: 20px;
    }

    .container {
      max-width: 700px;
      margin: auto;
      background: var(--darker-gray);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
      border: 1px solid var(--mint-green);
      animation: fadeInDown 1s ease forwards;
    }

    h2, h3 {
      color: var(--mint-green);
      margin-bottom: 20px;
      animation: fadeInDown 1s ease forwards;
    }

    form {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: var(--metallic-silver);
      animation: fadeInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    input, button {
      padding: 10px;
      width: 100%;
      margin-top: 10px;
      border: 1px solid var(--metallic-silver);
      border-radius: 6px;
      font-size: 1rem;
      background: #333;
      color: var(--metallic-silver);
      animation: fadeInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    input:focus {
      outline: none;
      border-color: var(--mint-green);
      box-shadow: 0 0 5px rgba(62, 180, 137, 0.5);
    }

    button {
      background: var(--mint-green);
      color: #000;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.3s ease;
      animation: pulse 1.5s ease-in-out infinite;
    }

    button:hover {
      background: #2c7a68;
      transform: scale(1.05);
    }

    .result {
      margin-top: 20px;
      background: #e0fff0;
      padding: 15px;
      border-radius: 8px;
      color: #000;
      animation: bounceIn 1s ease forwards;
    }

    .error {
      background: #ffe0e0;
      color: #000;
      animation: bounceIn 1s ease forwards;
    }

    table {
      margin-top: 20px;
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border: 1px solid var(--metallic-silver);
      text-align: left;
    }

    th {
      background: var(--mint-green);
      color: #000;
      font-weight: 600;
    }

    td {
      background: #2a2a2a;
    }

    tr {
      animation: fadeInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .back-button {
      display: inline-block;
      margin-top: 25px;
      padding: 10px 20px;
      background: var(--mint-green);
      color: #000;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.3s ease;
      animation: pulse 1.5s ease-in-out infinite;
    }

    .back-button:hover {
      background: #2c7a68;
      transform: scale(1.05);
    }

    /* Animations */
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(50px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
      0% { opacity: 0; transform: translateY(-50px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounceIn {
      0% { opacity: 0; transform: scale(0.3); }
      50% { opacity: 1; transform: scale(1.2); }
      100% { transform: scale(1); }
    }

    @keyframes pulse {
      0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(62, 180, 137, 0.7); }
      50% { transform: scale(1.05); box-shadow: 0 0 10px 5px rgba(62, 180, 137, 0.3); }
      100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(62, 180, 137, 0); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }

      h2, h3 {
        font-size: 1.5rem;
      }

      input, button {
        font-size: 0.9rem;
        padding: 8px;
      }

      .back-button {
        font-size: 0.9rem;
        padding: 8px 16px;
      }

      th, td {
        font-size: 0.9rem;
        padding: 8px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Check Wallet Balance</h2>
  <form method="POST">
    <label for="phone" style="--i: 1;">Phone Number</label>
    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required autofocus style="--i: 2;">
    <button type="submit" style="--i: 3;">Check Balance</button>
  </form>

  <?php if ($balanceChecked): ?>
    <?php if ($user): ?>
      <div class="result">
        Hello <strong><?= htmlspecialchars($user['name']) ?></strong>, your wallet balance is: 
        <strong>₹<?= number_format($balance, 2) ?></strong>
      </div>

      <?php if (!empty($wallet)): ?>
        <h3>Wallet Transaction History</h3>
        <table>
          <tr>
            <th>Amount</th>
            <th>UTR</th>
            <th>Message</th>
            <th>Date</th>
          </tr>
          <?php $i = 1; foreach ($wallet as $entry): ?>
            <tr style="--i: <?= $i++; ?>">
              <td>₹<?= number_format($entry['amount'], 2) ?></td>
              <td><?= htmlspecialchars($entry['utr']) ?></td>
              <td><?= htmlspecialchars($entry['additional_msg'] ?: 'N/A') ?></td>
              <td><?= date("d M Y, h:i A", strtotime($entry['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <p style="margin-top:10px;">No wallet transactions found.</p>
      <?php endif; ?>
    <?php else: ?>
      <div class="result error">
        No user found with phone number: <?= htmlspecialchars($phone) ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Back button -->
  <a href="dashboard.php" class="back-button">← Back</a>
</div>
</body>
</html>