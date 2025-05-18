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
    <title>TrustBank | Where Money Meets Trust</title>
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
        
        /* Hero Section */
        .hero {
            padding: 180px 0 100px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
            text-align: center;
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
    .hero {
    padding: 180px 0 100px;
    background: 
      linear-gradient(
        rgba(0, 30, 60, 0.6), 
        rgba(0, 30, 60, 0.6)
      ),
      url('assets/images/background.jpg') no-repeat center center/cover;
    text-align: center;
    color: white;
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
        }
        
        .cta-button:hover {
            background-color: #218838;
        }
        
        /* Features */
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
            padding: 10px  10px;
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
            
            .hero {
                padding: 150px 0 80px;
            }
            
            .hero h1 {
                font-size: 36px;
            }
        }
        .hamburger {
            display: none;
            cursor: pointer;
            width: 30px;
            height: 25px;
            flex-direction: column;
            justify-content: space-between;
        }
        .hamburger .bar {
            height: 4px;
            width: 100%;
            background-color: var(--dark);
            border-radius: 2px;
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                gap: 15px;
                text-align: center;
                background-color: white;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                padding: 20px 0;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 1001;
            }
            .nav-links.active {
                display: flex;
            }
            .auth-buttons {
                display: none;
                flex-direction: column;
                gap: 15px;
                text-align: center;
                background-color: white;
                position: absolute;
                top: calc(70px + 100%);
                left: 0;
                width: 100%;
                padding: 20px 0;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 1001;
            }
            .auth-buttons.active {
                display: flex;
            }
            .hamburger {
                display: flex;
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
                <div class="hamburger" id="hamburger" aria-label="Toggle menu" role="button" tabindex="0">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
                <div class="nav-links" id="nav-links">
                    <a href="index.php">Home</a>
                    <a href="about-us.php">About Us</a>
                    <a href="services.php">Services</a>
                    <a href="contact.php">Contact</a>
                    
                </div>
                <div class="auth-buttons" id="auth-buttons">
                    <a href="login.php">Login</a>
                    <a href="register.php">Sign Up</a>
                </div>
            </nav>
        </div>
    </header>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('nav-links');
        const authButtons = document.getElementById('auth-buttons');

        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            authButtons.classList.toggle('active');
        });
    </script>

    <!-- Hero Section -->
            <section class="hero">
                <div class="container">
                    <h1 style="color: white;">Where Money Meets Trust</h1>
                    <p>Experience secure, reliable banking tailored to your financial goals. Open your savings account today and start building your future with confidence.</p>
                    <a href="register.php" class="cta-button">Open Savings Account</a>
                </div>
            </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Our Banking Services</h2>
                <p>Discover financial products designed to help you grow and protect your money</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-piggy-bank"></i>
                    <h3>Savings Accounts</h3>
                    <p>High-yield savings accounts with competitive interest rates to grow your money faster.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-home"></i>
                    <h3>Home Loans</h3>
                    <p>Competitive mortgage rates with flexible payment options for your dream home.</p>
                </div>
            </div>
        </div>
    </section>

    
    

    <!-- Footer -->
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
