<?php    
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nexus Bank | Where Money Meets Trust</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary: #0056b3;
      --primary-dark: #003d82;
      --secondary: #28a745;
      --dark: #212529;
      --light: #f8f9fa;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --nav-links-height: 0px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    body {
      color: var(--dark);
      line-height: 1.6;
      background-color: var(--light);
    }

    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    header {
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      position: fixed;
      width: 100%;
      z-index: 2000;
      padding: 0;
      height: 60px;
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 60px;
      padding: 0 20px;
      position: relative;
    }

    .nav-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo img {
      height: 40px;
      display: block;
    }

    .nav-links {
      display: flex;
      gap: 30px;
    }

    .nav-links a {
      color: var(--dark);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }

    .nav-links a:hover {
      color: var(--primary);
    }

    .auth-buttons {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .auth-buttons a {
      text-decoration: none;
      font-weight: 500;
    }

    .auth-buttons a:first-child {
      color: var(--gray);
    }

    .auth-buttons a:last-child {
      color: white;
      background-color: var(--primary);
      padding: 8px 20px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .auth-buttons a:last-child:hover {
      background-color: var(--primary-dark);
    }

    /* Hamburger Menu Styles */
    .hamburger {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 10px;
      z-index: 1001;
    }

    .hamburger i {
      font-size: 24px;
      color: var(--dark);
    }

    /* Mobile Menu Styles */
    @media (max-width: 768px) {
      .hamburger {
        display: block;
      }

      .nav-links {
        position: fixed;
        top: 60px;
        left: 0;
        width: 100%;
        background-color: white;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        transform: translateY(-150%);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 1000;
        gap: 0;
      }

      .auth-buttons {
        position: fixed;
        top: calc(60px + 250px);
        left: 0;
        width: 100%;
        background-color: white;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        transform: translateY(-150%);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 999;
        border-top: 1px solid var(--light-gray);
      }

      .nav-links a, .auth-buttons a {
        width: 100%;
        text-align: center;
        padding: 15px 0;
      }

      .auth-buttons a:last-child {
        margin: 10px auto;
        width: 200px;
      }

      .nav-links.active {
        transform: translateY(0);
        opacity: 1;
        pointer-events: all;
      }

      .auth-buttons.active {
        transform: translateY(0);
        opacity: 1;
        pointer-events: all;
      }
    }

    /* Hero Section */
    .hero {
      padding: 140px 0 60px;
      text-align: center;
      background: 
        linear-gradient(
          rgba(0, 30, 60, 0.6), 
          rgba(0, 30, 60, 0.6)
        ),
        url('assets/images/background.jpg') no-repeat center center/cover;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .hero h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .hero p {
      font-size: 18px;
      max-width: 700px;
      margin: 0 auto 0;
      color: #ddd;
    }

    .cta-button {
      display: inline-block;
      background-color: var(--secondary);
      color: white;
      padding: 12px 30px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      transition: background-color 0.3s;
      margin-top: 20px;
    }

    .cta-button:hover {
      background-color: #218838;
    }

    /* Features Section */
    .features {
      padding: 80px 0;
      background-color: white;
    }

    .section-title {
      text-align: center;
      margin-bottom: 50px;
    }

    .section-title h2 {
      font-size: 32px;
      color: var(--dark);
      margin-bottom: 15px;
    }

    .section-title p {
      color: var(--gray);
      max-width: 700px;
      margin: 0 auto;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }

    .feature-card {
      background-color: var(--light);
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s;
    }

    .feature-card:hover {
      transform: translateY(-5px);
    }

    .feature-card i {
      font-size: 40px;
      color: var(--primary);
      margin-bottom: 20px;
    }

    .feature-card h3 {
      font-size: 20px;
      margin-bottom: 15px;
      color: var(--dark);
    }

    .feature-card p {
      color: var(--gray);
    }

    /* Services Section */
    .services {
      padding: 60px 0 100px;
      background-color: var(--light);
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }

    .service-card {
      background-color: white;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s;
      text-align: center;
    }

    .service-card:hover {
      transform: translateY(-5px);
    }

    .service-card i {
      font-size: 50px;
      color: var(--primary);
      margin-bottom: 20px;
    }

    .service-card h3 {
      font-size: 24px;
      margin-bottom: 15px;
      color: var(--dark);
    }

    .service-card p {
      color: var(--gray);
      font-size: 16px;
      line-height: 1.5;
    }

    /* Footer */
    footer {
      background-color: var(--dark);
      color: white;
      padding: 60px 0 20px;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
      margin-bottom: 40px;
    }

    .footer-col h3 {
      font-size: 18px;
      margin-bottom: 20px;
      color: var(--light-gray);
    }

    .footer-links {
      list-style: none;
    }

    .footer-links li {
      margin-bottom: 10px;
    }

    .footer-links a {
      color: var(--gray);
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-links a:hover {
      color: white;
    }

    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    .social-links a {
      color: white;
      font-size: 18px;
    }

    .copyright {
      text-align: center;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      color: var(--gray);
      font-size: 14px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .hero {
        padding: 150px 20px 80px;
      }
      
      .hero h1 {
        font-size: 36px;
      }
      
      .hero p {
        font-size: 16px;
      }
      
      .footer-grid {
        grid-template-columns: 1fr;
        gap: 30px;
      }
      
      .service-card {
        padding: 20px;
      }
      
      .service-card i {
        font-size: 40px;
      }
      
      .service-card h3 {
        font-size: 20px;
      }
    }

    @media (max-width: 480px) {
      .hero h1 {
        font-size: 28px;
      }
      
      .cta-button {
        padding: 10px 20px;
        font-size: 14px;
      }
      
      .feature-card, .service-card {
        padding: 20px 15px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="container">
      <nav>
        <div class="nav-left">
          <a href="index.php" class="logo" aria-label="Nexus Bank Home">
            <img src="assets/images/Logo-color-1.png" alt="Nexus Bank logo" />
          </a>
        </div>
        <div class="nav-links" id="nav-links">
          <a href="index.php">Home</a>
          <a href="about-us.php">About Us</a>
          <a href="services.php">Services</a>
          <a href="contact.php">Contact</a>
        </div>
      
        <div class="auth-buttons" id="auth-buttons">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
          <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Sign Up</a>
          <?php endif; ?>
        </div>
        <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
          <i class="fas fa-bars"></i>
        </button>
      </nav>
    </div>
  </header>

  <main>
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <h1>Where Money Meets Trust</h1>
        <p>At Nexus Bank, we prioritize your financial security and convenience with our trusted banking services tailored for you.</p>
      </div>
    </section>

    <!-- Services Section -->
    <section class="services">
      <div class="container">
        <div class="section-title">
          <h2>Our System Services</h2>
          <p>Explore the comprehensive financial services designed to help you achieve your goals.</p>
        </div>
        <div class="services-grid">
          <div class="service-card">
            <i class="fas fa-piggy-bank"></i>
            <h3>Savings Accounts</h3>
            <p>Secure and competitive savings accounts with high interest rates to help grow your wealth safely.</p>
          </div>
          <div class="service-card">
            <i class="fas fa-home"></i>
            <h3>Home Loans</h3>
            <p>Affordable mortgage solutions tailored to help you buy your dream home with ease.</p>
          </div>
          <div class="service-card">
            <i class="fas fa-chart-line"></i>
            <h3>Investment Planning</h3>
            <p>Personalized investment advice and plans to help you maximize returns and grow your portfolio.</p>
          </div>
          <div class="service-card">
            <i class="fas fa-mobile-alt"></i>
            <h3>Mobile Banking</h3>
            <p>Manage your accounts conveniently with our secure and user-friendly mobile banking app.</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <h3>Nexus Bank</h3>
          <p>Where money meets trust. Providing reliable banking services since 2019.</p>
          <div class="contact-info">
            <p>📧 Email: Nexus-Banksystem@gmail.com</p>
            <p>📞 Phone: 09564282978</p>
          </div>
        </div>
        <div class="footer-col">
          <h3>Services</h3>
          <ul class="footer-links">
            <li><span>Loans</span></li>
            <li><span>Investments</span></li>
            <li><span>Savings</span></li>
            <li><span>Insurance</span></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3>Conditions</h3>
          <ul class="footer-links">
            <li><a href="terms.php">Terms and Conditions</a></li>
            <li><a href="privacy-policy.php">Privacy Policy</a></li>
            <li><a href="security-policy.php">Security Policy</a></li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        &copy; 2025 Nexus Bank. All rights reserved.
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburger = document.getElementById('hamburger');
      const navLinks = document.getElementById('nav-links');
      const authButtons = document.getElementById('auth-buttons');
      const hamburgerIcon = hamburger.querySelector('i');

      // Calculate nav links height and set CSS variable
      function calculateNavHeight() {
        if (window.innerWidth <= 768) {
          const navHeight = navLinks.scrollHeight;
          document.documentElement.style.setProperty('--nav-links-height', `${navHeight}px`);
        }
      }

      // Initial calculation
      calculateNavHeight();
      
      // Recalculate on resize
      window.addEventListener('resize', calculateNavHeight);

      // Toggle menu function
      function toggleMenu() {
        navLinks.classList.toggle('active');
        authButtons.classList.toggle('active');
        
        // Update aria-expanded attribute
        const isExpanded = navLinks.classList.contains('active');
        hamburger.setAttribute('aria-expanded', isExpanded);
        
        // Change hamburger icon
        if (isExpanded) {
          hamburgerIcon.classList.remove('fa-bars');
          hamburgerIcon.classList.add('fa-times');
        } else {
          hamburgerIcon.classList.remove('fa-times');
          hamburgerIcon.classList.add('fa-bars');
        }
      }

      // Hamburger click event
      hamburger.addEventListener('click', toggleMenu);

      // Keyboard accessibility
      hamburger.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleMenu();
        }
      });

      // Close menu when clicking on a link (mobile)
      document.querySelectorAll('#nav-links a, #auth-buttons a').forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            toggleMenu();
          }
        });
      });

      // Close menu when clicking outside (mobile)
      document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            !e.target.closest('nav') && 
            navLinks.classList.contains('active')) {
          toggleMenu();
        }
      });
    });
  </script>
</body>
</html>