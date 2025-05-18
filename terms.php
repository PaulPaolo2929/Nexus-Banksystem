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
    <title>Terms and Conditions - Nexus Bank</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
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
            background: url('assets/images/sticker-terms.png') no-repeat center center/contain;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sticker"></div>
        <h1>Terms and Conditions</h1>
        <p>Welcome to Nexus Bank. By using our services, you agree to the following terms and conditions.</p>
        <p>1. Account Usage: You agree to use your account responsibly and keep your credentials confidential.</p>
        <p>2. Transactions: All transactions are subject to verification and approval by Nexus Bank.</p>
        <p>3. Privacy: We respect your privacy and handle your data according to our Privacy Policy.</p>
        <p>4. Liability: Nexus Bank is not liable for any unauthorized access or loss of funds due to negligence.</p>
        <p>5. Changes: We reserve the right to update these terms at any time. Continued use of our services constitutes acceptance of changes.</p>
        <p>For full details, please contact our support team.</p>
    </div>
    <div style="max-width: 900px; margin: 20px auto; text-align: center;">
        <button id="agreeBtn" style="background-color: #28a745; color: white; border: none; padding: 12px 25px; margin: 10px; border-radius: 5px; font-size: 16px; cursor: pointer;">Agree</button>
        <button id="disagreeBtn" style="background-color: #dc3545; color: white; border: none; padding: 12px 25px; margin: 10px; border-radius: 5px; font-size: 16px; cursor: pointer;">Disagree</button>
    </div>
    <script>
        document.getElementById('agreeBtn').addEventListener('click', function() {
            alert('Thank you for agreeing to the Terms and Conditions.');
            window.location.href = 'index.php';
        });
        document.getElementById('disagreeBtn').addEventListener('click', function() {
            alert('You must agree to the Terms and Conditions to use our services.');
            // Stay on the same page if disagree
        });
    </script>
</body>
</html>
