<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/otp.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$weeklyLimit = 100000.00;

// Get user's account and balance
$stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();

if ($account) {
    $accountId = $account['account_id'];
    $balance = $account['balance'];

    // Total deposited in last 7 days
    $stmt = $pdo->prepare("
        SELECT SUM(amount) FROM transactions 
        WHERE account_id = ? AND type = 'deposit' AND created_at >= NOW() - INTERVAL 7 DAY
    ");
    $stmt->execute([$accountId]);
    $weeklyDeposits = $stmt->fetchColumn() ?: 0;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['amount'])) {
            $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            if ($amount <= 0) {
                $error = "Amount must be greater than 0.";
            } elseif (($weeklyDeposits + $amount) > $weeklyLimit) {
                $remaining = $weeklyLimit - $weeklyDeposits;
                $error = "Weekly deposit limit exceeded. You can only deposit $" . number_format($remaining, 2) . " more this week.";
            } else {
                // Get user's email
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                $email = $user['email'];

                // Generate and send OTP
                if ($email && generateOTP($email)) {
                    $_SESSION['pending_deposit'] = [
                        'account_id' => $accountId,
                        'amount' => $amount
                    ];

                    header("Location: ../otp-verification.php?type=deposit");
                    exit();
                } else {
                    $error = "Failed to send OTP. Please try again later.";
                }
            }
        }
    }
} else {
    $error = "Account not found.";
    $balance = 0;
    $weeklyDeposits = 0;
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
    <title>Nexus-Banksystem - Deposit</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/deposit.css">
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
                <img src="../assets/images/inactive-dashboard.png" alt="dashboard-logo" class="nav-icon"
                     data-default="../assets/images/inactive-dashboard.png"
                     data-hover="../assets/images/hover-dashboard.png"> 
                Dashboard
            </a>
            <a href="deposit.php" class="btn dash-text">
                <img src="../assets/images/hover-deposit.png" alt="deposit-logo" class="nav-icon"
                     data-default="../assets/images/hover-deposit.png"
                     data-hover="../assets/images/hover-deposit.png"> 
                Deposit
            </a>
            <a href="withdraw.php" class="btn">
                <img src="../assets/images/inactive-withdraw.png" alt="withdraw-logo" class="nav-icon"
                     data-default="../assets/images/inactive-withdraw.png"
                     data-hover="../assets/images/hover-withdraw.png"> 
                Withdraw
            </a>
            <a href="transfer.php" class="btn">
                <img src="../assets/images/inactive-transfer.png" alt="transfer-logo" class="nav-icon"
                     data-default="../assets/images/inactive-transfer.png"
                     data-hover="../assets/images/hover-transfer.png"> 
                Transfer
            </a>
            <a href="transactions.php" class="btn">
                <img src="../assets/images/inactive-transaction.png" alt="transactions-logo" class="nav-icon"
                     data-default="../assets/images/inactive-transaction.png"
                     data-hover="../assets/images/hover-transaction.png"> 
                Transactions
            </a>
            <a href="investment.php" class="btn">
                <img src="../assets/images/inactive-investment.png" alt="investment-logo" class="nav-icon"
                     data-default="../assets/images/inactive-investment.png"
                     data-hover="../assets/images/hover-investment.png"> 
                Investment
            </a>
            <a href="loan.php" class="btn">
                <img src="../assets/images/inactive-loans.png" alt="loans-logo" class="nav-icon"
                     data-default="../assets/images/inactive-loans.png"
                     data-hover="../assets/images/hover-loans.png"> 
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
            <h1>Deposit Funds</h1>
            <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>

        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php" class="active">Deposit</a>
            <a href="withdraw.php">Withdraw</a>
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
                <p>Deposited this week: <strong>$<?= number_format($weeklyDeposits, 2) ?></strong> / $100,000 limit</p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Amount to Deposit</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>

                <button type="submit" class="btn">Deposit</button>
            </form>
        </div>
    </main>
</div>
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
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Initial timer start
        resetInactivityTimer();
    });
</script>
</body>
</html>
