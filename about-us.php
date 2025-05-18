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
    <title>TrustBank | About Us</title>
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

        .nav-links a.active {
            color: var(--primary);
            font-weight: 700;
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
              url('assets/images/about-bg.jpg') no-repeat center center/cover;
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

            .about-content {
                margin: 20px 15px 60px;
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
                    <a href="about-us.php" class="active">About Us</a>
                    <a href="services.php">Services</a>
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
            <h1>About Us</h1>
            <p>Learn more about Nexus Bank — our mission, vision, and commitment to serving you.</p>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="about-content">
        <div class="container">
            <h2>Our Story</h2>
            <p>
                Since 1995, Nexus Bank has been committed to providing trustworthy and innovative financial services. 
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
</body>
</html>
