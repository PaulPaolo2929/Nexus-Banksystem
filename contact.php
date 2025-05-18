<?php
session_start();
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us | Nexus Bank</title>
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
      --white: #fff;
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
    /* Header */
    header {
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      position: fixed;
      width: 100%;
      z-index: 1000;
    }
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 0;
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
      transition: background-color 0.3s;
    }
    .auth-buttons a:last-child:hover {
      background-color: var(--primary-dark);
    }
    /* Hero Section (blue gradient) */
    .hero {
      padding: 180px 0 100px;
      background:
        linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%),
        repeating-radial-gradient(circle at center, rgba(255,255,255,0.05), rgba(255,255,255,0.05) 1px, transparent 2px, transparent 10px);
      text-align: center;
      color: var(--white);
    }
    .hero h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 10px;
    }
    .hero p {
      font-size: 20px;
      max-width: 700px;
      margin: 0 auto;
      color: #cdd9f3;
    }
    /* Contact Form Section */
    .contact-section {
      padding: 80px 0 100px;
      background-color: var(--light);
      color: var(--dark);
    }
    .section-title {
      text-align: center;
      margin-bottom: 50px;
    }
    .section-title h2 {
      font-size: 32px;
      color: var(--primary-dark);
      margin-bottom: 15px;
      font-weight: 700;
    }
    .section-title p {
      color: var(--gray);
      max-width: 700px;
      margin: 0 auto;
      font-weight: 500;
    }
    form.contact-form {
      max-width: 600px;
      margin: 0 auto;
      background: var(--white);
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    form.contact-form label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--primary-dark);
    }
    form.contact-form input,
    form.contact-form textarea {
      width: 100%;
      padding: 15px 18px;
      margin-bottom: 25px;
      border: 1.5px solid var(--light-gray);
      border-radius: 8px;
      font-size: 16px;
      font-family: inherit;
      transition: border-color 0.3s;
      resize: vertical;
      color: var(--dark);
    }
    form.contact-form input::placeholder,
    form.contact-form textarea::placeholder {
      color: var(--gray);
    }
    form.contact-form input:focus,
    form.contact-form textarea:focus {
      outline: none;
      border-color: var(--primary);
      background-color: #e7f0ff;
    }
    form.contact-form textarea {
      min-height: 150px;
    }
    form.contact-form button {
      background-color: var(--primary);
      color: var(--white);
      padding: 15px 35px;
      border: none;
      border-radius: 8px;
      font-weight: 700;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s;
      width: 100%;
    }
    form.contact-form button:hover {
      background-color: var(--primary-dark);
    }
    /* Message Box Styles */
    .message-box {
      max-width: 600px;
      margin: 20px auto;
      padding: 15px 20px;
      border-radius: 8px;
      font-weight: 600;
      text-align: center;
    }
    .message-success {
      background-color: #d1e7dd;
      color: #0f5132;
      border: 1px solid #badbcc;
    }
    .message-error {
      background-color: #f8d7da;
      color: #842029;
      border: 1px solid #f5c2c7;
    }
    /* Footer */
    footer {
      background-color: var(--dark);
      color: var(--white);
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
      color: var(--white);
    }
    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }
    .social-links a {
      color: var(--white);
      font-size: 18px;
    }
    .copyright {
      text-align: center;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      color: var(--gray);
      font-size: 14px;
    }
    /* Responsive */
    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        gap: 20px;
      }
      .nav-links {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
      .auth-buttons {
        margin-top: 15px;
      }
      .hero {
        padding: 150px 0 80px;
      }
      form.contact-form {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container">
      <nav>
        <a href="index.php" class="logo">
          <img src="assets/images/Logo-color-1.png" alt="Nexus Bank Logo" />
          
        </a>
        <div class="nav-links">
          <a href="index.php">Home</a>
          <a href="about-us.php">About Us</a>
          <a href="services.php">Services</a>
          <a href="contact.php" style="color: var(--primary); font-weight: 700;">Contact</a>
          
        </div>
        <div class="auth-buttons">
          <a href="login.php">Login</a>
          <a href="register.php">Sign Up</a>
        </div>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h1>Contact Us</h1>
      <p>Have questions or want to get in touch? We’re here to help you.</p>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="contact-section">
    <div class="container">
      <div class="section-title">
        <h2>Get in Touch</h2>
        <p>Fill out the form below and our team will get back to you as soon as possible.</p>
      </div>
      <?php if (isset($_SESSION['contact_success'])): ?>
        <div class="message-box message-success">
          <?php 
            echo htmlspecialchars($_SESSION['contact_success']); 
            unset($_SESSION['contact_success']);
          ?>
        </div>
      <?php elseif (isset($_SESSION['contact_error'])): ?>
        <div class="message-box message-error">
          <?php 
            echo htmlspecialchars($_SESSION['contact_error']); 
            unset($_SESSION['contact_error']);
          ?>
        </div>
      <?php endif; ?>
      <form class="contact-form" action="process-contact.php" method="POST" novalidate>
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required placeholder="Your full name" />

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required placeholder="you@example.com" />

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" required placeholder="Subject of your message" />

        <label for="message">Message</label>
        <textarea id="message" name="message" required placeholder="Write your message here..."></textarea>

        <button type="submit">Send Message</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
        <hr style="border: none; height: 1px; background-color: rgba(255, 255, 255, 0.1); margin: 20px 0;" />
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
<style>
    footer .footer-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    footer .footer-col ul.footer-links a {
        color: var(--light-gray);
        text-decoration: none;
    }
    footer .footer-col ul.footer-links a:hover {
        color: white;
    }
</style>
</footer>
    </div>
</footer>
    </boody>
    
    </html>