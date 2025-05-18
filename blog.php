<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? "admin/dashboard.php" : "user/dashboard.php"));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nexus Bank | Why Choose Us</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
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
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--light);
      color: var(--dark);
    }

    header {
      background: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      position: fixed;
      width: 100%;
      z-index: 999;
    }

    nav {
      max-width: 1200px;
      margin: auto;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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

    .auth-buttons a {
      margin-left: 15px;
      text-decoration: none;
      font-weight: 500;
    }

    .auth-buttons a:first-child {
      color: var(--gray);
    }

    .auth-buttons a:last-child {
      color: white;
      background-color: var(--primary);
      padding: 10px 20px;
      border-radius: 5px;
    }

    .auth-buttons a:last-child:hover {
      background-color: var(--primary-dark);
    }

    .page-title {
      padding: 140px 0 60px;
      text-align: center;
      background: 
        linear-gradient(
          rgba(0, 30, 60, 0.6), 
          rgba(0, 30, 60, 0.6)
        ),
        url('assets/images/background.jpg') no-repeat center center/cover;
      color: white;
    }

    .page-title h1 {
      font-size: 48px;
      font-weight: 700;
    }

    .page-title p {
      margin-top: 10px;
      font-size: 18px;
      color: #ddd;
    }

    .pros-section {
      padding: 60px 20px;
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .pro-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      padding: 20px;
      text-align: center;
      transition: transform 0.3s;
    }

    .pro-card:hover {
      transform: translateY(-5px);
    }

    .pro-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .pro-card h3 {
      font-size: 22px;
      margin-bottom: 10px;
      color: var(--primary);
    }

    .pro-card p {
      font-size: 16px;
      color: var(--gray);
      line-height: 1.4;
    }

    footer {
      background-color: var(--dark);
      color: white;
      padding: 60px 0 20px;
      margin-top: 80px;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
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
      margin-top: 40px;
      font-size: 14px;
      color: var(--gray);
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        gap: 15px;
      }
      .nav-links {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
      .pros-section {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <nav>
      <a href="index.php" class="logo">
        <img src="assets/images/Logo-color-1.png" alt="Nexus Bank Logo" />
        Nexus Bank
      </a>
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about-us.php">About Us</a>
        <a href="services.php">Services</a>
        <a href="contact.php">Contact</a>
        <a href="blog.php" class="active">Blog</a>
      </div>
      <div class="auth-buttons">
        <a href="login.php">Login</a>
        <a href="register.php">Sign Up</a>
      </div>
    </nav>
  </header>

  <section class="page-title">
    <h1>Why Choose Nexus Bank?</h1>
    <p>Discover the advantages and benefits of banking with us.</p>
  </section>

  <section class="pros-section">
    <div class="pro-card">
      <img src="assets/images/secure-banking.jpg" alt="Secure Banking" />
      <h3>Secure Banking</h3>
      <p>We prioritize your security with advanced encryption and fraud protection.</p>
    </div>
    <div class="pro-card">
      <img src="assets/images/customer-support.jpg" alt="Customer Support" />
      <h3>24/7 Customer Support</h3>
      <p>Our dedicated support team is available around the clock to assist you.</p>
    </div>
    <div class="pro-card">
      <img src="assets/images/fast-transactions.jpg" alt="Fast Transactions" />
      <h3>Fast Transactions</h3>
      <p>Experience quick and seamless transactions with our efficient banking system.</p>
    </div>
    <div class="pro-card">
      <img src="assets/images/mobile-banking.jpg" alt="Mobile Banking" />
      <h3>Mobile Banking</h3>
      <p>Manage your accounts anytime, anywhere with our user-friendly mobile app.</p>
    </div>
    <div class="pro-card">
      <img src="assets/images/investment-planning.jpg" alt="Investment Planning" />
      <h3>Investment Planning</h3>
      <p>Get expert advice to grow your wealth and secure your financial future.</p>
    </div>
    <div class="pro-card">
      <img src="assets/images/loan-options.jpg" alt="Loan Options" />
      <h3>Flexible Loan Options</h3>
      <p>Choose from a variety of loan products tailored to your needs.</p>
    </div>
  </section>

  <footer>
    <div class="container">
      <hr style="border: none; height: 1px; background-color: rgba(255, 255, 255, 0.1); margin: 20px auto;" />
      <div class="footer-grid">
        <div class="footer-col">
          <h3>Nexus Bank</h3>
          <p>Where money meets trust. Providing reliable banking services since 1995.</p>
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
</body>
</html>
