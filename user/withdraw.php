<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/otp.php';

redirectIfNotLoggedIn();  // Ensure user is logged in

$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$otpSent = false;
$balance = 0;

// Get user's account balance
$stmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetchColumn();

// Handle withdrawal form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['amount'])) {
        $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Validate amount
        if ($amount <= 0) {
            $error = "Amount must be greater than 0.";
        } elseif ($amount > $balance) {
            $error = "Insufficient balance.";
        } else {
            // Get user's email for OTP generation
            $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $email = $user['email'];

            // Generate OTP and send to email
            if ($email && generateOTP($email)) {
                // Store pending withdrawal in session
                $_SESSION['pending_withdrawal'] = [
                    'amount' => $amount
                ];

                // Redirect to OTP verification page
                header("Location: ../otp-verification.php?type=withdraw");
                exit();
            } else {
                $error = "Failed to send OTP. Please try again.";
            }
        }
    }
}

// Get user account information
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.*, a.account_number, a.balance 
    FROM users u 
    JOIN accounts a ON u.user_id = a.user_id 
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die('User account not found.');
}

// Check if the user has a profile picture
$stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
$profilePic = $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../assets/images/default-avatar.png';
// Fetch user's profile information

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus-Banksystem - Withdraw</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/withdraw.css">

    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        
       <div class="Logos-cont">
                    <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
                </div>
<hr>
                <div class="profile-container">
                    <img src="<?= $profilePic ?>" alt="Profile Picture" class="img-fluid">
                    <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p><?= htmlspecialchars($user['account_number']) ?></p>
                </div>
      <hr>
                <nav>
            <a href="dashboard.php" class="btn">
                <img src="../assets/images/inactive-dashboard.png" alt="dashboard-logo" class="nav-icon" data-default="../assets/images/inactive-dashboard.png" data-hover="../assets/images/hover-dashboard.png"> 
                Dashboard
            </a>

            <a href="deposit.php" class="btn">
                <img src="../assets/images/inactive-deposit.png" alt="deposit-logo" class="nav-icon" data-default="../assets/images/inactive-deposit.png" data-hover="../assets/images/hover-deposit.png"> 
                Deposit
            </a>

            <a href="withdraw.php" class="btn dash-text">
                <img src="../assets/images/hover-withdraw.png" alt="withdraw-logo" class="nav-icon" data-default="../assets/images/hover-withdraw.png" data-hover="../assets/images/hover-withdraw.png"> 
                Withdraw
            </a>

            <a href="transfer.php" class="btn">
                <img src="../assets/images/inactive-transfer.png" alt="transfer-logo" class="nav-icon" data-default="../assets/images/inactive-transfer.png" data-hover="../assets/images/hover-transfer.png"> 
                Transfer
            </a>

            <a href="transactions.php" class="btn">
                <img src="../assets/images/inactive-transaction.png" alt="transactions-logo" class="nav-icon" data-default="../assets/images/inactive-transaction.png" data-hover="../assets/images/hover-transaction.png"> 
                Transactions
            </a>

            <a href="investment.php" class="btn">
                <img src="../assets/images/inactive-investment.png" alt="investment-logo" class="nav-icon" data-default="../assets/images/inactive-investment.png" data-hover="../assets/images/hover-investment.png"> 
                Investment
            </a>

            <a href="loan.php" class="btn">
                <img src="../assets/images/inactive-loans.png" alt="loans-logo" class="nav-icon" data-default="../assets/images/inactive-loans.png" data-hover="../assets/images/hover-loans.png"> 
                Loans
            </a>
        </nav>       
<hr>
        <div class="logout-cont">
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </aside>

    <main class="container">
        <header>
            <h1>Withdraw Funds</h1>
            <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>

        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php">Deposit</a>
            <a href="withdraw.php" class="active">Withdraw</a>
            <a href="transfer.php">Transfer</a>
            <a href="transactions.php">Transactions</a>
        </nav>

        <div class="content">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="balance-info">
                <p>Current Balance: <strong>$<?= number_format($balance, 2) ?></strong></p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Amount to Withdraw</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>

                <button type="submit" class="btn">Withdraw</button>
            </form>
        </div>
    </main>
</div>
</body>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set session timeout to 10 minutes
        const inactivityTime = 600000;
        let inactivityTimer;

        const resetInactivityTimer = () => {
            // Clear existing timer
            if (inactivityTimer) clearTimeout(inactivityTimer);

            // Set timeout
            inactivityTimer = setTimeout(() => {
                window.location.href = '../logout.php?timeout=1';
            }, inactivityTime);
        };

        // Reset timer on user activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'submit'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Add form submit handler
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Reset timer but don't prevent form submission
                resetInactivityTimer();
            });
        }

        // Initial timer start
        resetInactivityTimer();
    });
    </script>
</html>
