<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user's loans
$stmt = $pdo->prepare("
    SELECT * FROM loans 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$loans = $stmt->fetchAll();

// Fetch balance from the accounts table (optional - not currently used in this page)
$accountStmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$accountStmt->execute([$userId]);
$account = $accountStmt->fetch();
$balance = $account ? $account['balance'] : 0;

// Generate and store CSRF token to prevent double submissions
if (empty($_SESSION['loan_token'])) {
    $_SESSION['loan_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['loan_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['loan_token']) {
        $error = "Duplicate submission or invalid token.";
    } else {
        // Sanitize and validate input
        $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $term = intval($_POST['term']);
        $purpose = htmlspecialchars($_POST['purpose'], ENT_QUOTES, 'UTF-8');

        if ($amount < 100) {
            $error = "Minimum loan amount is $100";
        } elseif ($term < 1 || $term > 60) {
            $error = "Loan term must be between 1 and 60 months";
        } else {
            // Calculate interest rate
            $interestRate = 5.0;
            if ($amount > 10000) $interestRate = 4.5;
            if ($term > 36) $interestRate += 1.0;

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO loans (user_id, amount, interest_rate, term_months, status, purpose)
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $stmt->execute([$userId, $amount, $interestRate, $term, $purpose]);

                // Regenerate token to prevent double submissions
                $_SESSION['loan_token'] = bin2hex(random_bytes(32));

                $success = "Loan application submitted successfully!";
            } catch (Exception $e) {
                $error = "Failed to submit loan application: " . $e->getMessage();
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
    <title>SecureBank - Loans</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/loans.css">

    <!-- NAVIGATION EFFECTS -->
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
                        <img 
                        src="../assets/images/inactive-dashboard.png" 
                        alt="dashboard-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-dashboard.png"
                        data-hover="../assets/images/hover-dashboard.png"
                        > 
                        Dashboard
                    </a>

                    <a href="deposit.php" class="btn">
                        <img 
                        src="../assets/images/inactive-deposit.png" 
                        alt="deposit-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-deposit.png"
                        data-hover="../assets/images/hover-deposit.png"
                        > 
                        Deposit
                    </a>

                    <a href="withdraw.php" class="btn">
                        <img 
                        src="../assets/images/inactive-withdraw.png" 
                        alt="withdraw-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-withdraw.png"
                        data-hover="../assets/images/hover-withdraw.png"
                        > 
                        Withdraw
                    </a>

                    <a href="transfer.php" class="btn">
                        <img 
                        src="../assets/images/inactive-transfer.png" 
                        alt="transfer-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transfer.png"
                        data-hover="../assets/images/hover-transfer.png"
                        > 
                        Transfer
                    </a>

                    <a href="transactions.php" class="btn">
                        <img 
                        src="../assets/images/inactive-transaction.png" 
                        alt="transactions-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transaction.png"
                        data-hover="../assets/images/hover-transaction.png"
                        > 
                        Transactions
                    </a>

                    <a href="investment.php" class="btn">
                        <img 
                        src="../assets/images/inactive-investment.png" 
                        alt="investment-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-investment.png"
                        data-hover="../assets/images/hover-investment.png"
                        > 
                        Investment
                    </a>

                    <a href="loan.php" class="btn dash-text">
                        <img 
                        src="../assets/images/hover-loans.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/hover-loans.png"
                        data-hover="../assets/images/hover-loans.png"
                        > 
                        Loans
                    </a>

                    <a href="profile.php" class="btn">
                        <img 
                        src="../assets/images/inactive-profile.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-profile.png"
                        data-hover="../assets/images/inactive-profile"
                        > 
                        Settings
                    </a>
                </nav>       
 <hr>
            <div class="logout-cont">
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </aside>
    
        <main class="container">
            <header>
                <h1>Loan Management</h1>
                <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
            </header>

            <nav class="dashboard-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="deposit.php">Deposit</a>
                <a href="withdraw.php">Withdraw</a>
                <a href="transfer.php">Transfer</a>
                <a href="transactions.php">Transactions</a>
            </nav>

            <main class="content">
                    <h2>Apply for a Loan</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="form-group">
                            <label>Loan Amount ($)</label>
                            <input type="number" name="amount" min="100" step="100" required>
                        </div>
                        <div class="form-group">
                            <label>Loan Term (months)</label>
                            <input type="number" name="term" min="1" max="60" required>
                        </div>
                        <div class="form-group">
                            <label>Purpose</label>
                            <textarea name="purpose" required></textarea>
                        </div>
                        <button type="submit" class="btn">Apply for Loan</button>
                    </form>

                    <h2>Your Loans</h2>

                    <?php if (empty($loans)): ?>
                        <p>You have no active loans.</p>
                    <?php else: ?>
                        <table class="loans-table">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Interest Rate</th>
                                    <th>Term</th>
                                    <th>Status</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td>$<?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= $loan['interest_rate'] ?>%</td>
                                    <td><?= $loan['term_months'] ?> months</td>
                                    <td class="status-<?= $loan['status'] ?>"><?= ucfirst($loan['status']) ?></td>
                                    <td><?= date('M j, Y', strtotime($loan['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
            </>
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
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Initial timer start
        resetInactivityTimer();
    });
    </script>
</html>
