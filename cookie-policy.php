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
    <title>Cookie Policy - Nexus Bank</title>
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
            bottom: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: url('assets/images/sticker-cookie.png') no-repeat center center/contain;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sticker"></div>
        <h1>Cookie Policy</h1>
        <p>Our website uses cookies to improve your browsing experience and provide personalized services.</p>
        <p>1. What Are Cookies: Small text files stored on your device to remember preferences and activity.</p>
        <p>2. Types of Cookies: We use essential, performance, and targeting cookies.</p>
        <p>3. Managing Cookies: You can control cookie settings through your browser preferences.</p>
        <p>4. Third-Party Cookies: We may allow third-party cookies for analytics and advertising.</p>
        <p>5. Consent: By using our site, you consent to our use of cookies as described in this policy.</p>
        <p>For questions, please contact our support team.</p>
    </div>
</body>
</html>
