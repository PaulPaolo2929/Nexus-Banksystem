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

      .nav-links, .auth-buttons {
        position: fixed;
        top: 60px;
        left: 0;
        width: 100%;
        background-color: white;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        transform: translateY(-100%);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 1000;
      }

      .nav-links {
        gap: 0;
      }

      .auth-buttons {
        top: calc(60px + 250px); /* Adjust based on nav-links height */
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

      .nav-links.active, .auth-buttons.active {
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
          <a href="about-us.php" class="active">About Us</a>
          <a href="services.php">Services</a>
          <a href="contact.php" class="active">Contact</a>
        </div>
      
        <div class="auth-buttons" id="auth-buttons">
          <?php if (isset($_SESSION['user_id'])): ?>
            
          <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Sign Up</a>
          <?php endif; ?>
        </div>
        <button class="hamburger" id="hamburger">
          <i class="fas fa-bars"></i>
        </button>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container">
        <h1>Where Money Meets Trust</h1>
        <p>At Nexus Bank, we prioritize your financial security and convenience with our trusted banking services tailored for you.</p>
        <a href="register.php" class="cta-button">Get Started</a>
      </div>
    </section>

    <section class="features">
      <div class="container">
        <div class="section-title">
          <h2>Our Core Services</h2>
          <p>Explore the benefits and security we provide to help manage your money better.</p>
        </div>
        <div class="features-grid">
          <article class="feature-card">
            <i class="fa fa-shield-alt" aria-hidden="true"></i>
            <h3>Secure Banking</h3>
            <p>Your safety is our priority. We use top-notch security to protect your money and information.</p>
          </article>
          <article class="feature-card">
            <i class="fa fa-mobile-alt" aria-hidden="true"></i>
            <h3>Mobile Access</h3>
            <p>Bank on the go with our user-friendly mobile app, available 24/7 at your fingertips.</p>
          </article>
          <article class="feature-card">
            <i class="fa fa-dollar-sign" aria-hidden="true"></i>
            <h3>Low Fees</h3>
            <p>Enjoy competitive rates and minimal fees designed to give you more value.</p>
          </article>
          <article class="feature-card">
            <i class="fa fa-headset" aria-hidden="true"></i>
            <h3>24/7 Support</h3>
            <p>Our dedicated support team is always ready to assist you with any questions or concerns.</p>
          </article>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container">
        <hr style="border: none; height: 1px; background-color: rgba(255, 255, 255, 0.1); margin: 20px 0;" />
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Nexus Bank</h3>
                <p>Where money meets trust. Providing reliable banking services since 2019.</p>
                <div class="contact-info" style="color: var(--light-gray); font-size: 16px; margin-top: 20px; white-space: nowrap;">
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
        <hr style="border: none; height: 1px; background-color: rgba(255, 255, 255, 0.1); margin: 20px 0;" />
        <div class="copyright">
            &copy; 2025 Nexus Bank. All rights reserved.
        </div>
        <hr style="border: none; height: 1px; background-color: rgba(255, 255, 255, 0.1); margin: 20px 0 0 0;" />
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hamburger = document.getElementById('hamburger');
      const navLinks = document.getElementById('nav-links');
      const authButtons = document.getElementById('auth-buttons');
      const hamburgerIcon = hamburger.querySelector('i');

      hamburger.addEventListener('click', function() {
        // Toggle menu visibility
        navLinks.classList.toggle('active');
        authButtons.classList.toggle('active');
        
        // Change hamburger icon
        if (navLinks.classList.contains('active')) {
          hamburgerIcon.classList.remove('fa-bars');
          hamburgerIcon.classList.add('fa-times');
        } else {
          hamburgerIcon.classList.remove('fa-times');
          hamburgerIcon.classList.add('fa-bars');
        }
      });

      // Close menu when clicking on a link
      document.querySelectorAll('#nav-links a, #auth-buttons a').forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            navLinks.classList.remove('active');
            authButtons.classList.remove('active');
            hamburgerIcon.classList.remove('fa-times');
            hamburgerIcon.classList.add('fa-bars');
          }
        });
      });
    });
  </script>
</body>
</html>