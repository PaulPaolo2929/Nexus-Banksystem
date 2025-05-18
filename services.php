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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TrustBank | Our Services</title>
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

        /* Page Title Section */
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
            margin-bottom: 10px;
        }

        .page-title p {
            font-size: 18px;
            max-width: 700px;
            margin: 0 auto;
            color: #ddd;
        }

        /* Services Section */
        .services {
            padding: 60px 0 100px;
            background-color: var(--light);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
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

            .page-title h1 {
                font-size: 36px;
            }

            .services-grid {
                grid-template-columns: 1fr;
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
                    <a href="services.php" class="active">Services</a>
                    <a href="contact.php">Contact</a>
                    
                </div>
                <div class="auth-buttons">
                    <a href="login.php">Login</a>
                    <a href="register.php">Sign Up</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Title Section -->
    <section class="page-title">
        <div class="container">
            <h1>Our Services</h1>
            <p>Explore the comprehensive financial services designed to help you achieve your goals.</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="services-grid">
                <div class="service-card">
                    <i class="fas fa-piggy-bank"></i>
                    <h3>Savings Accounts</h3>
                    <p>Secure and competitive savings accounts with high interest rates to help grow your wealth safely.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-credit-card"></i>
                    <h3>Credit Cards</h3>
                    <p>Flexible credit card options with cashback rewards, travel benefits, and low fees.</p>
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
                    <i class="fas fa-shield-alt"></i>
                    <h3>Insurance</h3>
                    <p>Comprehensive insurance policies designed to protect your family, assets, and future.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Mobile Banking</h3>
                    <p>Manage your accounts conveniently with our secure and user-friendly mobile banking app.</p>
                </div>
            </div>
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