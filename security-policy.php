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
    <title>Security Policy - Nexus Bank</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #212529;
        }
        .container {
            max-width: 900px;
            width: 100%;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            text-align: center; /* Center content */
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #0056b3;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .sticker {
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: url('assets/images/sticker-security.png') no-repeat center center/contain;
            opacity: 0.8;
        }
        @media (max-width: 768px) {
            body, html {
                display: block;
                padding: 20px;
            }
            .container {
                margin: 20px auto;
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sticker"></div>
        <h1>Security Policy</h1>
        <p>At Nexus Bank, we prioritize the security of your personal and financial information.</p>
        <p>1. Data Protection: We use advanced encryption and security protocols to protect your data.</p>
        <p>2. Access Control: Only authorized personnel have access to sensitive information.</p>
        <p>3. Fraud Prevention: We monitor accounts for suspicious activity and take immediate action.</p>
        <p>4. Secure Transactions: All online transactions are secured with SSL and multi-factor authentication.</p>
        <p>5. Incident Response: We have procedures in place to respond to security incidents promptly.</p>
        <p>For any security concerns, please contact our support team immediately.</p>
        <div style="max-width: 900px; margin-left: auto; margin-right: auto; text-align: center; margin-top: 20px;">
            <button id="agreeBtn" style="background-color: #28a745; color: white; border: none; padding: 12px 25px; margin: 10px; border-radius: 5px; font-size: 16px; cursor: pointer;">Agree</button>
            <button id="disagreeBtn" style="background-color: #dc3545; color: white; border: none; padding: 12px 25px; margin: 10px; border-radius: 5px; font-size: 16px; cursor: pointer;">Disagree</button>
        </div>
    </div>
    <script>
        document.getElementById('agreeBtn').addEventListener('click', function() {
            alert('Thank you for agreeing to the Security Policy.');
            window.location.href = 'index.php';
        });
        document.getElementById('disagreeBtn').addEventListener('click', function() {
            alert('You must agree to the Security Policy to use our services.');
            // Stay on the same page if disagree
        });
    </script>
<!-- <?php include 'includes/footer.php'; ?> -->
</body>
</html>
