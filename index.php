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


    /* ============================================
       6. EXTRA SMALL SCREENS (@media max-width: 360px)
       ============================================ */


    /* ============================================
       DESKTOP HERO SECTION (@media min-width: 769px)
       ============================================ */
 /* End of Desktop Hero Media Query */

    /* ============================================
       SERVICES SECTION (All Screens)
       ============================================ */
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
      border-radius: 15px;
      width: clamp(140px, 16vw, 200px);
      height: clamp(220px, 24vw, 300px);
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
      transform-style: preserve-3d; /* Fix flickering */
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
      border-radius: 15px;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden; /* Fix Safari flickering */
      transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transform-style: preserve-3d; /* Fix flickering */
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
      padding: clamp(6px, 1vw, 10px);
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border-radius: 15px 15px 0 0;
      border-bottom: 1px solid var(--glass-border);
    }

    .service-image-frame img {
      width: 70%;
      height: 70%;
      object-fit: cover;
      border-radius: 10px;
      border: 2px solid var(--primary-green);
      background: rgba(52, 209, 157, 0.05);
      padding: clamp(4px, 1vw, 6px);
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
      font-size: clamp(0.6rem, 1vw, 0.75rem);
      color: var(--primary-blue);
      text-align: center;
      animation: pulse-glow 2s ease-in-out infinite;
      margin-top: clamp(1px, 0.3vw, 2px);
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
      font-size: clamp(0.85rem, 1.4vw, 1.1rem); /* More responsive range */
      line-height: 1.6;
      font-weight: 500;
    }

    .book-btn {
      display: inline-block;
      margin-top: clamp(0.8rem, 1.5vw, 1rem);
      padding: clamp(0.6rem, 1.2vw, 0.8rem) clamp(1.2rem, 2.2vw, 1.8rem); /* Better responsive padding */
      background-color: var(--primary-green);
      color: var(--white);
      border-radius: 30px;
      font-weight: 600;
      font-size: clamp(0.75rem, 1.3vw, 1rem); /* More responsive font-size */
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
      padding: clamp(1.5rem, 4vw, 2.5rem) clamp(1rem, 3vw, 2rem); /* Same as hero content */
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
      padding: clamp(2rem, 4vw, 3rem) clamp(1.5rem, 3vw, 2.5rem); /* Same as hero inner card */
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
      padding: clamp(1.5rem, 4vw, 2.5rem) clamp(1rem, 3vw, 2rem); /* Same as hero content */
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
      padding: clamp(2rem, 4vw, 3rem) clamp(1.5rem, 3vw, 2.5rem); /* Same as hero inner card */
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

    @media (min-width: 769px) {
      /* --- Hero Section (Desktop) --- */
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
        height: 560px; /* Adjusted height to accommodate inner card */
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
      padding: clamp(1.2rem, 2.5vw, 1.8rem) clamp(1.5rem, 3vw, 2rem); /* Adjusted padding */
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
      position: relative;
      z-index: 3;
      transition: all 0.3s ease;
      overflow: visible; /* Changed to visible so text doesn't get cut */
      min-height: 460px; /* Adjusted to accommodate text wrapper */
      max-height: 460px; /* Adjusted fixed maximum height */
      height: 460px; /* Adjusted fixed height */
      display: flex;
      flex-direction: column;
      justify-content: space-between; /* Space between elements */
      align-items: center; /* Center align all content */
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
      height: clamp(210px, 31vh, 270px); /* Adjusted to fit new line2 height */
      min-height: clamp(210px, 31vh, 270px);
      margin-top: 0; /* Removed negative margin */
      margin-bottom: 1rem; /* Reduced bottom margin */
      overflow: visible; /* Ensure text is not clipped */
      width: 100%; /* Ensure full width */
      flex-shrink: 0; /* Don't shrink */
    }

    #hero-line1 {
      font-size: clamp(24px, 8vw, 64px);
      font-weight: 600;
      height: clamp(30px, 6vh, 64px); /* Fixed height matches font-size */
      min-height: clamp(30px, 6vh, 64px);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem; /* Gap between lines */
      overflow: visible; /* Ensure text is not clipped */
      flex-shrink: 0; /* Don't shrink */
    }

    #hero-line2 {
      font-size: clamp(32px, 12vw, 120px);
      font-weight: 700;
      height: clamp(48px, 14vh, 150px); /* Slightly increased from 120px to 150px for text rendering */
      min-height: clamp(48px, 14vh, 150px);
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(180deg, #2eb589 0%, #2eb589 45%, #2ba5d9 55%, #2ba5d9 100%);
      background-size: 100% 200%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
      line-height: 1.25; /* Slightly increased for proper rendering */
      margin-bottom: 0; /* No bottom margin */
      animation: gradientShift 4s ease-in-out infinite;
      overflow: visible; /* Ensure text is not clipped */
      flex-shrink: 0; /* Don't shrink */
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

    @keyframes blink {
      50% { border-color: transparent; }
    }


    .hero-content p {
      font-size: clamp(1.1rem, 3.5vw, 1.4rem); /* Increased from 1rem to 1.1rem min, 1.2rem to 1.4rem max */
      margin-bottom: 0.8rem; /* Reduced margin */
      animation: fadeInUp 1.5s forwards 0.5s;
      color: var(--text-grey);
      text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
      z-index: 1;
      position: relative;
      height: auto; /* Auto height instead of fixed */
      max-height: none; /* Remove max height restriction */
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      width: 100%; /* Ensure full width */
      padding: 0 1rem; /* Add horizontal padding */
      line-height: 1.6; /* Match About section line height */
      flex-shrink: 0; /* Don't shrink */
    }

    .cta-btn,
    .book-btn,
    .contact-form button {
      animation: pulse 1.5s ease-in-out infinite;
    }

    .cta-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.8rem 2rem;
      background: var(--primary-green);
      color: var(--white);
      border-radius: 50px;
      text-decoration: none;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1); /* Smooth transition for all properties */
      animation: bounceIn 1s ease forwards 1s, pulse 2s ease-in-out infinite;
      box-shadow: 0 8px 25px rgba(52, 209, 157, 0.4), 0 4px 12px rgba(52, 209, 157, 0.3);
      z-index: 1;
      position: relative;
      height: 45px;
      width: 180px;
      margin: 0 auto;
      align-self: center;
      flex-shrink: 0;
    }

    .cta-btn:hover {
      background: var(--primary-blue);
      transform: scale(1.05) translateY(-3px);
      box-shadow: 0 12px 40px rgba(56, 193, 242, 0.4), 0 6px 20px rgba(56, 193, 242, 0.3);
    }
  } /* End of @media (min-width: 769px) */

  /* === MEDIUM SCREENS (769px - 991px) === */
  @media (min-width: 769px) and (max-width: 991px) {
    /* Fix hero heading overflow for medium screens */
    #hero-line1 {
      font-size: clamp(20px, 4vw, 32px) !important;
    }

    #hero-line2 {
      font-size: clamp(28px, 6vw, 56px) !important;
      height: clamp(40px, 8vh, 70px) !important;
      min-height: clamp(40px, 8vh, 70px) !important;
    }

    .hero-text-wrapper {
      height: clamp(150px, 20vh, 180px) !important;
      min-height: clamp(150px, 20vh, 180px) !important;
    }

    /* Two-column layout for contact section */
    .contact-content {
      grid-template-columns: 1fr 1fr !important;
      gap: 2.5rem !important;
      text-align: left !important;
    }
    
    .contact-inner-card h2 {
      grid-column: 1 / -1 !important;
      text-align: center !important;
      font-size: clamp(2rem, 4.5vw, 2.8rem) !important;
      margin-bottom: 1.5rem !important;
    }
    
    /* Hide the top subtitle */
    .contact-subtitle {
      display: none !important;
    }
    
    .contact-form-wrapper {
      order: 1 !important;
      grid-column: 1 !important;
    }
    
    .contact-info {
      order: 2 !important;
      grid-column: 2 !important;
      display: flex !important;
      align-items: flex-start !important;
      justify-content: flex-start !important;
      text-align: left !important;
    }

    /* Add subtitle text as pseudo-element in right column */
    .contact-hero-content::before {
      content: 'Our expert drone services are tailored to bring your vision to life. Whether you need aerial photography, surveying, or custom solutions — we're here to help.';
      display: block;
      font-size: clamp(0.95rem, 2vw, 1.15rem);
      color: var(--text-grey);
      line-height: 1.6;
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    .contact-details-modern {
      align-items: flex-start !important;
    }
    
    .contact-item {
      max-width: 100% !important;
      width: 100% !important;
    }
    
    /* Hide "Request a Free Quote" */
    .contact-cta {
      display: none !important;
    }
  }

  @media (max-width: 768px) {
    /* Contact section single column for mobile */
    .contact-content {
      grid-template-columns: 1fr;
      text-align: center;
      gap: 1.5rem;
    }
    
    .contact-inner-card h2 {
      margin-bottom: 0.8rem;
      font-size: clamp(1.6rem, 6vw, 2.2rem);
    }
    
    .contact-subtitle {
      margin-bottom: 1.2rem;
      display: block !important; /* Show on mobile */
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

    .contact-hero-content {
      text-align: center;
    }

    /* Remove the pseudo-element on mobile */
    .contact-hero-content::before {
      display: none !important;
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

    /* Mobile styles continue */
    .navbar {
      padding: 1rem;
      height: 60px;
    }

      .hero-section {
        height: 100vh;
        max-height: 100vh;
        padding: 1rem;
        padding-top: calc(60px + 1rem);
        /* Center content vertically and horizontally */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }
      
      .hero-content {
        width: 98vw;
        max-width: none;
        height: auto !important;
        padding: clamp(2rem, 6vw, 3rem) clamp(1rem, 4vw, 2rem);
        /* Glass morphism effect - outer card */
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        position: relative;
        z-index: 2;
      }

      .hero-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        pointer-events: none;
        z-index: -1;
      }
      
      .hero-inner-card {
        height: auto !important;
        min-height: 420px; /* Fixed min-height to prevent card resize during typing */
        max-height: none !important;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
        overflow: visible;
        border-radius: 15px;
        /* Glass morphism effect - inner card */
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 
          0 8px 32px rgba(0, 0, 0, 0.1),
          0 4px 16px rgba(0, 0, 0, 0.05),
          inset 0 1px 0 rgba(255, 255, 255, 0.4);
        position: relative;
        z-index: 3;
        /* Flexbox to arrange children */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
      }

      .hero-inner-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 15px;
        pointer-events: none;
        z-index: -1;
      }

      .hero-title-container {
        margin-bottom: 1.5rem;
        width: 100%;
      }

      .hero-text-wrapper {
        height: auto !important;
        min-height: 120px; /* Fixed min-height to prevent spacing changes during typing */
        max-height: none !important;
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem; /* Fixed spacing between lines */
      }

      #hero-line1 {
        font-size: clamp(26px, 8vw, 48px); /* Increased from 22px to 26px */
        height: auto !important;
        min-height: 1.3em; /* Minimum height to reserve space */
        margin-bottom: 0;
        display: block;
        text-align: center;
        width: 100%;
        font-weight: 600;
        color: var(--text-black);
        line-height: 1.3;
      }

      #hero-line2 {
        font-size: clamp(38px, 12vw, 72px); /* Increased from 32px to 38px */
        height: auto !important;
        min-height: 1.3em; /* Minimum height to reserve space */
        line-height: 1.3;
        padding-bottom: 0px;
        margin-bottom: 0;
        display: block;
        text-align: center;
        width: 100%;
        background: linear-gradient(180deg, #2eb589 0%, #2eb589 45%, #2ba5d9 55%, #2ba5d9 100%);
        background-size: 100% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        animation: gradientShift 4s ease-in-out infinite;
      }
      
      .hero-content p {
        height: auto;
        max-height: none;
        margin-top: 1rem;
        margin-bottom: 1rem;
        padding: 0 0.8rem; /* Increased padding for justify */
        font-size: clamp(1rem, 3.8vw, 1.15rem); /* Increased from 0.95rem to 1rem */
        line-height: 1.6;
        text-align: justify; /* Changed from center to justify */
        text-align-last: center; /* Keep last line centered */
        color: var(--text-black);
      }
      
      .cta-btn {
        height: auto;
        width: auto;
        padding: clamp(0.7rem, 2.5vw, 0.9rem) clamp(1.5rem, 5vw, 2rem);
        font-size: clamp(0.85rem, 3vw, 1rem);
        margin: 0 auto;
        margin-top: 0.5rem;
        /* Button styling */
        background: var(--white);
        color: var(--text-black);
        border: none;
        border-radius: 25px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-decoration: none;
        display: inline-block;
        text-align: center;
      }

      .cta-btn:hover {
        background: var(--light-grey);
        transform: scale(1.05) translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
      }

      .about-container,
      .contact-container {
        padding: clamp(1.5rem, 4vw, 2rem) clamp(1rem, 3vw, 1.5rem);
      }
      
      .about-inner-card,
      .contact-inner-card {
        padding: clamp(2rem, 5vw, 2.5rem) clamp(1.5rem, 4vw, 2rem);
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
        min-height: auto; /* Changed from 100vh - no need for full height */
        padding: 2rem 1rem; /* Added top/bottom padding for spacing */
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* Align to top, not center */
        align-items: center;
      }

      .services-container {
        width: 94vw; /* Reduced from 98vw to add more margin from edges */
        max-width: none;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
        margin: 0 auto; /* Center the container */
      }

      .services-wrapper {
        padding: clamp(1.2rem, 4vw, 2rem) clamp(1rem, 4vw, 2rem);
      }

      .services-wrapper h2 {
        font-size: clamp(1.5rem, 5vw, 2.2rem) !important;
        margin-bottom: clamp(1.5rem, 4vw, 2rem) !important;
      }

      .services {
        display: grid;
        grid-template-columns: 1fr 1fr; /* 2 cards per row on mobile */
        grid-template-rows: auto;
        gap: clamp(1rem, 3.5vw, 1.5rem); /* Increased gap between cards */
        padding: 0;
        overflow: visible;
        justify-items: center;
        align-items: start;
        width: 100%;
        margin: 0 auto;
      }

      .card {
        width: clamp(150px, 45vw, 180px);
        height: clamp(210px, 52vw, 250px) !important;
        margin: 0;
        transform-style: preserve-3d; /* Fix flickering */
        transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
      }

      .card .services-card-front, .card .services-card-back {
        transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1) !important;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transform-style: preserve-3d; /* Fix flickering */
      }

      .service-image-frame {
        padding: clamp(8px, 2vw, 12px); /* Proper padding around frame */
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 15px 15px 0 0;
        border-bottom: 1px solid var(--glass-border);
      }

      .service-image-frame img {
        width: 80px !important;
        height: 80px !important;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid var(--primary-green);
        background: rgba(52, 209, 157, 0.05);
        padding: clamp(6px, 1.5vw, 10px); /* Proper padding inside border */
        box-shadow: 0 4px 20px rgba(52, 209, 157, 0.2);
      }

      .service-card-info {
        padding: 0.6rem 0.5rem 0.5rem 0.5rem; /* Proper padding for text area */
        height: calc(100% - 100px); /* Take remaining height after image */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
      }

      .service-card-info h3 {
        font-size: 0.82rem !important;
        margin: 0 0 6px 0;
        line-height: 1.25;
        font-weight: 700;
        white-space: normal;
        overflow: visible;
        max-height: 2.5em; /* Limit to 2 lines */
      }

      .service-card-info .specs {
        font-size: 0.68rem !important;
        line-height: 1.6 !important; /* Better spacing between lines */
        margin-top: 0;
        white-space: normal;
        overflow: visible;
      }

      .service-card-info .specs strong {
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-block;
      }

      .coming-soon {
        font-size: 0.7rem;
        margin-top: 8px;
        font-weight: 700;
        padding: 4px 8px;
        background: rgba(52, 209, 157, 0.2);
        border-radius: 8px;
        display: inline-block;
      }

      .services-card-back {
        padding: clamp(1rem, 3vw, 1.5rem); /* Responsive padding */
      }

      .service-card-content p {
        font-size: clamp(0.72rem, 2vw, 0.85rem) !important; /* More responsive */
        margin-bottom: clamp(0.8rem, 2.5vw, 1.2rem); /* Responsive spacing */
        line-height: 1.5 !important;
        font-weight: 500;
      }

      .book-btn {
        padding: clamp(0.45rem, 1.5vw, 0.6rem) clamp(0.9rem, 2.5vw, 1.2rem) !important; /* Responsive padding */
        font-size: clamp(0.7rem, 2vw, 0.8rem) !important; /* Responsive font */
        border-radius: 12px;
        font-weight: 700;
      }

      .about-section {
        min-height: auto; /* Changed from 100vh - no need for full height */
        padding: 2rem 1rem; /* Added top/bottom padding for spacing */
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* Align to top, not center */
        align-items: center;
      }

      .about-container {
        width: 94vw; /* Reduced from 98vw to match services section margin */
        max-width: none;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem);
        margin: 0 auto; /* Center the container */
      }

      .about-inner-card {
        padding: clamp(1.2rem, 4vw, 2rem) clamp(1rem, 4vw, 2rem); /* Reduced padding */
      }

      .cta-section {
        min-height: auto; /* Changed from 100vh - no need for full height */
        padding: 2rem 1rem; /* Added top/bottom padding for spacing */
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* Align to top, not center */
        align-items: center;
      }

      .contact-container {
        width: 98vw;
        max-width: none;
        padding: clamp(1.5rem, 5vw, 2.5rem) clamp(1rem, 4vw, 2rem); /* Reduced padding */
      }

      .contact-inner-card {
        padding: clamp(1.2rem, 4vw, 2rem) clamp(1rem, 4vw, 2rem); /* Reduced padding */
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

      /* Additional mobile overrides for desktop styles */
      .hero-content,
      .services-container,
      .about-container,
      .contact-container {
        width: 95vw;
        padding: 1.5rem 1rem;
      }
      
      /* Removed duplicate .hero-inner-card definition - using the one at line 1515 instead */
      /* Removed duplicate .hero-inner-card::before - using the one near line 1515 instead */
      
      .contact-content {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 480px) {
      .services-container {
        width: 95vw; /* Reduced to add margin from edges */
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
        height: clamp(170px, 43vw, 200px) !important;
        margin: 0;
        transform-style: preserve-3d; /* Fix flickering */
        transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
      }

      .card .services-card-front, .card .services-card-back {
        transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1) !important;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        transform-style: preserve-3d; /* Fix flickering */
      }

      .service-image-frame {
        padding: clamp(6px, 1.5vw, 8px) !important; /* Proper frame padding */
        height: 75px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 15px 15px 0 0 !important;
        border-bottom: 1px solid var(--glass-border) !important;
      }

      .service-image-frame img {
        width: 65px !important;
        height: 65px !important;
        object-fit: cover !important;
        border-radius: 6px !important;
        border: 2px solid var(--primary-green) !important;
        background: rgba(52, 209, 157, 0.05) !important;
        padding: clamp(4px, 1vw, 6px) !important; /* Proper image padding */
        box-shadow: 0 4px 20px rgba(52, 209, 157, 0.2) !important;
      }

      .service-card-info {
        padding: 0.4rem 0.3rem !important;
        height: calc(100% - 75px) !important; /* Remaining space */
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-start !important;
      }

      .service-card-info h3 {
        font-size: 0.68rem !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        margin: 0 0 4px 0 !important;
        max-height: 2.5em !important;
        white-space: normal !important;
        overflow: visible !important;
      }

      .service-card-info .specs {
        font-size: 0.58rem !important;
        line-height: 1.55 !important;
        margin-top: 0 !important;
        white-space: normal !important;
        overflow: visible !important;
      }

      .service-card-info .specs strong {
        font-size: 0.6rem !important;
        font-weight: 700 !important;
      }

      .services-card-back {
        padding: clamp(0.7rem, 2.5vw, 1rem) !important; /* Responsive padding */
      }

      .service-card-content p {
        font-size: clamp(0.62rem, 1.8vw, 0.72rem) !important; /* More responsive */
        margin-bottom: clamp(0.6rem, 2vw, 0.8rem) !important; /* Responsive spacing */
        line-height: 1.45 !important;
        font-weight: 500;
      }

      .book-btn {
        padding: clamp(0.32rem, 1.2vw, 0.4rem) clamp(0.65rem, 2vw, 0.8rem) !important; /* Responsive */
        font-size: clamp(0.55rem, 1.6vw, 0.65rem) !important; /* Responsive */
        border-radius: 8px !important;
        font-weight: 700 !important;
      }
    }

    @media (max-width: 360px) {
      .services {
        gap: 0.8rem !important;
      }

      .card {
        width: clamp(100px, 32vw, 120px) !important;
        height: clamp(155px, 40vw, 180px) !important;
        transform-style: preserve-3d !important; /* Fix flickering */
        transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1) !important;
      }

      .card .services-card-front, .card .services-card-back {
        transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1) !important;
        -webkit-backface-visibility: hidden !important;
        backface-visibility: hidden !important;
        transform-style: preserve-3d !important; /* Fix flickering */
      }

      .service-image-frame {
        padding: clamp(5px, 1.2vw, 7px) !important; /* Proper frame padding */
        height: 65px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 15px 15px 0 0 !important;
        border-bottom: 1px solid var(--glass-border) !important;
      }

      .service-image-frame img {
        width: 55px !important;
        height: 55px !important;
        object-fit: cover !important;
        border-radius: 5px !important;
        border: 2px solid var(--primary-green) !important;
        background: rgba(52, 209, 157, 0.05) !important;
        padding: clamp(3px, 0.8vw, 5px) !important; /* Proper image padding */
        box-shadow: 0 4px 20px rgba(52, 209, 157, 0.2) !important;
      }

      .service-card-info {
        padding: 0.3rem 0.25rem !important;
        height: calc(100% - 65px) !important; /* Remaining space */
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-start !important;
      }

      .service-card-info h3 {
        font-size: 0.56rem !important;
        line-height: 1.2 !important;
        font-weight: 700 !important;
        margin: 0 0 3px 0 !important;
        max-height: 2.4em !important;
        white-space: normal !important;
        overflow: visible !important;
      }

      .service-card-info .specs {
        font-size: 0.5rem !important;
        line-height: 1.5 !important;
        margin-top: 0 !important;
        white-space: normal !important;
        overflow: visible !important;
      }

      .service-card-info .specs strong {
        font-size: 0.52rem !important;
        font-weight: 700 !important;
      }
        white-space: normal !important;
        overflow: visible !important;
      

      .services-card-back {
        padding: clamp(0.55rem, 2vw, 0.75rem) !important; /* Responsive padding */
      }

      .service-card-content p {
        font-size: clamp(0.52rem, 1.6vw, 0.62rem) !important; /* More responsive */
        margin-bottom: clamp(0.5rem, 1.8vw, 0.7rem) !important; /* Responsive spacing */
        line-height: 1.35 !important;
        font-weight: 500;
      }

      .book-btn {
        padding: clamp(0.28rem, 1vw, 0.35rem) clamp(0.55rem, 1.8vw, 0.7rem) !important; /* Responsive */
        font-size: clamp(0.5rem, 1.5vw, 0.6rem) !important; /* Responsive */
        border-radius: 6px !important;
        font-weight: 700 !important;
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

      /* Hero section adjustments for extra small screens */
      .hero-section {
        height: 100vh;
        max-height: 100vh;
        padding-top: calc(60px + 1rem);
        /* Center content vertically */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }

      .hero-content {
        height: auto !important; /* Changed from fixed 440px to auto for flexible content */
        width: 99vw;
      }
      
      .hero-inner-card {
        min-height: 360px !important; /* Fixed min-height for extra small screens */
        max-height: none !important; /* Removed max-height restriction */
        height: auto !important; /* Changed from fixed 340px to auto */
        padding: clamp(1rem, 3vw, 1.5rem) clamp(0.8rem, 2.5vw, 1.2rem);
        overflow: visible;
        /* Flexbox for arrangement */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
      }

      .hero-text-wrapper {
        height: auto !important;
        min-height: 100px !important; /* Fixed min-height to prevent spacing changes */
        max-height: none !important;
        margin-bottom: 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 0.4rem; /* Fixed spacing between lines */
      }

      #hero-line1 {
        height: auto !important;
        min-height: 1.3em !important; /* Reserve space for text */
        margin-bottom: 0;
        display: block;
        text-align: center;
        width: 100%;
        font-size: clamp(22px, 7vw, 28px); /* Increased from 18px to 22px */
        line-height: 1.3;
        font-weight: 600;
      }

      #hero-line2 {
        height: auto !important;
        min-height: 1.3em !important; /* Reserve space for text */
        line-height: 1.3;
        margin-bottom: 0;
        display: block;
        text-align: center;
        width: 100%;
        font-size: clamp(30px, 10vw, 38px); /* Increased from 24px to 30px */
        font-weight: 700;
      }
      
      .hero-content p {
        height: auto;
        max-height: none;
        margin-top: 0.5rem;
        margin-bottom: 0.8rem;
        padding: 0 0.8rem; /* Increased padding for justify */
        font-size: clamp(0.9rem, 3.5vw, 1.05rem); /* Increased from 0.85rem to 0.9rem */
        line-height: 1.5;
        text-align: justify; /* Changed from center to justify */
        text-align-last: center; /* Keep last line centered */
      }
      
      .cta-btn {
        height: auto !important;
        width: auto !important;
        padding: 0.6rem 1.5rem !important;
        font-size: 0.85rem !important;
        margin: 0.5rem auto 0 !important;
        /* Button styling */
        background: var(--white) !important;
        color: var(--text-black) !important;
        border: none !important;
        border-radius: 25px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        text-decoration: none !important;
        display: inline-block !important;
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