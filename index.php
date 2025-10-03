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
      --primary-green: #34d19d;
      --primary-blue: #38c1f2;
      --light-grey: #f5f5f5;
      --text-black: #000000;
      --text-grey: #666666;
      --text-light-grey: #999999;
      --white: #FFFFFF;
      --animated-gradient: linear-gradient(-45deg, #34d19d, #38c1f2, #34d19d, #38c1f2);
      --card-shadow: 0 8px 24px rgba(52, 209, 157, 0.15);
      --button-shadow: 0 4px 12px rgba(52, 209, 157, 0.15);
      --hover-shadow: 0 12px 32px rgba(52, 209, 157, 0.25);
      --section-bg: rgba(255, 255, 255, 0.95);
      --section-shadow: 0 8px 32px rgba(52, 209, 157, 0.1);
      --card-bg: rgba(255, 255, 255, 0.2);
      --inner-card-bg: rgba(255, 255, 255, 0.25);
      --glass-border: rgba(255, 255, 255, 0.3);
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
      background: var(--animated-gradient);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      color: var(--text-black);
      overflow-x: hidden;
      margin: 0;
      padding: 0;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Universal Button Hover Effect */
    button, .button, input[type="submit"] {
      transition: all 0.3s ease;
    }

    button:hover, .button:hover, input[type="submit"]:hover {
      transform: translateY(-3px);
      box-shadow: var(--hover-shadow);
    }

    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 1rem 2rem;
      height: 70px;
      box-shadow: var(--card-shadow);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
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
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--text-black);
      text-decoration: none;
      animation: fadeInRight 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
      transition: all 0.3s ease;
      padding: 0.5rem 1rem;
      border-radius: 25px;
    }

    .nav-links a.nav-link:hover {
      color: var(--white);
      background: var(--primary-green);
      transform: translateY(-2px);
      box-shadow: var(--button-shadow);
    }

    .dropdown-container {
      position: relative;
    }

    .dropdown-toggle {
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--text-black);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      animation: fadeInRight 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
      transition: all 0.3s ease;
      padding: 0.5rem 1rem;
      border-radius: 25px;
    }

    .dropdown-toggle:hover {
      color: var(--white);
      background: var(--primary-blue);
      transform: translateY(-2px);
      box-shadow: var(--button-shadow);
    }

    .dropdown-toggle i {
      transition: transform 0.3s ease;
    }

    .dropdown-toggle.active i {
      transform: rotate(180deg);
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 1rem;
      border-radius: 15px;
      border: 1px solid var(--primary-green);
      box-shadow: var(--card-shadow);
      z-index: 1001;
      flex-direction: column;
      gap: 0.75rem;
      min-width: 150px;
    }

    .dropdown.active {
      display: flex;
    }

    .dropdown a {
      color: var(--text-black);
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
      border-radius: 10px;
    }

    .dropdown a:hover {
      color: var(--white);
      background: var(--primary-green);
      transform: translateX(5px);
      box-shadow: var(--button-shadow);
    }

    .menu-toggle {
      display: none;
      font-size: 2rem;
      cursor: pointer;
      color: var(--primary-green);
      animation: bounceIn 1s ease forwards;
    }

    @media (max-width: 768px) {
      .navbar {
        padding: 1rem;
        height: 60px;
      }

      .hero-section {
        min-height: 100vh; /* Full screen height on mobile too */
        padding: 1rem;
        padding-top: calc(60px + 1rem); /* Account for mobile navbar height */
      }

      .hero-title-container {
        margin-bottom: 1rem;
      }

      .hero-text-wrapper {
        min-height: clamp(80px, 15vh, 120px); /* Further reduced for mobile */
        margin-bottom: 0.8rem; /* Further reduced margin */
      }

      #hero-line1 {
        font-size: clamp(18px, 6vw, 40px);
        min-height: clamp(18px, 4vh, 30px); /* Further reduced height */
        height: clamp(18px, 4vh, 30px);
        margin-bottom: 0.5rem; /* Increased gap between lines on mobile */
      }

      #hero-line2 {
        font-size: clamp(28px, 10vw, 65px);
        min-height: clamp(35px, 8vh, 55px); /* Further reduced height */
        height: clamp(35px, 8vh, 55px);
        line-height: 0.95; /* Even tighter line height for mobile */
        padding-bottom: 0px; /* No padding after Skyhawk text */
        background: linear-gradient(180deg, #34d19d 0%, #34d19d 45%, #38c1f2 55%, #38c1f2 100%);
        background-size: 100% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: bold;
        animation: gradientShift 4s ease-in-out infinite;
      }
      
      .hero-content p {
        margin-bottom: 1.5rem; /* Further reduced margin */
        margin-top: 0.5rem; /* Reduced top margin */
      }

      @keyframes gradientShift {
        0%, 100% {
          background-position: 0% 0%;
        }
        50% {
          background-position: 0% 100%;
        }
      }

      .hero-content {
        width: 98vw;
        max-width: none;
        padding: clamp(2rem, 6vw, 3rem) clamp(1rem, 4vw, 2rem);
      }

      .hero-inner-card {
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
      }

      .hero-content p {
        font-size: clamp(14px, 4vw, 18px);
        margin-bottom: 1.5rem;
        padding: 0;
      }

      .cta-btn {
        padding: clamp(0.8rem, 3vw, 1.2rem) clamp(1.8rem, 6vw, 2.5rem);
        font-size: clamp(0.9rem, 3.5vw, 1.1rem);
      }

      .menu-toggle {
        display: block;
      }

      .nav-links {
        display: none;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        width: 100%;
        position: absolute;
        top: 60px;
        left: 0;
        padding: 1rem 2rem;
        z-index: 999;
        box-shadow: var(--card-shadow);
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
        background: rgba(52, 209, 157, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 15px;
        border: 1px solid var(--primary-green);
        box-shadow: none;
      }

      .dropdown a {
        animation: slideInUp 0.5s ease forwards;
        animation-delay: calc(0.1s * var(--i));
      }

      .services-section {
        min-height: 100vh; /* Full screen height on mobile */
        padding: 1rem;
      }

      .services-container {
        width: 98vw;
        max-width: none;
        padding: clamp(2rem, 6vw, 3rem) clamp(1rem, 4vw, 2rem);
      }

      .services-wrapper {
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
      }

      .services-wrapper h2 {
        font-size: clamp(1.5rem, 5vw, 2.2rem) !important;
        margin-bottom: clamp(1.5rem, 4vw, 2rem) !important;
      }

      .services {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: clamp(0.8rem, 3vw, 1.2rem);
        padding: 0;
        overflow: visible;
        justify-items: center;
        align-items: start;
        width: 100%;
        margin: 0 auto;
      }

      .card {
        width: clamp(140px, 43vw, 170px);
        height: clamp(180px, 45vw, 220px);
        margin: 0;
      }

      .service-image-frame {
        padding: 8px;
        height: 60%;
      }

      .service-image-frame img {
        width: 85%;
        height: 85%;
        padding: 6px;
      }

      .service-card-info {
        padding: 0.4rem;
        height: 40%;
      }

      .service-card-info h3 {
        font-size: 0.75rem;
        margin: 0 0 3px 0;
        line-height: 1.1;
      }

      .service-card-info .specs {
        font-size: 0.6rem;
        line-height: 1.2;
      }

      .coming-soon {
        font-size: 0.65rem;
        margin-top: 3px;
      }

      .services-card-back {
        padding: 1rem;
      }

      .service-card-content p {
        font-size: 0.7rem;
        margin-bottom: 0.8rem;
        line-height: 1.3;
      }

      .book-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.65rem;
        border-radius: 12px;
      }

      .about-section {
        min-height: 100vh; /* Full screen height on mobile */
        padding: 1rem;
      }

      .about-container {
        width: 98vw;
        max-width: none;
        padding: clamp(2rem, 6vw, 3rem) clamp(1rem, 4vw, 2rem);
      }

      .about-inner-card {
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
      }

      .cta-section {
        min-height: 100vh; /* Full screen height on mobile */
        padding: 1rem;
      }

      .contact-container {
        width: 98vw;
        max-width: none;
        padding: clamp(2rem, 6vw, 3rem) clamp(1rem, 4vw, 2rem);
      }

      .contact-inner-card {
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
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
        padding: 0.8rem;
        font-size: 0.9rem;
      }

      .contact-form textarea {
        min-height: 80px;
      }

      .contact-form button {
        padding: 0.8rem 1.5rem;
        font-size: 0.9rem;
      }
    }

    /* Extra small screens (phones) */
    @media (max-width: 480px) {
      .services-container {
        width: 99vw;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(0.8rem, 3vw, 1.5rem);
      }

      .services-wrapper {
        padding: clamp(1.2rem, 4vw, 2rem) clamp(0.8rem, 3vw, 1.5rem);
      }

      .services-wrapper h2 {
        font-size: clamp(1.3rem, 4.5vw, 2rem) !important;
        margin-bottom: clamp(1.2rem, 3.5vw, 1.8rem) !important;
      }

      .services {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 1rem;
        padding: 0;
        overflow: visible;
        justify-items: center;
        align-items: start;
        width: 100%;
        margin: 0 auto;
      }

      .card {
        width: clamp(110px, 35vw, 140px);
        height: clamp(150px, 38vw, 180px);
        margin: 0;
      }

      .service-image-frame {
        padding: 5px !important;
        height: 62% !important;
      }

      .service-image-frame img {
        width: 88% !important;
        height: 88% !important;
        padding: 2px !important;
      }

      .service-card-info {
        padding: 0.25rem !important;
        height: 38% !important;
      }

      .service-card-info h3 {
        font-size: 0.6rem !important;
      }

      .service-card-info .specs {
        font-size: 0.55rem !important;
      }

      .services-card-back {
        padding: 0.6rem !important;
      }

      .service-card-content p {
        font-size: 0.6rem !important;
        margin-bottom: 0.6rem !important;
        line-height: 1.15 !important;
      }

      .book-btn {
        padding: 0.3rem 0.6rem !important;
        font-size: 0.55rem !important;
        border-radius: 8px !important;
      }
    }

    /* Extra small screens (very small phones) */
    @media (max-width: 360px) {
      .services {
        gap: 0.8rem !important;
      }

      .card {
        width: clamp(100px, 32vw, 120px) !important;
        height: clamp(135px, 35vw, 155px) !important;
      }

      .service-image-frame {
        padding: 4px !important;
        height: 65% !important;
      }

      .service-card-info {
        padding: 0.2rem !important;
        height: 35% !important;
      }

      .service-card-info h3 {
        font-size: 0.55rem !important;
      }

      .service-card-info .specs {
        font-size: 0.5rem !important;
      }

      .services-card-back {
        padding: 0.5rem !important;
      }

      .service-card-content p {
        font-size: 0.55rem !important;
        margin-bottom: 0.5rem !important;
      }

      .book-btn {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.5rem !important;
        border-radius: 6px !important;
      }

      .hero-content {
        width: 99vw;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(0.8rem, 3vw, 1.5rem);
      }

      .hero-inner-card {
        padding: clamp(1.2rem, 4vw, 2rem) clamp(0.8rem, 3vw, 1.5rem);
      }

      .about-container,
      .contact-container {
        width: 99vw;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(0.8rem, 3vw, 1.5rem);
      }

      .about-inner-card,
      .contact-inner-card {
        padding: clamp(1.2rem, 4vw, 2rem) clamp(0.8rem, 3vw, 1.5rem);
      }
    }

    .hero-section {
      height: 100vh; /* Fixed height to exactly 100vh */
      max-height: 100vh; /* Ensure it doesn't exceed 100vh */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: transparent;
      color: var(--text-black);
      padding: 1rem; /* Reduced padding */
      position: relative;
      padding-top: calc(70px + 1rem); /* Reduced top padding */
      box-sizing: border-box;
    }

    .hero-content {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      padding: clamp(1.5rem, 4vw, 2.5rem) clamp(1rem, 3vw, 2rem); /* Reduced padding */
      box-shadow: var(--section-shadow);
      width: clamp(95vw, 90vw, calc(70vw + 20vw));
      max-width: 1200px;
      min-height: 350px; /* Reduced min-height to fit in 100vh */
      position: relative;
      z-index: 2;
      overflow: visible; /* Ensure content is not clipped */
    }

    .hero-content::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      pointer-events: none;
      z-index: -1;
    }

    /* Inner hero card for double glass effect */
    .hero-inner-card {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: clamp(1.5rem, 3vw, 2rem) clamp(1rem, 2vw, 1.5rem); /* Reduced padding */
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
      position: relative;
      z-index: 3;
      transition: all 0.3s ease;
      overflow: visible; /* Ensure content is not clipped */
    }

    .hero-inner-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      pointer-events: none;
      z-index: -1;
    }

    .hero-inner-card:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 6px 20px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
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
      z-index: 1;
    }

    /* === HERO TYPING ANIMATION STYLES === */
    .hero-text-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    #hero-line1, #hero-line2 {
      color: var(--text-black);
      position: relative;
      text-shadow: 2px 2px 4px rgba(255,255,255,0.3);
      z-index: 1;
    }

    .hero-text-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: clamp(140px, 18vh, 200px); /* Reduced height to fit in 100vh */
      margin-bottom: 1rem; /* Reduced margin */
      overflow: visible; /* Ensure text is not clipped */
    }

    #hero-line1 {
      font-size: clamp(24px, 8vw, 64px);
      font-weight: 600;
      min-height: clamp(30px, 6vh, 50px); /* Reduced height */
      height: auto; /* Changed to auto to accommodate text */
      display: block;
      margin-bottom: 0.8rem; /* Reduced gap */
      overflow: visible; /* Ensure text is not clipped */
    }

    #hero-line2 {
      font-size: clamp(32px, 12vw, 120px);
      font-weight: 700;
      min-height: clamp(60px, 12vh, 100px); /* Reduced height while keeping text visible */
      height: auto; /* Changed to auto to accommodate text */
      display: block;
      background: linear-gradient(180deg, #34d19d 0%, #34d19d 45%, #38c1f2 55%, #38c1f2 100%);
      background-size: 100% 200%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 20px rgba(52, 209, 157, 0.3);
      filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
      line-height: 1.1; /* Optimized line height */
      padding-bottom: 8px; /* Reduced padding */
      margin-bottom: 5px; /* Reduced margin */
      animation: gradientShift 4s ease-in-out infinite;
      overflow: visible; /* Ensure text is not clipped */
    }

    /* The blinking cursor is now handled by a simple class */
    /*
    .blinking-cursor {
        position: relative;
    }

    .blinking-cursor::after {
        content: '';
        position: absolute;
        right: -2px;
        top: 50%;
        transform: translateY(-50%);
        width: 2px;
        height: 70%;
        background-color: var(--text-black);
        animation: blink 1s step-end infinite;
        z-index: 10;
    }

    /* Specific cursor styling for hero-line1 */
    /*
    #hero-line1.blinking-cursor::after {
        width: 1.5px;
        right: -1.5px;
        height: 65%;
    }

    /* Special cursor styling for gradient text elements (hero-line2) */
    /*
    #hero-line2.blinking-cursor::after {
        background-color: #2c3e50;
        width: 2px;
        right: -2px;
        height: 70%;
        box-shadow: 0 0 3px rgba(255,255,255,0.8);
    }

    @keyframes blink {
      0%, 50% { opacity: 1; }
      51%, 100% { opacity: 0; }
    }
    */

    @media (max-width: 768px) {
      /*
      .blinking-cursor::after {
        width: 1.5px;
        right: -1.5px;
        height: 65%;
      }
      
      #hero-line1.blinking-cursor::after {
        width: 1px;
        right: -1.5px;
        height: 65%;
      }
      
      #hero-line2.blinking-cursor::after {
        width: 1.5px;
        right: -1.5px;
        height: 65%;
      }
      */
      
      /* Force mobile services layout - override desktop styles */
      .services-wrapper h2 {
        font-size: clamp(1.5rem, 5vw, 2.2rem) !important;
        margin-bottom: clamp(1.5rem, 4vw, 2rem) !important;
      }

      .services {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        grid-template-rows: 1fr 1fr !important;
        gap: 1rem !important;
        padding: 0 !important;
        overflow: visible !important;
        justify-items: center !important;
        align-items: start !important;
        width: 100% !important;
        margin: 0 auto !important;
      }

      .card {
        width: clamp(110px, 35vw, 140px) !important;
        height: clamp(150px, 38vw, 180px) !important;
        margin: 0 !important;
      }

      .service-image-frame {
        padding: 5px !important;
        height: 62% !important;
      }

      .service-image-frame img {
        width: 88% !important;
        height: 88% !important;
        padding: 2px !important;
      }

      .service-card-info {
        padding: 0.25rem !important;
        height: 38% !important;
      }

      .service-card-info h3 {
        font-size: 0.6rem !important;
        margin: 0 0 3px 0 !important;
        line-height: 1.1 !important;
      }

      .service-card-info .specs {
        font-size: 0.55rem !important;
        line-height: 1.2 !important;
      }

      .coming-soon {
        font-size: 0.6rem !important;
        margin-top: 3px !important;
      }

      .services-card-back {
        padding: 0.6rem !important;
      }

      .service-card-content p {
        font-size: 0.6rem !important;
        margin-bottom: 0.6rem !important;
        line-height: 1.15 !important;
      }

      .book-btn {
        padding: 0.3rem 0.6rem !important;
        font-size: 0.55rem !important;
        border-radius: 8px !important;
      }
      
      .hero-content,
      .services-container,
      .about-container,
      .contact-container {
        width: 95vw;
        padding: 1.5rem 1rem;
      }
      
      .hero-inner-card {
        padding: 2rem 1.5rem;
        border-radius: 15px;
      }
      
      .hero-inner-card::before {
        border-radius: 15px;
      }
      
      .contact-content {
        grid-template-columns: 1fr;
      }
    }

    /* Extra small screens adjustments */
    @media (max-width: 480px) {
      .hero-section {
        min-height: 100vh; /* Full screen height for very small screens too */
        padding-top: calc(60px + 1rem); /* Account for navbar */
      }

      .hero-text-wrapper {
        min-height: clamp(70px, 14vh, 100px); /* Even smaller for very small screens */
        margin-bottom: 0.6rem;
      }

      #hero-line1 {
        min-height: clamp(16px, 3.5vh, 25px);
        height: clamp(16px, 3.5vh, 25px);
        margin-bottom: 0.3rem; /* Small gap between lines */
      }

      #hero-line2 {
        min-height: clamp(30px, 7vh, 45px);
        height: clamp(30px, 7vh, 45px);
        line-height: 0.9;
        padding-bottom: 0px; /* No padding after text */
      }
      
      .hero-content p {
        margin-top: 0.3rem; /* Minimal top margin */
        margin-bottom: 1.2rem;
      }
    }

    @keyframes blink {
      50% { border-color: transparent; }
    }


    .hero-content p {
      font-size: clamp(16px, 2.5vw, 22px); /* Reduced font size */
      margin-bottom: 1.5rem; /* Reduced margin */
      animation: fadeInUp 1.5s forwards 0.5s;
      color: var(--text-grey);
      text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
      z-index: 1;
      position: relative;
    }

    .cta-btn,
    .book-btn,
    .contact-form button {
      animation: pulse 1.5s ease-in-out infinite;
    }

    .cta-btn {
      display: inline-block;
      padding: 1.2rem 2.5rem;
      background: var(--primary-green);
      color: var(--white);
      border-radius: 50px;
      text-decoration: none;
      font-weight: 700;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      animation: bounceIn 1s ease forwards 1s;
      box-shadow: var(--button-shadow);
      z-index: 1;
      position: relative;
    }

    .cta-btn:hover {
      background: var(--primary-blue);
      transform: scale(1.05) translateY(-3px);
      box-shadow: var(--hover-shadow);
    }

    .services-section {
      min-height: 100vh; /* Full screen height for services section */
      padding: 2rem;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      box-sizing: border-box;
    }

    .services-container {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      padding: clamp(2rem, 6vw, 4rem) clamp(1rem, 4vw, 4rem); /* Restored original padding */
      box-shadow: var(--section-shadow);
      width: clamp(95vw, 90vw, calc(70vw + 20vw)); /* Restored original width */
      max-width: 1200px; /* Restored original max-width */
      position: relative;
      z-index: 2;
    }

    .services-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      pointer-events: none;
      z-index: -1;
    }

    .services-section h2 {
      text-align: center;
      margin-bottom: 3rem; /* Restored original margin */
      font-size: clamp(2rem, 6vw, 3rem); /* Restored original size */
      color: var(--text-black);
      animation: fadeInDown 1s ease forwards;
      position: relative;
      z-index: 1;
    }

    .services-wrapper {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem); /* Restored original padding */
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
      position: relative;
      transition: all 0.3s ease;
    }

    .services-wrapper::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      pointer-events: none;
      z-index: -1;
    }

    .services-wrapper:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 6px 20px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .services {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: clamp(0.5rem, 0.8vw, 0.8rem); /* Smaller gaps between cards */
      padding: clamp(0.5rem, 1vw, 1rem); /* Reduced padding */
      justify-items: center;
      align-items: start;
    }

    .card {
      background: var(--card-bg);
      backdrop-filter: blur(15px);
      border-radius: 15px; /* Slightly smaller border radius */
      width: clamp(140px, 16vw, 200px); /* Reduced card width */
      height: clamp(220px, 24vw, 300px); /* Reduced card height */
      perspective: 1000px;
      position: relative;
      cursor: pointer;
      border: 1px solid var(--glass-border);
      box-shadow: 
        0 8px 32px rgba(52, 209, 157, 0.2),
        0 4px 16px rgba(52, 209, 157, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
      color: var(--text-black);
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--card-bg);
      border-radius: 20px;
      pointer-events: none;
      z-index: -1;
    }

    .card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 
        0 16px 48px rgba(52, 209, 157, 0.3),
        0 8px 24px rgba(52, 209, 157, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }
    .card .services-card-front, .card .services-card-back {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      border-radius: 15px; /* Match card border radius */
      backface-visibility: hidden;
      transition: transform 1.5s cubic-bezier(0.23, 1, 0.32, 1);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .card .services-card-front {
      background: var(--inner-card-bg);
      backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      z-index: 2;
      transform: rotateY(0deg);
      box-shadow: 
        0 4px 20px rgba(52, 209, 157, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
      justify-content: space-between;
    }

    .card .services-card-front::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: var(--inner-card-bg);
      border-radius: 15px; /* Match card border radius */
      pointer-events: none;
      z-index: -1;
    }

    .card .services-card-back {
      background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: var(--white);
      transform: rotateY(180deg);
      z-index: 3;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 2rem;
      box-shadow: 
        0 8px 32px rgba(52, 209, 157, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .card:hover .services-card-front {
      transform: rotateY(180deg);
    }

    .card:hover .services-card-back {
      transform: rotateY(0deg);
    }

    /* Top image section */
    .service-image-frame {
      height: 62%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(6px, 1vw, 10px); /* Reduced padding */
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border-radius: 15px 15px 0 0; /* Match card border radius */
      border-bottom: 1px solid var(--glass-border);
    }

    .service-image-frame img {
      width: 70%; /* Reduced image size */
      height: 70%; /* Reduced image size */
      object-fit: cover;
      border-radius: 10px; /* Smaller border radius */
      border: 2px solid var(--primary-green);
      background: rgba(52, 209, 157, 0.05);
      padding: clamp(4px, 1vw, 6px); /* Reduced padding */
      transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
      box-shadow: 0 4px 20px rgba(52, 209, 157, 0.2);
    }

    .card:hover .service-image-frame img {
      transform: scale(1.08) rotate(2deg);
      border-color: var(--primary-blue);
      box-shadow: 0 8px 30px rgba(56, 193, 242, 0.3);
    }

    /* Bottom info section */
    .service-card-info {
      height: 38%;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(15px);
      border-radius: 0 0 15px 15px; /* Match card border radius */
      padding: clamp(0.4rem, 1vw, 0.6rem); /* Reduced padding */
      display: flex;
      flex-direction: column;
      justify-content: center;
      border-top: 1px solid var(--glass-border);
    }
    
    .service-card-info h3 {
      margin: 0 0 clamp(3px, 0.6vw, 4px) 0; /* Reduced margin */
      font-size: clamp(0.7rem, 1.2vw, 0.85rem); /* Reduced font size */
      font-weight: bold;
      color: var(--primary-green);
      text-align: center;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.1);
      line-height: 1.1;
    }

    .coming-soon {
      font-size: clamp(0.6rem, 1vw, 0.75rem); /* Reduced font size */
      color: var(--primary-blue);
      text-align: center;
      animation: pulse-glow 2s ease-in-out infinite;
      margin-top: clamp(1px, 0.3vw, 2px); /* Reduced margin */
      font-weight: bold;
    }

    @keyframes pulse-glow {
      0%, 100% {
        opacity: 1;
        text-shadow: 0 0 5px var(--primary-blue);
      }
      50% {
        opacity: 0.7;
        text-shadow: 0 0 15px var(--primary-blue), 0 0 25px var(--primary-blue);
      }
    }

    .service-card-info .specs {
      color: var(--text-grey);
      font-size: clamp(0.55rem, 1vw, 0.65rem); /* Reduced font size */
      line-height: 1.1; /* Tighter line height */
      text-align: left;
    }

    .service-card-info .specs strong {
      color: var(--text-black);
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



    .service-card-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
    }
    
    .service-card-content p {
      margin-bottom: clamp(1rem, 2vw, 1.5rem);
      color: var(--white);
      font-size: clamp(0.9rem, 1.6vw, 1.1rem);
      line-height: 1.5;
    }

    .book-btn {
      display: inline-block;
      margin-top: clamp(0.8rem, 1.5vw, 1rem);
      padding: clamp(0.6rem, 1.2vw, 0.75rem) clamp(1.2rem, 2.2vw, 1.5rem);
      background-color: var(--primary-green);
      color: var(--white);
      border-radius: 30px;
      font-weight: 600;
      font-size: clamp(0.8rem, 1.4vw, 1rem);
      text-decoration: none;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }

    .book-btn:hover {
      background-color: var(--primary-blue);
      transform: scale(1.1) translateY(-3px);
    }

    /* Alternating button colors for different services */
    .card:nth-child(even) .book-btn {
      background: var(--primary-blue);
    }

    .card:nth-child(even) .book-btn:hover {
      background: var(--primary-green);
    }

    .about-section {
      min-height: 100vh; /* Full screen height for about section */
      padding: 2rem;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      box-sizing: border-box;
    }

    .about-container {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      padding: clamp(2rem, 6vw, 4rem) clamp(1rem, 4vw, 4rem);
      box-shadow: var(--section-shadow);
      width: clamp(95vw, 90vw, calc(70vw + 20vw));
      max-width: 1200px;
      position: relative;
      z-index: 2;
      color: var(--text-black);
    }

    .about-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      pointer-events: none;
      z-index: -1;
    }

    .about-inner-card {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
      position: relative;
      transition: all 0.3s ease;
    }

    .about-inner-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      pointer-events: none;
      z-index: -1;
    }

    .about-inner-card:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 6px 20px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .about-inner-card h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--text-black);
      font-size: clamp(2rem, 6vw, 3rem);
      animation: fadeInDown 1s ease forwards;
    }

    .about-inner-card p {
      font-size: clamp(1rem, 3vw, 1.2rem);
      line-height: 1.6;
      text-align: center;
      max-width: 100%;
      margin: 0 auto;
      animation: fadeInUp 1s ease forwards 0.3s;
    }

    .cta-section {
      min-height: 100vh; /* Full screen height for contact section */
      padding: 2rem;
      text-align: center;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      box-sizing: border-box;
    }

    .contact-container {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      padding: clamp(2rem, 6vw, 4rem) clamp(1rem, 4vw, 4rem);
      box-shadow: var(--section-shadow);
      width: clamp(95vw, 90vw, calc(70vw + 20vw));
      max-width: 1200px;
      position: relative;
      z-index: 2;
      color: var(--text-black);
    }

    .contact-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 25px;
      pointer-events: none;
      z-index: -1;
    }

    .contact-inner-card {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
      position: relative;
      transition: all 0.3s ease;
    }

    .contact-inner-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      pointer-events: none;
      z-index: -1;
    }

    .contact-inner-card:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 6px 20px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .contact-content {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem; /* Reduced gap */
      align-items: start; /* Changed from center to start */
      min-height: 400px; /* Reduced min-height */
    }

    .contact-inner-card h2 {
      margin-bottom: 1rem; /* Reduced margin */
      animation: fadeInDown 1s ease forwards;
      color: var(--text-black);
      font-size: clamp(1.8rem, 5vw, 2.8rem);
      position: relative;
      z-index: 1;
      text-align: center; /* Center the main heading */
      grid-column: 1 / -1; /* Span full width */
      order: -1; /* Move to top */
    }

    .contact-subtitle {
      font-size: clamp(1rem, 3vw, 1.3rem);
      color: var(--text-grey);
      line-height: 1.6;
      margin-bottom: 1.5rem; /* Reduced margin */
      font-weight: 500;
      text-align: center; /* Center the subtitle */
      grid-column: 1 / -1; /* Span full width */
      order: 0; /* After heading */
    }

    .contact-form-wrapper {
      order: 1;
    }

    .contact-info {
      order: 2;
    }

    /* Hide "Request a Free Quote" section on large screens */
    .contact-cta {
      display: none;
    }

    @media (min-width: 992px) {
      .contact-content {
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
      }
      
      .contact-inner-card h2 {
        grid-column: 1 / -1; /* Span both columns */
        white-space: nowrap; /* Keep in single line */
        font-size: clamp(2.2rem, 4vw, 3.2rem); /* Larger on desktop */
      }
      
      .contact-subtitle {
        grid-column: 1 / -1; /* Span both columns */
        margin-bottom: 2rem;
      }
      
      .contact-form-wrapper {
        order: 1;
        grid-column: 1;
      }
      
      .contact-info {
        order: 2;
        grid-column: 2;
        display: flex;
        align-items: flex-start; /* Changed to flex-start */
        justify-content: center;
        padding-top: 1rem; /* Added top padding */
      }

      /* Keep only email and location items on desktop */
      .contact-details-modern {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
      }
      
      /* Hide the "Request a Free Quote" on desktop */
      .contact-cta {
        display: none !important;
      }
    }

    @media (max-width: 991px) {
      .contact-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 1.5rem; /* Reduced gap for mobile */
      }
      
      .contact-inner-card h2 {
        margin-bottom: 0.8rem; /* Reduced margin for mobile */
        font-size: clamp(1.6rem, 6vw, 2.2rem); /* Adjusted for mobile */
      }
      
      .contact-subtitle {
        margin-bottom: 1.2rem; /* Reduced margin for mobile */
      }
      
      .contact-form-wrapper {
        order: 2;
      }
      
      .contact-info {
        order: 1;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      /* Show "Request a Free Quote" on mobile */
      .contact-cta {
        display: block;
        margin-top: 1rem;
      }
    }

    .contact-info {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .contact-hero-content {
      text-align: left;
    }

    .contact-hero-content h2 {
      font-size: clamp(1.8rem, 5vw, 2.8rem);
      font-weight: 700;
      color: var(--text-black);
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1.2;
    }

    .contact-subtitle {
      font-size: clamp(1rem, 3vw, 1.3rem);
      color: var(--text-grey);
      line-height: 1.6;
      margin-bottom: 2rem;
      font-weight: 500;
    }

    .contact-details-modern {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .contact-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(52, 209, 157, 0.2);
      background: rgba(255, 255, 255, 0.2);
    }

    .contact-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
      border-radius: 50%;
      color: white;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .contact-text {
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .contact-label {
      font-size: clamp(0.9rem, 2.5vw, 1.1rem);
      font-weight: 600;
      color: var(--text-black);
    }

    .contact-value {
      font-size: clamp(0.85rem, 2.5vw, 1rem);
      color: var(--text-grey);
      font-weight: 500;
    }

    .contact-cta {
      margin-top: 1rem;
    }

    .cta-highlight {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1.5rem;
      background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
      border-radius: 20px;
      color: white;
      font-weight: 700;
      font-size: clamp(1rem, 3vw, 1.2rem);
      text-align: center;
      box-shadow: 0 8px 25px rgba(52, 209, 157, 0.3);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .cta-highlight:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 12px 35px rgba(52, 209, 157, 0.4);
    }

    .cta-highlight i {
      font-size: 1.5rem;
      animation: pulse 2s ease-in-out infinite;
    }

    .cta-text {
      flex: 1;
    }

    @media (max-width: 991px) {
      .contact-hero-content {
        text-align: center;
      }
      
      .contact-details-modern {
        align-items: center;
      }
      
      .contact-item {
        max-width: 400px;
        width: 100%;
      }
      
      .cta-highlight {
        max-width: 300px;
        width: 100%;
        justify-content: center;
      }
    }





    .contact-inner-card p {
      animation: fadeInUp 1s ease forwards 0.3s;
      color: var(--text-black);
      position: relative;
      z-index: 1;
      font-size: clamp(0.9rem, 3vw, 1.1rem);
    }

    .contact-info {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      font-size: 1.2rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
      color: var(--white);
      position: relative;
      z-index: 1;
    }



    .contact-form {
      max-width: 100%;
      margin: 0;
      text-align: left;
      position: relative;
      z-index: 1;
    }

    .contact-form label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold; 
      color: var(--text-black);
      font-size: clamp(0.9rem, 3vw, 1rem);
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: clamp(0.8rem, 3vw, 1rem);
      margin-bottom: 1rem;
      border: 2px solid rgba(102,102,102,0.3);
      border-radius: 15px;
      background: rgba(255,255,255,0.9);
      color: var(--text-black);
      font-size: clamp(0.9rem, 3vw, 1rem);
      resize: vertical;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: var(--primary-green);
      background: var(--white);
      outline: none;
      transform: translateY(-2px);
      box-shadow: var(--button-shadow);
    }

    .contact-form input::placeholder,
    .contact-form textarea::placeholder {
      color: var(--text-grey);
      opacity: 0.8;
    }

    .contact-form textarea {
      min-height: 100px;
    }

    .contact-form button {
      background: var(--white);
      color: var(--text-black);
      padding: clamp(0.8rem, 3vw, 1rem) clamp(1.5rem, 5vw, 2rem);
      border: none;
      border-radius: 25px;
      font-weight: 700;
      font-size: clamp(1rem, 3vw, 1.1rem);
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: var(--button-shadow);
      width: 100%;
      max-width: 200px;
    }

    .contact-form button:hover {
      background: var(--light-grey);
      transform: scale(1.05) translateY(-3px);
      box-shadow: var(--hover-shadow);
    }

    .footer {
      text-align: center;
      padding: 2rem 0;
      background: var(--text-black);
      color: var(--light-grey);
    }

    .footer p {
      animation: fadeInUp 1s ease forwards;
    }
    
    .contact-section-label{
      font-weight: bold;
      color: var(--text-black);
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

    /* Coming Soon Modal Styles */
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
      background: var(--white);
      padding: 2rem;
      border-radius: 25px;
      max-width: 400px;
      width: 90%;
      color: var(--text-black);
      position: relative;
      border: 2px solid var(--primary-green);
      text-align: center;
      box-shadow: var(--card-shadow);
    }

    .coming-soon-content h2 {
      margin-bottom: 1rem;
      color: var(--primary-green);
      animation: fadeInDown 1s ease forwards;
    }

    .coming-soon-content p {
      margin-bottom: 1.5rem;
      animation: fadeInUp 1s ease forwards 0.3s;
    }

    .coming-soon-content button {
      background: var(--primary-blue);
      color: var(--white);
      padding: 0.9rem 2rem;
      border: none;
      border-radius: 25px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: var(--button-shadow);
    }

    .coming-soon-content button:hover {
      background: var(--primary-green);
      transform: scale(1.05) translateY(-3px);
      box-shadow: var(--hover-shadow);
    }

    .close-coming-soon {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.5rem;
      color: var(--text-light);
      cursor: pointer;
      animation: bounceIn 1s ease forwards;
    }

    .close-coming-soon:hover {
      color: var(--primary-green);
      transform: scale(1.2);
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
          My Account <i class="fas fa-chevron-down"></i>
        </div>
        <div class="dropdown" id="accountDropdown">
          <a href="user/index.php" style="--i: 1;" class="nav-link">user</a>
          <a href="pilot/index.php" style="--i: 2;" class="nav-link">pilot</a>
          <a href="admin/index.php" style="--i: 3;" class="nav-link">admin</a>
        </div>
      </div>
    </div>
  </nav>

  <header class="hero-section" data-aos="fade-right" data-aos-delay="200">
    <div class="hero-content">
      <div class="hero-inner-card">
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
        <p>"Experience precision, reliability, and speed with Skyhawk's advanced drone services.<br>From aerial photography to surveying, we deliver top-notch solutions for all your needs."</p>
        <a href="#services" class="cta-btn">Explore Services</a>
      </div>
    </div>
  </header>

  <section id="services" class="services-section" data-aos="fade-right" data-aos-delay="300">
    <div class="services-container">
      <div class="services-wrapper">
        <h2>Our Drone Services</h2>
        <div class="services">
          <div class="card" data-aos="fade-right" data-aos-delay="400">
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
                <a href="user/index.php" class="book-btn">Book Now</a>
              </div>
            </div>
          </div>
          <div class="card" data-aos="fade-right" data-aos-delay="500">
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
                <a href="user/index.php" class="book-btn">Book Now</a>
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
      </div>
    </div>
  </section>

  <section class="about-section" id="about" data-aos="fade-right" data-aos-delay="300">
    <div class="about-container">
      <div class="about-inner-card">
        <h2>About Us</h2>
        <p>
          At Skyhawk, we believe that advanced drone technology should be accessible to everyone. Our mission is to empower individuals and communities by offering versatile aerial solutions that cater to a wide range of needs—from capturing breathtaking moments and providing detailed surveys to enhancing security and supporting innovative projects. With our state-of-the-art drones and a team of dedicated experts, we ensure that every customer receives personalized service, enabling you to leverage the power of aerial perspectives for any purpose you envision. Join us as we redefine what's possible with drone technology for all.
        </p>
      </div>
    </div>
  </section>

  <section class="cta-section" id="contact" data-aos="fade-right" data-aos-delay="300">
    <div class="contact-container">
      <div class="contact-inner-card">
        <div class="contact-content">
          <h2 data-aos="fade-left" data-aos-delay="200">🚀 Let's Elevate Your Project</h2>
          <p class="contact-subtitle" data-aos="fade-left" data-aos-delay="300">
            Our expert drone services are tailored to bring your vision to life. Whether you need aerial photography, surveying, or custom solutions — we're here to help.
          </p>
          <div class="contact-form-wrapper">
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
          </div>
          <div class="contact-info">
            <div class="contact-hero-content">
              <div class="contact-details-modern">
                <div class="contact-item" data-aos="fade-left" data-aos-delay="400">
                  <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <div class="contact-text">
                    <span class="contact-label">📩 Email Us:</span>
                    <span class="contact-value">skyhawkservice@gmail.com</span>
                  </div>
                </div>
                
                <div class="contact-item" data-aos="fade-left" data-aos-delay="500">
                  <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="contact-text">
                    <span class="contact-label">📍 Available Nationwide:</span>
                    <span class="contact-value">Multiple Locations</span>
                  </div>
                </div>
                
                <div class="contact-cta" data-aos="fade-left" data-aos-delay="600">
                  <div class="cta-highlight">
                    <i class="fas fa-check-circle"></i>
                    <span class="cta-text">✅ Request a Free Quote</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 Skyhawk. All rights reserved.</p>
  </footer>

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

    function openComingSoonModal() {
      const modal = document.getElementById("comingSoonModal");
      if (modal) {
        modal.style.display = "flex";
      }
    }

    function closeComingSoonModal() {
      const modal = document.getElementById("comingSoonModal");
      if (modal) {
        modal.style.display = "none";
      }
    }

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
      
      // === START: NEW INFINITE LOOP TYPING ANIMATION LOGIC (FLIP ANIMATION COMMENTED OUT) ===
      // const flipper = document.querySelector('.hero-flipper');
      const titleContainer = document.querySelector('.hero-title-container');
      const line1 = document.getElementById('hero-line1');
      const line2 = document.getElementById('hero-line2');
      
      let isAnimating = false; // Flag to control the animation loop

      // Helper function for delays
      const delay = ms => new Promise(res => setTimeout(res, ms));

      // Type out a string character by character
      async function type(element, text, speed = 200) {
        element.classList.add('blinking-cursor');
        for (const char of text) {
          if (!isAnimating) return; // Exit if animation was stopped
          element.textContent += char;
          await delay(speed);
        }
        element.classList.remove('blinking-cursor');
      }

      // Delete text character by character
      async function deleteText(element, speed = 200) {
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
          
          // Show cursor on line1 during small pause
          line1.classList.add('blinking-cursor');
          await delay(300); // Small pause between lines
          line1.classList.remove('blinking-cursor');
          if (!isAnimating) break;
          
          await type(line2, "Skyhawk");
          if (!isAnimating) break;
          
          // Show cursor on line2 during the pause
          line2.classList.add('blinking-cursor');
          await delay(1500); // Longer pause when text is fully displayed
          line2.classList.remove('blinking-cursor');
          if (!isAnimating) break;

          // Deleting phase
          await deleteText(line2);
          if (!isAnimating) break;
          await deleteText(line1);
          if (!isAnimating) break;

          await delay(500); // Pause before restarting
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
      });
    });

    // Contact form clearing functionality
    document.addEventListener('DOMContentLoaded', function() {
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
    });

    // Clear form when navigating back from WhatsApp
    window.addEventListener('pageshow', function(event) {
        const contactForm = document.querySelector('.contact-form');
        if (contactForm && (event.persisted || document.referrer.includes('wa.me'))) {
            contactForm.reset();
            localStorage.removeItem('contactFormData');
            sessionStorage.removeItem('contactFormData');
        }
    });
  </script>
</body>
</html>