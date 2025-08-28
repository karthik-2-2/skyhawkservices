<?php
session_start();
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_phone'])) {
    header('Location: index.php');
    exit();
}

$phone_session = $_SESSION['user_phone'];

// Check for completed orders that need reviews
$stmt_pending_reviews = $conn->prepare("SELECT id, service_type, booking_date, total_price FROM userordersuccess WHERE phone = ? AND (rating IS NULL OR rating = 0) ORDER BY created_at DESC LIMIT 1");
$stmt_pending_reviews->execute([$phone_session]);
$pending_review = $stmt_pending_reviews->fetch();

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $review = trim($_POST['review']);

    if ($order_id && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("UPDATE userordersuccess SET rating = ?, review = ? WHERE id = ? AND phone = ?");
        if ($stmt->execute([$rating, $review, $order_id, $phone_session])) {
            $review_success = true;
            // Refresh to remove the popup
            header("Location: dashboard.php?review_submitted=1");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Skyhawk - Online Drone Service</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

  <style>
    :root {
      --primary-mint: #4ECDC4;
      --secondary-mint: #45C0B7;
      --light-mint: #E8FAF9;
      --dark-mint: #3BA99E;
      --text-dark: #2C3E50;
      --text-light: #7F8C8D;
      --white: #FFFFFF;
      --gradient-bg: linear-gradient(135deg, #4ECDC4 0%, #45C0B7 100%);
      --card-shadow: 0 10px 30px rgba(78, 205, 196, 0.2);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background: var(--gradient-bg);
      color: var(--text-dark);
      overflow-x: hidden;
      margin: 0;
      padding: 0;
    }

    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: var(--dark-gray);
      padding: 0.5rem 1rem;
      height: 60px;
    }

    .logo-img {
      height: 80px;
      width: auto;
      object-fit: contain;
      margin-right: 0.75rem;
      display: block;
      animation: slideInLeft 1s ease forwards;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
      position: relative;
    }

    .nav-links a.nav-link {
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--mint-green);
      text-decoration: none;
      animation: fadeInRight 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .nav-links a.nav-link:hover {
      color: var(--metallic-silver);
    }

    .dropdown-container {
      position: relative;
    }

    .dropdown-toggle {
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--mint-green);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      animation: fadeInRight 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .dropdown-toggle:hover {
      color: var(--metallic-silver);
    }

    .dropdown-toggle i {
      transition: transform 0.3s ease;
    }

    .dropdown-toggle.active i {
      /* Removed rotation effect for profile icon */
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      background-color: var(--dark-gray);
      padding: 1rem;
      border-radius: 8px;
      border: 1px solid var(--mint-green);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
      z-index: 1001;
      flex-direction: column;
      gap: 0.75rem;
      min-width: 150px;
      margin-top: 0.5rem;
    }

    .dropdown.active {
      display: flex;
    }

    .dropdown a {
      color: var(--metallic-silver);
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 0;
      transition: color 0.3s ease;
    }

    .dropdown a:hover {
      color: var(--mint-green);
    }

    .menu-toggle {
      display: none;
      font-size: 2rem;
      cursor: pointer;
      color: var(--mint-green);
      animation: bounceIn 1s ease forwards;
    }

    @media (max-width: 768px) {
      .hero-section {
        padding: 1rem;
        height: auto; /* Remove fixed height on mobile */
        min-height: 70vh; /* Reduced from 100vh to reduce excessive spacing */
      }

      .hero-title-container {
        margin-bottom: 1rem;
      }

      #hero-line1 {
        font-size: clamp(18px, 6vw, 50px);
        min-height: 40px;
      }

      #hero-line2 {
        font-size: clamp(30px, 10vw, 80px);
        min-height: 80px;
      }

      .hero-content p {
        font-size: clamp(14px, 4vw, 18px);
        margin-bottom: 1.5rem;
        padding: 0 1rem;
      }

      .menu-toggle {
        display: block;
      }

      .nav-links {
        display: none;
        flex-direction: column;
        background-color: var(--dark-gray);
        width: 100%;
        position: absolute;
        top: 60px;
        left: 0;
        padding: 1rem 2rem;
        z-index: 999;
      }

      .nav-links.active {
        display: flex;
      }

      .nav-links a.nav-link,
      .dropdown-toggle {
        animation: slideInUp 0.5s ease forwards;
        animation-delay: calc(0.1s * var(--i));
      }

      .dropdown {
        position: static;
        background-color: #222;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        border: 1px solid var(--mint-green);
        box-shadow: none;
      }

      .dropdown a {
        animation: slideInUp 0.5s ease forwards;
        animation-delay: calc(0.1s * var(--i));
      }

      .cta-section {
        padding: 2rem 1rem;
      }

      .services-section {
        padding: 3rem 1rem; /* Ensure mobile padding for services */
      }

      .about-section {
        padding: 3rem 1rem; /* Ensure mobile padding for about */
        margin: 0 1rem 2rem 1rem; /* Add side margins */
      }

      .cta-section h2 {
        font-size: 1.8rem;
      }

      .cta-section p {
        font-size: 1.2rem;
      }

      .contact-info {
        flex-direction: column;
        gap: 1rem;
        font-size: 1rem;
      }

      .contact-form {
        max-width: 90%;
        width: 100%;
        margin: 1.5rem auto 0;
      }

      .contact-form label {
        font-size: 0.9rem;
      }

      .contact-form input,
      .contact-form textarea {
        padding: 0.5rem;
        font-size: 0.9rem;
      }

      .contact-form textarea {
        min-height: 80px;
      }

      .contact-form button {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
      }
    }

    .hero-section {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: linear-gradient(135deg, #000, var(--mint-green));
      color: var(--metallic-silver);
      padding: 1rem; /* Reduced from 3rem */
    }

    .hero-content {
      /* This is already a flex container */
    }

    /* --- Flip Animation Styles (COMMENTED OUT) --- */
    /*
    .hero-title-container {
      position: relative;
      height: 300px;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 1rem;
      perspective: 1200px; 
      cursor: pointer;
    }

    .hero-flipper {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 1.2s ease-in-out;
      transform-style: preserve-3d;
    }
    
    .hero-flipper.flipped {
      transform: rotateY(180deg);
    }
    
    .flip-back, .flip-front {
      position: absolute;
      width: 100%;
      height: 100%;
      backface-visibility: hidden; 
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .flip-back {
      transform: rotateY(180deg);
    }

    #hero-logo {
      height: 350px;
      width: auto;
      object-fit: contain;
    }
    */

    /* Simplified hero title without flip animation */
    .hero-title-container {
      position: relative;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 2rem;
    }

    /* === HERO TYPING ANIMATION STYLES === */
    .hero-text-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    #hero-line1, #hero-line2 {
      color: var(--mint-green);
      position: relative;
    }

    #hero-line1 {
      font-size: clamp(20px, 8vw, 60px);
      font-weight: 600;
      min-height: 48px; /* Reserve space to prevent layout shift */
    }

    #hero-line2 {
      font-size: clamp(40px, 14vw, 100px);
      font-weight: 700;
      min-height: 96px; /* Reserve space to prevent layout shift */
    }

    /* The blinking cursor is now handled by a simple class */
    .blinking-cursor {
        border-right: 4px solid var(--mint-green);
        animation: blink 1s step-end infinite;
    }

    @keyframes blink {
      50% { border-color: transparent; }
    }


    .hero-content p {
      font-size: clamp(12px,2vw,80px );
      margin-bottom: 2.3rem;
      animation: fadeInUp 1.5s forwards 0.5s;
    }

    .cta-btn,
    .book-btn,
    .contact-form button,
    .modal-content button {
      animation: pulse 1.5s ease-in-out infinite;
    }

    .cta-btn {
      display: inline-block;
      padding: 1rem 2rem;
      background-color: var(--mint-green);
      color: #000;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.3s ease;
      animation: bounceIn 1s ease forwards 1s;
    }

    .cta-btn:hover {
      background-color: #2c7a68;
      transform: scale(1.1) translateY(-3px);
    }

    .services-section {
      background-color: var(--black);
      padding: 4rem 2rem; /* Added horizontal padding */
    }

    .services-section h2 {
      text-align: center;
      margin-bottom: 3rem;
      font-size: 2.5rem;
      color: var(--mint-green);
      animation: fadeInDown 1s ease forwards;
    }


    .services {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: center;
      perspective: 3000px;
    }

    .card {
      background-color: var(--dark-gray);
      border-radius: 20px; /* Increased from 16px for softer look */
      width: 270px;
      height: 440px;
      perspective: 1000px;
      position: relative;
      display: inline-block;
      margin: 1rem;
      cursor: pointer;
      border: 1px solid var(--mint-green);
      box-shadow: 5px 5px 10px var(--mint-green), -5px -5px 10px var(--mint-green);
      vertical-align: top;
      color: var(--metallic-silver);
      overflow: hidden; /* Ensure content respects the border radius */
    }
    
    .card .services-card-front, .card .services-card-back {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      border-radius: 20px; /* Increased from 16px to match card */
      backface-visibility: hidden;
      transition: transform 1.5s cubic-bezier(0.23, 1, 0.32, 1);
      display: flex;
      flex-direction: column;
      overflow: hidden; /* Ensure content respects the border radius */
    }
    
    .card .services-card-front {
      background-size: cover;
      background-position: center;
      z-index: 2;
      transform: rotateY(0deg);
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      justify-content: space-between; /* Changed to distribute space between top and bottom */
    }
    
    .card .services-card-back {
      background: var(--dark-gray);
      color: var(--metallic-silver);
      transform: rotateY(180deg);
      z-index: 3;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 2rem;
    }
    
    .card:hover .services-card-front {
      transform: rotateY(180deg);
    }
    
    .card:hover .services-card-back {
      transform: rotateY(0deg);
    }

    /* NEW: Top image section */
    .service-image-frame {
      height: 65%; /* Top 65% */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: var(--black); /* Solid black background */
      border-radius: 20px 20px 0 0; /* Rounded top corners to match card */
    }

    .service-image-frame img {
      width: 80%;
      height: 80%;
      object-fit: cover; /* Changed from contain to cover for better aspect ratio */
      border-radius: 15px; /* Increased from 8px for softer image corners */
      border: none; /* Removed the mint green border */
      background: rgba(0,0,0,0.3);
      padding: 10px;
    }

    /* MODIFIED: Bottom info section */
    .service-card-info {
      height: 35%; /* Bottom 35% */
      background: var(--black); /* Solid black background */
      border-radius: 0 0 20px 20px; /* Increased from 16px to match card */
      padding: 1rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .service-card-info h3 {
      margin: 0 0 10px 0;
      font-size: 1.1rem; /* Reduced from 1.3rem for first card */
      font-weight: bold;
      color: var(--mint-green); /* Mint green title */
      text-align: center;
      text-shadow: 2px 2px 6px var(--dark-gray);
    }

    /* Coming Soon Animation for last 2 cards */
    .coming-soon {
      font-size: 0.95rem;
      color: var(--mint-green);
      text-align: center;
      animation: pulse-glow 2s ease-in-out infinite;
      margin-top: 5px;
      font-weight: bold;
    }

    @keyframes pulse-glow {
      0%, 100% {
        opacity: 1;
        text-shadow: 0 0 5px var(--mint-green);
      }
      50% {
        opacity: 0.7;
        text-shadow: 0 0 15px var(--mint-green), 0 0 25px var(--mint-green);
      }
    }

    .service-card-info .specs {
      color: var(--metallic-silver); /* Metallic silver text */
      font-size: 0.9rem;
      line-height: 1.6;
      text-align: left; /* Starts from left */
    }

    .service-card-info .specs strong {
      color: var(--metallic-silver);
      font-weight: bold;
    }
    .service-card-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
    }
    .service-card-content p {
      margin-bottom: 1.5rem;
      color: var(--metallic-silver);
    }

    .book-btn {
      display: inline-block;
      margin-top: 1rem;
      padding: 0.75rem 1.5rem;
      background-color: var(--mint-green);
      color: #000;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      transition: background-color 0.3s ease, transform 0.3s ease;
      border: none;
      cursor: pointer;
    }

    .book-btn:hover {
      background-color: #2c7a68;
      transform: scale(1.1) translateY(-3px);
    }

    .about-section {
      background-color: var(--dark-gray);
      padding: 4rem 2rem;
      max-width: 900px;
      margin: 0 auto 4rem auto;
      border-radius: 16px;
      color: var(--metallic-silver);
    }

    .about-section h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--mint-green);
      font-size: 2.5rem;
      animation: fadeInDown 1s ease forwards;
    }

    .about-section p {
      font-size: 1.2rem;
      line-height: 1.6;
      text-align: center;
      max-width: 700px;
      margin: 0 auto;
      animation: fadeInUp 1s ease forwards 0.3s;
    }

    .cta-section {
      background-color: var(--mint-green);
      color: #000;
      padding: 4rem 2rem; /* Added horizontal padding */
      text-align: center;
    }

    .cta-section h2 {
      margin-bottom: 1.5rem;
      animation: fadeInDown 1s ease forwards;
    }

    .cta-section p {
      animation: fadeInUp 1s ease forwards 0.3s;
    }

    .contact-info {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      font-size: 1.2rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
      color: #000;
    }

    .contact-info p {
      margin: 0;
      animation: slideInLeft 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .contact-info i {
      margin-right: 0.5rem;
      color: #000;
      animation: bounceIn 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .contact-form {
      max-width: 600px;
      margin: 2rem auto 0 auto;
      text-align: left;
    }

    .contact-form label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold; 
      color: #000; /* Changed to black */
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid var(--metallic-silver); 
      border-radius: 8px;
      background-color: var(--metallic-silver); /* Changed to metallic silver */
      color: #000; /* Changed text color to black */
      font-size: 1rem;
      resize: vertical;
    }

    .contact-form input::placeholder,
    .contact-form textarea::placeholder {
      color: #000; /* Changed placeholder to black */
      opacity: 0.7;
    }

    .contact-form textarea {
      min-height: 100px;
    }

    .contact-form button {
      background-color: #286f5b;
      color: #fff;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .contact-form button:hover {
      background-color: #1f5243;
      transform: scale(1.1) translateY(-3px);
    }

    .footer {
      text-align: center;
      padding: 1rem 0;
      background-color: #111;
      color: var(--metallic-silver);
    }

    .footer p {
      animation: fadeInUp 1s ease forwards;
    }
    
    .contact-section-label{
      font-weight: bold;
      color: #000; /* Changed to black */
      font-size:clamp(14px, 4vw, 20px);
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(50px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
      0% { opacity: 0; transform: translateY(-50px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInLeft {
      0% { opacity: 0; transform: translateX(-50px); }
      100% { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInRight {
      0% { opacity: 0; transform: translateX(50px); }
      100% { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInLeft {
      0% { opacity: 0; transform: translateX(-100px); }
      100% { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInUp {
      0% { opacity: 0; transform: translateY(100px); }
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

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      justify-content: center;
      align-items: center;
      z-index: 1001;
    }

    .modal-content {
      background-color: #111;
      padding: 2rem;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      color: var(--metallic-silver);
      position: relative;
      border: 1px solid var(--mint-green);
    }

    .modal-content h2 {
      margin-bottom: 1rem;
      color: var(--mint-green);
      animation: fadeInDown 1s ease forwards;
    }

    .modal-content input,
    .modal-content textarea,
    .modal-content select {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: none;
      border-radius: 8px;
      background-color: #222;
      color: var(--metallic-silver);
      animation: slideInUp 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .modal-content label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--metallic-silver);
      animation: fadeInLeft 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .modal-content button {
      background-color: var(--mint-green);
      color: #000;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .modal-content button:hover {
      background-color: #2c7a68;
      transform: scale(1.1) translateY(-3px);
    }

    .modal-content .price-display {
      margin-bottom: 1rem;
      font-size: 1.2rem;
      color: var(--mint-green);
      text-align: center;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.5rem;
      color: var(--metallic-silver);
      cursor: pointer;
      animation: bounceIn 1s ease forwards;
    }

    .close-btn:hover {
      color: var(--mint-green);
      transform: scale(1.2);
    }

    .coming-soon-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      justify-content: center;
      align-items: center;
      z-index: 1001;
    }

    .coming-soon-content {
      background-color: #111;
      padding: 2rem;
      border-radius: 12px;
      max-width: 400px;
      width: 90%;
      color: var(--metallic-silver);
      position: relative;
      border: 1px solid var(--mint-green);
      text-align: center;
    }

    .coming-soon-content h2 {
      margin-bottom: 1rem;
      color: var(--mint-green);
      animation: fadeInDown 1s ease forwards;
    }

    .coming-soon-content p {
      margin-bottom: 1.5rem;
      animation: fadeInUp 1s ease forwards 0.3s;
    }

    .coming-soon-content button {
      background-color: var(--mint-green);
      color: #000;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .coming-soon-content button:hover {
      background-color: #2c7a68;
      transform: scale(1.1) translateY(-3px);
    }

    .close-coming-soon {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.5rem;
      color: var(--metallic-silver);
      cursor: pointer;
      animation: bounceIn 1s ease forwards;
    }

    .close-coming-soon:hover {
      color: var(--mint-green);
      transform: scale(1.2);
    }

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
  <nav class="navbar">
    <img src="logo.png" alt="Skyhawk Logo" class="logo-img">
    <div class="menu-toggle">☰</div>
    <div class="nav-links" id="navLinks">
      <a href="#services" class="nav-link" style="--i: 1;">Services</a>
      <a href="#about" class="nav-link" style="--i: 2;">About Us</a>
      <a href="#contact" class="nav-link" style="--i: 3;">Contact Us</a>
      <div class="dropdown-container">
        <div class="dropdown-toggle" style="--i: 4;">
          My Account <i class="fas fa-user"></i>
        </div>
        <div class="dropdown" id="accountDropdown">
          <a href="profile.php" style="--i: 1;" class="nav-link">Profile</a>
          <a href="my_bookings.php" style="--i: 1;" class="nav-link">My Bookings</a>
          <a href="../index.php" style="--i: 2;" class="nav-link">Logout</a>
        </div>
      </div>
    </div>
  </nav>

  <header class="hero-section" data-aos="fade-right" data-aos-delay="200">
    <div class="hero-content">
      <div class="hero-title-container">
        <!-- <div class="hero-flipper">
          <div class="flip-front"> -->
            <div class="hero-text-wrapper">
                <span id="hero-line1"></span>
                <span id="hero-line2"></span>
            </div>
          <!-- </div>
          <div class="flip-back">
            <img src="logo.png" alt="Skyhawk Logo" id="hero-logo">
          </div>
        </div> -->
      </div>
      <p>"Experience precision, reliability, and speed with Skyhawk’s advanced drone services.<br>From aerial photography to surveying, we deliver top-notch solutions for all your needs."</p>
      <a href="#services" class="cta-btn">Explore Services</a>
    </div>
  </header>

  <section id="services" class="services-section" data-aos="fade-right" data-aos-delay="300">
    <h2>Our Drone Services</h2>

    <div class="services">
      <div class="card" data-aos="fade-right" data-aos-delay="400" data-service="Videography & Photography" data-price="2000">
        <div class="services-card-front">
          <div class="service-image-frame">
            <img src="./drone2.png" alt="Videography Drone">
          </div>
          <div class="service-card-info">
            <h3>Videography & Photography</h3>
            <div class="specs">
              <strong>Model:</strong> DJI Mini 3<br>
              <strong>Fly Time:</strong> 25 min per Battery<br>
              <strong>Experience:</strong> 4 yrs
            </div>
          </div>
        </div>
        <div class="services-card-back">
          <div class="service-card-content">
            <p>Capture stunning aerial footage for events, marketing, and real estate.</p>
            <!-- <a href="user/index.php" class="book-btn">Book Now</a> -->
            <button class="book-btn" onclick="openBookingModal(this)">Book Now</button>
          </div>
        </div>
      </div>

      <div class="card" data-aos="fade-right" data-aos-delay="500" data-service="Inspection" data-price="2000">
        <div class="services-card-front">
          <div class="service-image-frame">
            <img src="./drone1.png" alt="Inspection Drone">
          </div>
          <div class="service-card-info">
            <h3>Inspection</h3>
            <div class="specs">
              <strong>Model:</strong> DJI Mini 3<br>
              <strong>Fly Time:</strong> 25 min per Battery<br>
              <strong>Experience:</strong> 4 yrs
            </div>
          </div>
        </div>
        <div class="services-card-back">
          <div class="service-card-content">
            <p>Perform structural and utility inspections with precision and safety.</p>
            <button class="book-btn" onclick="openBookingModal(this)">Book Now</button>
          </div>
        </div>
      </div>
      <div class="card" data-aos="fade-right" data-aos-delay="600">
        <div class="services-card-front">
          <div class="service-image-frame">
            <img src="./spray.png" alt="Agriculture Spraying Drone">
          </div>
          <div class="service-card-info">
            <h3>Agriculture Spraying</h3>
            <div class="coming-soon">Coming Soon</div>
          </div>
        </div>
        <div class="services-card-back">
          <div class="service-card-content">
            <p>Efficient and eco-friendly crop spraying with smart drone technology.</p>
            <button class="book-btn" onclick="openComingSoonModal()">Book Now</button>
          </div>
        </div>
      </div>
      <div class="card" data-aos="fade-right" data-aos-delay="700">
        <div class="services-card-front">
          <div class="service-image-frame">
            <img src="./dheli.png" alt="Delivery Drone">
          </div>
          <div class="service-card-info">
            <h3>Delivery Service</h3>
            <div class="coming-soon">Coming Soon</div>
          </div>
        </div>
        <div class="services-card-back">
          <div class="service-card-content">
            <p>Fast and reliable drone delivery, bringing your packages swiftly and safely to your doorstep.</p>
            <button class="book-btn" onclick="openComingSoonModal()">Book Now</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="about-section" id="about" data-aos="fade-right" data-aos-delay="300">
    <h2>About Us</h2>
    <p>
      At Skyhawk, we believe that advanced drone technology should be accessible to everyone. Our mission is to empower individuals and communities by offering versatile aerial solutions that cater to a wide range of needs—from capturing breathtaking moments and providing detailed surveys to enhancing security and supporting innovative projects. With our state-of-the-art drones and a team of dedicated experts, we ensure that every customer receives personalized service, enabling you to leverage the power of aerial perspectives for any purpose you envision. Join us as we redefine what's possible with drone technology for all.
    </p>
  </section>

  <section class="cta-section" id="contact" data-aos="fade-right" data-aos-delay="300">
    <h2>Get a Custom Drone Service Quote</h2>
    <p><b>Contact Us</b></p>
    <form class="contact-form" action="submit_contact.php" method="POST">
      <label for="contact-name" data-aos="fade-right" data-aos-delay="350" class="contact-section-label">Name</label>
      <input type="text" id="contact-name" name="name" placeholder="Your Name" required data-aos="fade-right" data-aos-delay="400">
      
      <label for="contact-phone" data-aos="fade-right" data-aos-delay="450" class="contact-section-label">Phone Number</label>
      <input type="tel" id="contact-phone" name="phone" placeholder="Your Phone Number" required data-aos="fade-right" data-aos-delay="500">
      
      <label for="contact-email" data-aos="fade-right" data-aos-delay="550" class="contact-section-label">Email</label>
      <input type="email" id="contact-email" name="email" placeholder="Your Email" required data-aos="fade-right" data-aos-delay="600">
      
      <label for="contact-message" data-aos="fade-right" data-aos-delay="650" class="contact-section-label">Message</label>
      <textarea id="contact-message" name="message" placeholder="Your Message" rows="5" data-aos="fade-right" data-aos-delay="700"></textarea>
      
      <button type="submit" data-aos="fade-right" data-aos-delay="750">Send Message</button>
    </form>
  </section>

  <footer class="footer">
    <p>© 2025 Skyhawk. All rights reserved.</p>
  </footer>
  <div id="bookingModal" class="modal">
    <div class="modal-content" data-aos="zoom-in" data-aos-delay="100">
      <span class="close-btn" onclick="closeModal()">×</span>
      <h2 id="modal-title">Book a Service</h2>
      <form id="bookingForm">
        <label for="booking_date" style="margin-top: 1rem;">Select Date:</label>
        <input type="date" id="booking_date" name="booking_date" required>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
          <div>
            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required onchange="calculateDuration()">
          </div>
          <div>
            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required onchange="calculateDuration()">
          </div>
        </div>
        
        <div style="margin-top: 1rem;">
          <label>Duration: <span id="duration-display">Please select start and end time</span></label>
          <input type="hidden" id="hours" name="hours" value="1">
        </div>
        
        <div class="price-display" id="priceDisplay">Total Price: ₹<span id="price-per-hour">2000</span></div>
        
        <button type="button" onclick="initiatePayment()">Proceed to Payment</button>
      </form>
    </div>
  </div>

  <div id="comingSoonModal" class="coming-soon-modal">
    <div class="coming-soon-content" data-aos="fade-right" data-aos-delay="100">
      <span class="close-coming-soon" onclick="closeComingSoonModal()">×</span>
      <h2>Coming Soon</h2>
      <p>This service is not yet available. Stay tuned for updates!</p>
      <button onclick="closeComingSoonModal()">OK</button>
    </div>
  </div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    AOS.init({
      duration: 1000,
      once: false,
      anchorPlacement: 'bottom-center'
    });

    // --- ORIGINAL MENU/DROPDOWN SCRIPT (UNCHANGED) ---
    function toggleMenu() {
      const navLinks = document.getElementById('navLinks');
      const dropdown = document.getElementById('accountDropdown');
      const dropdownToggle = document.querySelector('.dropdown-toggle');
      if (navLinks) {
        navLinks.classList.toggle('active');
        if (dropdown && dropdown.classList.contains('active')) {
          dropdown.classList.remove('active');
          dropdownToggle.classList.remove('active');
        }
      }
    }

    function toggleDropdown(event) {
      event.stopPropagation();
      const dropdown = document.getElementById('accountDropdown');
      const dropdownToggle = document.querySelector('.dropdown-toggle');
      if (dropdown && dropdownToggle) {
        dropdown.classList.toggle('active');
        dropdownToggle.classList.toggle('active');
      }
    }

    // --- NEW AND UPDATED FUNCTIONS FOR BOOKING WORKFLOW ---
    
    // Global variables to store service details when a modal is opened
    let currentService, currentPricePerHour;

    // RENAMED THIS FUNCTION to openBookingModal
    function openBookingModal(button) {
        const card = button.closest('.card');
        currentService = card.dataset.service;
        currentPricePerHour = parseFloat(card.dataset.price);

        // Update the new modal with the correct service info
        document.getElementById('modal-title').innerText = `Book: ${currentService}`;
        document.getElementById('price-per-hour').innerText = currentPricePerHour;
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('booking_date').setAttribute('min', today);
        
        // Reset form values
        document.getElementById('booking_date').value = '';
        document.getElementById('start_time').value = '';
        document.getElementById('end_time').value = '';
        document.getElementById('hours').value = 1;
        document.getElementById('duration-display').innerText = 'Please select start and end time';
        updatePrice(); // Calculate the initial price
        
        document.getElementById('bookingModal').style.display = 'flex'; // Use flex to center it
    }

    // Calculate duration between start and end time
    function calculateDuration() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (startTime && endTime) {
            const start = new Date(`2000-01-01 ${startTime}`);
            const end = new Date(`2000-01-01 ${endTime}`);
            
            if (end <= start) {
                alert('End time must be after start time');
                document.getElementById('end_time').value = '';
                document.getElementById('duration-display').innerText = 'Please select valid times';
                return;
            }
            
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            // Round to nearest 0.5 hour
            const roundedHours = Math.ceil(diffHours * 2) / 2;
            
            document.getElementById('hours').value = roundedHours;
            document.getElementById('duration-display').innerText = `${roundedHours} hour(s)`;
            
            updatePrice();
        }
    }

    // This closes the main booking modal
    function closeModal() {
      document.getElementById("bookingModal").style.display = "none";
    }

    // This is your original function for "Coming Soon" services (UNCHANGED)
    function openComingSoonModal() {
      const modal = document.getElementById("comingSoonModal");
      if (modal) {
        modal.style.display = "flex";
      }
    }

    // This is your original function for closing the "Coming Soon" modal (UNCHANGED)
    function closeComingSoonModal() {
      const modal = document.getElementById("comingSoonModal");
      if (modal) {
        modal.style.display = "none";
      }
    }

    // This is your original updatePrice function, now used by the new modal
    function updatePrice() {
        // It now uses the global variable for price per hour
        const hours = parseFloat(document.getElementById('hours').value) || 1;
        const totalPrice = currentPricePerHour * hours;
        document.getElementById('priceDisplay').innerText = `Total Price: ₹${totalPrice.toLocaleString('en-IN')}`;
    }

    // ** THIS IS THE NEW FUNCTION THAT REDIRECTS TO THE PAYMENT PAGE **
    function initiatePayment() {
        const bookingDate = document.getElementById('booking_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const hours = parseFloat(document.getElementById('hours').value);

        if (!bookingDate) {
            alert('Please select a booking date.');
            return;
        }

        if (!startTime || !endTime) {
            alert('Please select start and end times.');
            return;
        }

        if (hours <= 0) {
            alert('Please select valid times that give a positive duration.');
            return;
        }

        const totalPrice = currentPricePerHour * hours;

        const bookingDetails = {
            service_type: currentService,
            booking_date: bookingDate,
            start_time: startTime,
            end_time: endTime,
            hours: hours,
            total_price: totalPrice
        };

        // Save booking details to sessionStorage to pass them to the payment page
        sessionStorage.setItem('bookingDetails', JSON.stringify(bookingDetails));

        // Redirect to the new payment page
        window.location.href = 'payment.php';
    }

    // Your original event listeners, now combined to handle all modals
    window.addEventListener('load', function() {
      const menuToggle = document.querySelector('.menu-toggle');
      const dropdownToggle = document.querySelector('.dropdown-toggle');
      const navLinks = document.querySelectorAll('.nav-link');
      
      if (menuToggle) {
        menuToggle.addEventListener('click', toggleMenu);
        menuToggle.addEventListener('touchstart', function(e) {
          e.preventDefault();
          toggleMenu();
        });
      }

      if (dropdownToggle) {
        dropdownToggle.addEventListener('click', toggleDropdown);
        dropdownToggle.addEventListener('touchstart', function(e) {
          e.preventDefault();
          toggleDropdown(e);
        });
      }

      navLinks.forEach(link => {
        link.addEventListener('click', function() {
          const navLinksContainer = document.getElementById('navLinks');
          const dropdown = document.getElementById('accountDropdown');
          const dropdownToggle = document.querySelector('.dropdown-toggle');
          if (navLinksContainer.classList.contains('active')) {
            navLinksContainer.classList.remove('active');
          }
          if (dropdown && dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
            dropdownToggle.classList.remove('active');
          }
        });
      });

      window.addEventListener('click', function(event) {
        const dropdown = document.getElementById('accountDropdown');
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const bookingModal = document.getElementById("bookingModal");
        const comingSoonModal = document.getElementById("comingSoonModal");

        // Close dropdown if clicked outside
        if (dropdown && dropdownToggle && !dropdown.contains(event.target) && !dropdownToggle.contains(event.target)) {
          if (dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
            dropdownToggle.classList.remove('active');
          }
        }
      });
      
      // === START: NEW INFINITE LOOP TYPING ANIMATION LOGIC (FLIP ANIMATION COMMENTED OUT) ===
      // const flipper = document.querySelector('.hero-flipper');
      const titleContainer = document.querySelector('.hero-title-container');
      const line1 = document.getElementById('hero-line1');
      const line2 = document.getElementById('hero-line2');
      
      let isAnimating = false; // Flag to control the animation loop

      // Helper function for delays
      const delay = ms => new Promise(res => setTimeout(res, ms));

      // Type out a string character by character
      async function type(element, text, speed = 150) {
        element.classList.add('blinking-cursor');
        for (const char of text) {
          if (!isAnimating) return; // Exit if animation was stopped
          element.textContent += char;
          await delay(speed);
        }
        element.classList.remove('blinking-cursor');
      }

      // Delete text character by character
      async function deleteText(element, speed = 160) {
        element.classList.add('blinking-cursor');
        let text = element.textContent;
        while (text.length > 0) {
          if (!isAnimating) return; // Exit if animation was stopped
          text = text.slice(0, -1);
          element.textContent = text;
          await delay(speed);
        }
        element.classList.remove('blinking-cursor');
      }

      // The main infinite loop
      async function infiniteTypeLoop() {
        while (isAnimating) {
          // Typing phase
          await type(line1, "Welcome to");
          if (!isAnimating) break;
          await type(line2, "Skyhawk");
          if (!isAnimating) break;
          
          await delay(800); // Pause when text is fully displayed
          if (!isAnimating) break;

          // Deleting phase
          await deleteText(line2);
          if (!isAnimating) break;
          await deleteText(line1);
          if (!isAnimating) break;

          await delay(200); // Pause before restarting
        }
      }

      function runTypingAnimation() {
        if (isAnimating) return;
        isAnimating = true;
        infiniteTypeLoop();
      }
      
      function resetTypingAnimation() {
          isAnimating = false; // This flag will stop the loop gracefully
          line1.textContent = '';
          line2.textContent = '';
          line1.classList.remove('blinking-cursor');
          line2.classList.remove('blinking-cursor');
      }

      /* FLIP ANIMATION FUNCTIONS COMMENTED OUT
      function startInitialSequence() {
          flipper.classList.add('flipped'); 
          resetTypingAnimation();
          
          setTimeout(() => {
              flipper.classList.remove('flipped');
              setTimeout(runTypingAnimation, 1200); 
          }, 2000);
      }

      let isHoverBlocked = false;
      function handleHover() {
          if (isHoverBlocked) return;
          isHoverBlocked = true;

          flipper.classList.add('flipped');
          resetTypingAnimation();

          setTimeout(() => {
              flipper.classList.remove('flipped');
              setTimeout(runTypingAnimation, 1200);
          }, 2200);

          setTimeout(() => {
              isHoverBlocked = false;
          }, 4500);
      }

      if (flipper && titleContainer && line1 && line2) {
          titleContainer.addEventListener('mouseenter', handleHover);
          startInitialSequence();
      }
      */

      // Simple start without flip animation
      if (titleContainer && line1 && line2) {
          setTimeout(runTypingAnimation, 1000);
      }
      // === END: NEW INFINITE LOOP TYPING ANIMATION LOGIC ===


      window.addEventListener('click', function(event) {
        const dropdown = document.getElementById('accountDropdown');
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const comingSoonModal = document.getElementById("comingSoonModal");
        if (dropdown && dropdownToggle && !dropdown.contains(event.target) && !dropdownToggle.contains(event.target)) {
          if (dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
            dropdownToggle.classList.remove('active');
          }
        }
        if (comingSoonModal && event.target === comingSoonModal) {
          closeComingSoonModal();
        }
        // Close modals if clicked outside their content area
        if (bookingModal && event.target === bookingModal) {
          closeModal();
        }
        if (comingSoonModal && event.target === comingSoonModal) {
          closeComingSoonModal();
        }
      });
    });

    // Contact form clearing functionality
const contactForm = document.querySelector('.contact-form');

if (contactForm) {
    // Clear form when page loads if coming from WhatsApp redirect
    if (document.referrer.includes('wa.me') || window.location.search.includes('cleared=1')) {
        contactForm.reset();
        // Remove any URL parameters
        if (window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    // Clear form data when form is submitted
    contactForm.addEventListener('submit', function() {
        // Small delay to ensure form submission completes
        setTimeout(() => {
            this.reset();
            // Clear any stored form data
            localStorage.removeItem('contactFormData');
            sessionStorage.removeItem('contactFormData');
        }, 100);
    });

    // Store form data on input (for recovery if needed)
    const formInputs = contactForm.querySelectorAll('input, textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            const formData = new FormData(contactForm);
            const formObject = {};
            formData.forEach((value, key) => {
                formObject[key] = value;
            });
            localStorage.setItem('contactFormData', JSON.stringify(formObject));
        });
    });
}

// Clear form when navigating back from WhatsApp
window.addEventListener('pageshow', function(event) {
    if (contactForm && (event.persisted || document.referrer.includes('wa.me'))) {
        contactForm.reset();
        localStorage.removeItem('contactFormData');
        sessionStorage.removeItem('contactFormData');
    }
});

// Rating popup functionality
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
  if (document.getElementById('ratingForm')) {
    document.getElementById('ratingForm').addEventListener('submit', function(e) {
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
  window.location.href = 'dashboard.php?review_submitted=1';
}

// Close popup when clicking outside
document.addEventListener('click', function(e) {
  const ratingOverlay = document.getElementById('ratingOverlay');
  const ratingPopup = document.querySelector('.rating-popup');
  
  if (ratingOverlay && e.target === ratingOverlay) {
    skipReview();
  }
});
  </script>

  <!-- Modern Rating Popup -->
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
      
      <form method="POST" id="ratingForm">
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
</body>
</html>