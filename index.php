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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Digital Banking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to SecureBank</h1>
            <p>Your trusted digital banking partner</p>
        </header>
        
        <div class="hero">
            <div class="hero-content">
                <h2>Banking Made Simple, Secure, and Smart</h2>
                <p>Manage your finances anytime, anywhere with our secure digital banking platform.</p>
                
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary">Open an Account</a>
                    <a href="login.php" class="btn btn-secondary">Login to Your Account</a>
                </div>
            </div>
            
            <div class="hero-image">
                <img src="assets/images/.jpg" alt="Digital Banking">
            </div>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <h3>Secure Transactions</h3>
                <p>Bank-level encryption and OTP verification.</p>
            </div>
            
            <div class="feature-card">
                <h3>24/7 Access</h3>
                <p>Manage your accounts anytime from anywhere in the world.</p>
            </div>
            
            <div class="feature-card">
                <h3>Loan Services</h3>
                <p>Quick and easy loan application with competitive rates.</p>
            </div>
        </div>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> SecureBank. All rights reserved.</p>
            <nav>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </nav>
        </footer>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>