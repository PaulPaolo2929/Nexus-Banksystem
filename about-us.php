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
    <title>TrustBank | About Us</title>
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
            padding-top: 60px; /* Account for fixed header */
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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

        .nav-links a.active {
            color: var(--primary);
            font-weight: 700;
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

        /* Page Title Section */
        .page-title {
            padding: 100px 0 60px;
            text-align: center;
            background: 
              linear-gradient(
                rgba(0, 30, 60, 0.6), 
                rgba(0, 30, 60, 0.6)
              ),
              url('assets/images/background.jpg') no-repeat center center/cover;
            color: white;
            margin-top: -60px; /* Offset fixed header */
        }

        .page-title h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-title p {
            font-size: 18px;
            max-width: 700px;
            margin: 0 auto;
            color: #ddd;
        }

        /* About Content Section */
        .about-content {
            max-width: 900px;
            margin: 40px auto 80px;
            background-color: white;
            padding: 40px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            line-height: 1.8;
            color: var(--dark);
        }

        .about-content h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 32px;
            text-align: center;
        }

        .about-content p {
            font-size: 18px;
            color: var(--gray);
            margin-bottom: 20px;
        }

        .about-content ul {
            margin-left: 30px;
            margin-bottom: 20px;
        }

        .about-content li {
            font-size: 18px;
            color: var(--gray);
            margin-bottom: 10px;
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
                    <a href="index.php" class="logo" aria-label="TrustBank Home">
                        <img src="assets/images/Logo-color-1.png" alt="Nexus Bank logo" />
                    </a>
                </div>
                <div class="nav-links" id="nav-links">
                    <a href="index.php">Home</a>
                    <a href="about-us.php" class="active">About Us</a>
                    <a href="services.php">Services</a>
                    <a href="contact.php">Contact</a>
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

    <!-- Page Title Section -->
    <section class="page-title">
        <div class="container">
            <h1>About Us</h1>
            <p>Learn more about Nexus Bank — our mission, vision, and commitment to serving you.</p>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="about-content">
        <div class="container">
            <h2>Our Story</h2>
            <p>
                Since 2019, Nexus Bank has been committed to providing trustworthy and innovative financial services. 
                Our mission is to empower individuals and businesses to achieve their financial goals through personalized solutions, 
                cutting-edge technology, and a dedicated team of experts.
            </p>
            <p>
                We believe in building lasting relationships with our clients based on transparency, integrity, and mutual success. 
                Whether you're saving for the future, investing, or managing daily finances, Nexus Bank is here to guide you every step of the way.
            </p>
            <h2>Our Vision</h2>
            <p>
                To be the most trusted and customer-centric bank recognized for excellence in financial services and community support.
            </p>
            <h2>Our Values</h2>
            <ul>
                <li>Customer Focus — Putting your needs first</li>
                <li>Innovation — Embracing technology to serve you better</li>
                <li>Integrity — Acting with honesty and transparency</li>
                <li>Excellence — Striving for the highest standards</li>
                <li>Community — Supporting the growth and wellbeing of our society</li>
            </ul>
        </div>
    </section>

    <!-- Footer -->
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