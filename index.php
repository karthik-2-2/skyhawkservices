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
      --card-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      --button-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      --hover-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
      --section-bg: rgba(255, 255, 255, 0.95);
      --section-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
        padding: 1rem;
        height: auto;
        min-height: 70vh;
        margin-top: 60px;
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

      .cta-section {
        padding: 2rem 1rem;
      }

      .services-section {
        padding: 3rem 1rem;
      }

      .about-section {
        padding: 3rem 1rem;
        margin: 0 1rem 2rem 1rem;
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

    .hero-section {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: transparent;
      color: var(--text-black);
      padding: 2rem;
      position: relative;
      margin-top: 70px;
    }

    .hero-content {
      background: var(--section-bg);
      backdrop-filter: blur(10px);
      border-radius: 25px;
      padding: 3rem 2rem;
      box-shadow: var(--section-shadow);
      max-width: 900px;
      width: 90%;
      position: relative;
      z-index: 2;
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

    #hero-line1 {
      font-size: clamp(24px, 8vw, 64px);
      font-weight: 600;
      min-height: 48px;
    }

    #hero-line2 {
      font-size: clamp(48px, 14vw, 120px);
      font-weight: 700;
      min-height: 96px;
      background: linear-gradient(45deg, var(--primary-green), var(--primary-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 20px rgba(52, 209, 157, 0.3);
      filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
    }
    }

    /* The blinking cursor is now handled by a simple class */
    .blinking-cursor {
        border-right: 2px solid var(--text-black);
        animation: blink 1s step-end infinite;
    }

    @media (max-width: 768px) {
      .blinking-cursor {
        border-right: 1px solid var(--text-black);
      }
    }

    @keyframes blink {
      50% { border-color: transparent; }
    }


    .hero-content p {
      font-size: clamp(16px, 2.5vw, 24px);
      margin-bottom: 2.5rem;
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
      padding: 5rem 2rem;
      position: relative;
    }

    .services-container {
      background: var(--section-bg);
      backdrop-filter: blur(10px);
      border-radius: 25px;
      padding: 3rem 2rem;
      box-shadow: var(--section-shadow);
      max-width: 1200px;
      margin: 0 auto;
    }

    .services-section h2 {
      text-align: center;
      margin-bottom: 3rem;
      font-size: 3rem;
      color: var(--text-black);
      animation: fadeInDown 1s ease forwards;
      position: relative;
      z-index: 1;
    }

    .services {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: center;
      perspective: 3000px;
    }

    .card {
      background: var(--white);
      border-radius: 25px;
      width: 300px;
      height: 460px;
      perspective: 1000px;
      position: relative;
      display: inline-block;
      margin: 1rem;
      cursor: pointer;
      border: none;
      box-shadow: var(--card-shadow);
      vertical-align: top;
      color: var(--text-black);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: var(--hover-shadow);
    }
    }
    
    .card .services-card-front, .card .services-card-back {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      border-radius: 25px;
      backface-visibility: hidden;
      transition: transform 1.5s cubic-bezier(0.23, 1, 0.32, 1);
      display: flex;
      flex-direction: column;
      overflow: hidden;
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
      background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
      color: var(--white);
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
      height: 65%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: var(--light-grey);
      border-radius: 25px 25px 0 0;
    }

    .service-image-frame img {
      width: 80%;
      height: 80%;
      object-fit: cover;
      border-radius: 20px;
      border: none;
      background: rgba(255,255,255,0.8);
      padding: 10px;
      transition: transform 0.3s ease;
    }

    .card:hover .service-image-frame img {
      transform: scale(1.05);
    }

    /* MODIFIED: Bottom info section */
    .service-card-info {
      height: 35%;
      background: var(--white);
      border-radius: 0 0 25px 25px;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .service-card-info h3 {
      margin: 0 0 10px 0;
      font-size: 1.3rem;
      font-weight: bold;
      color: var(--text-black);
      text-align: center;
    }

    .coming-soon {
      font-size: 0.95rem;
      color: var(--primary-blue);
      text-align: center;
      animation: pulse-glow 2s ease-in-out infinite;
      margin-top: 5px;
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
      font-size: 0.9rem;
      line-height: 1.6;
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
    
    .service-card-content p {
      margin-bottom: 1.5rem;
      color: var(--white);
    }
    
    /* Removed old service-card-info h3 styles - already defined above */

    .book-btn {
      display: inline-block;
      margin-top: 1rem;
      padding: 0.9rem 2rem;
      background: var(--primary-green);
      color: var(--white);
      border-radius: 30px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      box-shadow: var(--button-shadow);
    }

    .book-btn:hover {
      background: var(--primary-blue);
      transform: scale(1.05) translateY(-3px);
      box-shadow: var(--hover-shadow);
    }

    /* Alternating button colors for different services */
    .card:nth-child(even) .book-btn {
      background: var(--primary-blue);
    }

    .card:nth-child(even) .book-btn:hover {
      background: var(--primary-green);
    }

    .about-section {
      padding: 5rem 2rem;
      margin: 0 auto 4rem auto;
    }

    .about-container {
      background: var(--section-bg);
      backdrop-filter: blur(10px);
      padding: 3rem 2rem;
      max-width: 1000px;
      margin: 0 auto;
      border-radius: 25px;
      color: var(--text-black);
      box-shadow: var(--section-shadow);
    }

    .about-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--text-black);
      font-size: 3rem;
      animation: fadeInDown 1s ease forwards;
    }

    .about-container p {
      font-size: 1.2rem;
      line-height: 1.6;
      text-align: center;
      max-width: 700px;
      margin: 0 auto;
      animation: fadeInUp 1s ease forwards 0.3s;
    }

    .cta-section {
      padding: 5rem 2rem;
      text-align: center;
      position: relative;
    }

    .contact-container {
      background: var(--section-bg);
      backdrop-filter: blur(10px);
      border-radius: 25px;
      padding: 3rem 2rem;
      box-shadow: var(--section-shadow);
      max-width: 800px;
      margin: 0 auto;
      color: var(--text-black);
    }

    .contact-container h2 {
      margin-bottom: 1.5rem;
      animation: fadeInDown 1s ease forwards;
      color: var(--text-black);
      font-size: 2.5rem;
      position: relative;
      z-index: 1;
    }

    .contact-container p {
      animation: fadeInUp 1s ease forwards 0.3s;
      color: var(--text-black);
      position: relative;
      z-index: 1;
    }
      z-index: 1;
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

    .contact-info p {
      margin: 0;
      animation: slideInLeft 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .contact-info i {
      margin-right: 0.5rem;
      color: var(--white);
      animation: bounceIn 1s ease forwards;
      animation-delay: calc(0.2s * var(--i));
    }

    .contact-form {
      max-width: 600px;
      margin: 2rem auto 0 auto;
      text-align: left;
      position: relative;
      z-index: 1;
    }

    .contact-form label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold; 
      color: var(--text-black);
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 1rem;
      margin-bottom: 1rem;
      border: 2px solid rgba(102,102,102,0.3);
      border-radius: 15px;
      background: rgba(255,255,255,0.9);
      color: var(--text-black);
      font-size: 1rem;
      resize: vertical;
      transition: all 0.3s ease;
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
      padding: 1rem 2rem;
      border: none;
      border-radius: 25px;
      font-weight: 700;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: var(--button-shadow);
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
  </header>

  <section id="services" class="services-section" data-aos="fade-right" data-aos-delay="300">
    <div class="services-container">
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
  </section>

  <section class="about-section" id="about" data-aos="fade-right" data-aos-delay="300">
    <div class="about-container">
      <h2>About Us</h2>
      <p>
        At Skyhawk, we believe that advanced drone technology should be accessible to everyone. Our mission is to empower individuals and communities by offering versatile aerial solutions that cater to a wide range of needs—from capturing breathtaking moments and providing detailed surveys to enhancing security and supporting innovative projects. With our state-of-the-art drones and a team of dedicated experts, we ensure that every customer receives personalized service, enabling you to leverage the power of aerial perspectives for any purpose you envision. Join us as we redefine what's possible with drone technology for all.
      </p>
    </div>
  </section>

  <section class="cta-section" id="contact" data-aos="fade-right" data-aos-delay="300">
    <div class="contact-container">
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