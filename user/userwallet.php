<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wallet Top-Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      background-color: var(--dark-gray);
      color: var(--metallic-silver);
    }

    .cta-section {
      background-color: var(--darker-gray);
      padding: 40px 20px;
      border-radius: 16px;
      max-width: 600px;
      margin: 40px auto;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
      border: 1px solid var(--mint-green);
      animation: fadeInDown 1s ease forwards;
    }

    .cta-section h2 {
      text-align: center;
      font-size: 2rem;
      margin-bottom: 10px;
      color: var(--mint-green);
      animation: fadeInDown 1s ease forwards;
    }

    .cta-section p {
      text-align: center;
      font-size: 1rem;
      margin-bottom: 30px;
      color: var(--metallic-silver);
      animation: fadeInDown 1s ease forwards;
      animation-delay: 0.2s;
    }

    .cta-section img {
      width: 200px;
      border: 4px solid var(--mint-green);
      border-radius: 12px;
      display: block;
      margin: 0 auto 30px auto;
      animation: bounceIn 1s ease forwards;
    }

    .contact-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .contact-form label {
      font-weight: bold;
      margin-bottom: 5px;
      color: var(--metallic-silver);
      animation: fadeInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .contact-form input[type="text"],
    .contact-form input[type="tel"],
    .contact-form input[type="number"] {
      padding: 10px;
      border: 2px solid var(--metallic-silver);
      border-radius: 8px;
      font-size: 1rem;
      background-color: #333;
      color: var(--metallic-silver);
      transition: border-color 0.3s, box-shadow 0.3s;
      animation: fadeInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .contact-form input:focus {
      outline: none;
      border-color: var(--mint-green);
      box-shadow: 0 0 5px rgba(62, 180, 137, 0.5);
    }

    .contact-form button {
      padding: 12px;
      font-size: 1rem;
      font-weight: bold;
      background-color: var(--mint-green);
      color: #000;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.3s;
      animation: fadeInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
      animation: pulse 1.5s ease-in-out infinite;
    }

    .contact-form button:hover {
      background-color: #2c7a68;
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
    @media (max-width: 600px) {
      .cta-section {
        padding: 30px 15px;
        margin: 20px;
      }

      .cta-section h2 {
        font-size: 1.5rem;
      }

      .cta-section p {
        font-size: 0.9rem;
      }

      .cta-section img {
        width: 150px;
      }

      .contact-form input,
      .contact-form button {
        font-size: 0.9rem;
        padding: 8px;
      }
    }
  </style>
</head>
<body>

  <!-- Wallet Section -->
  <section class="cta-section" id="wallet">
    <h2>Your Wallet</h2>
    <p>Scan the QR code below to top-up your wallet. Then fill the form to notify us of the transaction.</p>

    <img src="qr_code1.png" alt="Wallet QR Code">

    <form class="contact-form" action="wallet_topup.php" method="POST">
      <label for="wallet-name" style="--i: 1;">Your Name</label>
      <input type="text" id="wallet-name" name="name" required style="--i: 2;">

      <label for="wallet-phone" style="--i: 3;">Phone Number</label>
      <input type="tel" id="wallet-phone" name="phone" required style="--i: 4;">

      <label for="amount" style="--i: 5;">Amount (INR)</label>
      <input type="number" id="amount" name="amount" required style="--i: 6;">

      <label for="txn_id" style="--i: 7;">Transaction ID</label>
      <input type="text" id="txn_id" name="txn_id" required style="--i: 8;">

      <button type="submit" style="--i: 9;">Submit</button>
    </form>
  </section>

  <script>
    window.addEventListener('load', function() {
      const amountInput = document.getElementById('amount');
      const storedPrice = localStorage.getItem('bookingTotalPrice');
      if (storedPrice) {
        amountInput.value = storedPrice; // Populate the Amount field
        localStorage.removeItem('bookingTotalPrice'); // Clear localStorage
      }
    });
  </script>
</body>
</html>