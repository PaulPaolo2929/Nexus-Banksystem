<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn() && isAdmin()) {
    header("Location: admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us | Nexus Bank</title>
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
        transform: translateY(-150%);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 1000;
      }

      .nav-links {
        gap: 0;
      }

      .auth-buttons {
        top: calc(60px + 250px);
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

    /* Contact Section */
    .contact-section {
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

    .contact-section h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .contact-section p {
      font-size: 18px;
      max-width: 700px;
      margin: 0 auto;
      color: #ddd;
    }

    /* Contact Form */
    form.contact-form {
      max-width: 600px;
      margin: 40px auto 0;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      color: var(--dark);
      text-align: left;
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
      color: white;
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

    /* Responsive adjustments for contact form */
    @media (max-width: 768px) {
      .contact-section {
        padding: 120px 20px 40px;
      }
      
      .contact-section h1 {
        font-size: 36px;
      }
      
      .contact-section p {
        font-size: 16px;
      }
      
      form.contact-form {
        padding: 20px;
        margin: 30px auto 0;
      }
    }

    @media (max-width: 480px) {
      form.contact-form {
        padding: 15px;
      }
      
      form.contact-form input,
      form.contact-form textarea {
        padding: 12px 15px;
        margin-bottom: 20px;
      }
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
        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
          <i class="fas fa-bars"></i>
        </button>
      </nav>
    </div>
  </header>

  <main>
    <section class="contact-section">
      <div class="container">
        <h1>Contact Us</h1>
        <p>Have questions or want to get in touch? We're here to help you.</p>
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
      
      hamburger.addEventListener('click', function() {
        // Toggle menu visibility
        navLinks.classList.toggle('active');
        authButtons.classList.toggle('active');
        
        // Change hamburger icon
        const icon = this.querySelector('i');
        if (navLinks.classList.contains('active')) {
          icon.classList.replace('fa-bars', 'fa-times');
        } else {
          icon.classList.replace('fa-times', 'fa-bars');
        }
      });

      // Close menu when clicking on a link (for mobile)
      document.querySelectorAll('#nav-links a, #auth-buttons a').forEach(link => {
        link.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            navLinks.classList.remove('active');
            authButtons.classList.remove('active');
            hamburger.querySelector('i').classList.replace('fa-times', 'fa-bars');
          }
        });
      });
    });
  </script>
</body>
</html>