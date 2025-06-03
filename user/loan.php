<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/session_manager.php';

redirectIfNotLoggedIn();

$userId  = $_SESSION['user_id'];
$error   = '';
$success = '';

// Fetch all of this user’s loans (ordered by creation date descending)
$stmt = $pdo->prepare("
    SELECT *
      FROM loans 
     WHERE user_id = ?
  ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$loans = $stmt->fetchAll();

// (Optional) Fetch user’s account balance (not used on this page, but left for reference)
$accountStmt = $pdo->prepare("
    SELECT balance 
      FROM accounts 
     WHERE user_id = ?
");
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
        $amount  = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $term    = intval($_POST['term']);
        $purpose = htmlspecialchars($_POST['purpose'], ENT_QUOTES, 'UTF-8');

        if ($amount < 100) {
            $error = "Minimum loan amount is ₱100";
        } elseif ($amount > 50000) {
            $error = "Maximum loan amount is ₱50,000";
        } elseif ($term < 1 || $term > 60) {
            $error = "Loan term must be between 1 and 60 months";
        } else {
            // Calculate interest rate
            $interestRate = 5.0;
            if ($amount > 10000) {
                $interestRate = 4.5;
            }
            if ($term > 36) {
                $interestRate += 1.0;
            }

            try {
                // Insert new loan application with status = 'pending'
                $stmt = $pdo->prepare("
                    INSERT INTO loans (
                        user_id, 
                        amount, 
                        interest_rate, 
                        term_months, 
                        status, 
                        purpose,
                        total_due,
                        is_paid,
                        penalty_amount
                    ) VALUES (
                        ?, ?, ?, ?, 'pending', ?, 
                        ?, 'no', 0.00
                    )
                ");
                // Note: I'm initializing total_due = amount (actual calculations can be updated later
                // once loan is approved and you compute real total_due including interest).
                // For now, assume total_due == amount on application.
                $stmt->execute([
                    $userId, 
                    $amount, 
                    $interestRate, 
                    $term, 
                    $purpose,
                    $amount
                ]);

                // Regenerate token to prevent double submissions
                $_SESSION['loan_token'] = bin2hex(random_bytes(32));

                $success = "Loan application submitted successfully!";
            } catch (Exception $e) {
                $error = "Failed to submit loan application: " . $e->getMessage();
            }
        }
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// FETCH “Quick Summary” VALUES
// ──────────────────────────────────────────────────────────────────────────────

// 1) Total Loan Balance & Current Active Loans
//    (approved, not yet paid)
$summaryStmt = $pdo->prepare("
    SELECT 
       COALESCE(SUM(total_due), 0)   AS total_balance,
       COUNT(*)                     AS active_loans
      FROM loans
     WHERE user_id = ?
       AND status = 'approved'
       AND is_paid = 'no'
");
$summaryStmt->execute([$userId]);
$summary = $summaryStmt->fetch();

$totalBalance = $summary['total_balance'];
$activeLoans  = $summary['active_loans'];

// 2) Total Pending Loan
$pendingStmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) AS pending_amount
      FROM loans
     WHERE user_id = ?
       AND status = 'pending'
");
$pendingStmt->execute([$userId]);
$pending = $pendingStmt->fetch();
$totalPending = $pending['pending_amount'];

// 3) Last Loan Taken (most recent created_at, regardless of status)
$lastLoanStmt = $pdo->prepare("
    SELECT amount, created_at
      FROM loans
     WHERE user_id = ?
  ORDER BY created_at DESC
     LIMIT 1
");
$lastLoanStmt->execute([$userId]);
$lastLoan = $lastLoanStmt->fetch();

// 4) Next Payment (simple estimate for “oldest approved loan”)
//    ─── You may want to adjust this logic if you have a separate payment schedule table. ───
//    Here: find the single loan with status='approved' AND is_paid='no' with the earliest approved_at.
$nextPaymentAmount = null;
$nextPaymentDate   = null;

$nextLoanStmt = $pdo->prepare("
    SELECT approved_at, total_due, term_months
      FROM loans
     WHERE user_id = ?
       AND status = 'approved'
       AND is_paid = 'no'
  ORDER BY approved_at ASC
     LIMIT 1
");
$nextLoanStmt->execute([$userId]);
$nextLoan = $nextLoanStmt->fetch();

if ($nextLoan && !empty($nextLoan['approved_at'])) {
    // Estimate: first installment = (total_due / term_months)
    $monthlyInstallment = floatval($nextLoan['total_due']) / max(1, intval($nextLoan['term_months']));

    // Estimate: first due date = 1 month after approved_at
    $approvedAt    = $nextLoan['approved_at'];
    $timestamp     = strtotime($approvedAt . ' +1 month');
    $nextDueString = date('M j, Y', $timestamp);

    $nextPaymentAmount = $monthlyInstallment;
    $nextPaymentDate   = $nextDueString;
}

// ──────────────────────────────────────────────────────────────────────────────
// FETCH LOGGED-IN USER INFO (for sidebar)
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
$stmt = $pdo->prepare("
    SELECT profile_picture 
      FROM users 
     WHERE user_id = ?
");
$stmt->execute([$userId]);
$picRow = $stmt->fetch();
$profilePic = $picRow['profile_picture']
    ? '../uploads/' . $picRow['profile_picture']
    : '../assets/images/default-avatars.png';


    // 5) Loan Application History Summary
//    Count total applications, how many approved, how many rejected
$appHistoryStmt = $pdo->prepare("
    SELECT 
      COUNT(*) AS total_apps,
      SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
      SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM loans
    WHERE user_id = ?
");
$appHistoryStmt->execute([$userId]);
$appHistory = $appHistoryStmt->fetch();

$totalApps     = $appHistory['total_apps'];
$approvedCount = $appHistory['approved_count'];
$rejectedCount = $appHistory['rejected_count'];


// 6) Overdue Payments (sum of penalty_amount on approved, unpaid loans)
$overdueStmt = $pdo->prepare("
    SELECT COALESCE(SUM(penalty_amount), 0) AS total_overdue
      FROM loans
     WHERE user_id = ?
       AND status = 'approved'
       AND is_paid = 'no'
       AND penalty_amount > 0
");
$overdueStmt->execute([$userId]);
$overdue     = $overdueStmt->fetch();
$totalOverdue = $overdue['total_overdue'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Loans</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/loans.css">

    <!-- NAVIGATION EFFECTS -->
    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>
</head>

<body>
    <div class="wrapper">

        <!-- ────────────────────────────────── SIDEBAR ────────────────────────────────── -->
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
                        data-hover="../assets/images/hover-dashboard.png"> 
                    Dashboard
                </a>
                <a href="deposit.php" class="btn">
                    <img 
                        src="../assets/images/inactive-deposit.png" 
                        alt="deposit-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-deposit.png"
                        data-hover="../assets/images/hover-deposit.png"> 
                    Deposit
                </a>
                <a href="withdraw.php" class="btn">
                    <img 
                        src="../assets/images/inactive-withdraw.png" 
                        alt="withdraw-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-withdraw.png"
                        data-hover="../assets/images/hover-withdraw.png"> 
                    Withdraw
                </a>
                <a href="transfer.php" class="btn">
                    <img 
                        src="../assets/images/inactive-transfer.png" 
                        alt="transfer-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transfer.png"
                        data-hover="../assets/images/hover-transfer.png"> 
                    Transfer
                </a>
                <a href="transactions.php" class="btn">
                    <img 
                        src="../assets/images/inactive-transaction.png" 
                        alt="transactions-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transaction.png"
                        data-hover="../assets/images/hover-transaction.png"> 
                    Transactions
                </a>
                <a href="investment.php" class="btn">
                    <img 
                        src="../assets/images/inactive-investment.png" 
                        alt="investment-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-investment.png"
                        data-hover="../assets/images/hover-investment.png"> 
                    Investment
                </a>
                <a href="loan.php" class="btn dash-text">
                    <img 
                        src="../assets/images/hover-loans.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/hover-loans.png"
                        data-hover="../assets/images/hover-loans.png"> 
                    Loans
                </a>
                <a href="profile.php" class="btn">
                    <img 
                        src="../assets/images/inactive-profile.png" 
                        alt="profile-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-profile.png"
                        data-hover="../assets/images/hover-profile.png"> 
                    Settings
                </a>
            </nav>
            <hr>
            <div class="logout-cont">
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </aside>
        <!-- ────────────────────────────────── END SIDEBAR ─────────────────────────────── -->

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

            <!-- ────────────────────────────────── APPLY FOR A LOAN ────────────────────────────── -->
            <div class="wrap">
                <div class="content">
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
                            <label>Loan Amount (₱)</label>
                            <input type="number" name="amount" min="100" max="50000" step="100" required>
                            <small class="form-text">Minimum: ₱100 | Maximum: ₱50,000</small>
                        </div>
                        <div class="form-group">
                            <label>Loan Term (months)</label>
                            <input type="number" name="term" min="1" max="60" required>
                            <small class="form-text">Term must be between 1 and 60 months</small>
                        </div>
                        <div class="form-group purpose">
                            <label>Purpose</label>
                            <textarea name="purpose" required></textarea>
                        </div>
                        <button type="submit" class="btn11">Apply for Loan</button>
                    </form>
                </div>

                <!-- ────────────────────────────────── QUICK SUMMARY (LOAN) ─────────────────────────── -->
                <div class="Summary">
                    <div style="width: 100%;">
                        <h2>Quick Summary</h2>
                                
                                <ul>
                                    <li>
                                    Total Loan Balance:
                                        <strong>₱<?= number_format($totalBalance, 2) ?></strong>
                                    </li>
                                    <li>
                                        Active Loans:
                                        <strong><?= $activeLoans ?></strong>
                                    </li>
                                    <li>
                                    Next Payment:
                                        <?php if ($nextPaymentAmount !== null && $nextPaymentDate !== null): ?>
                                            <strong>₱<?= number_format($nextPaymentAmount, 2) ?> due on <?= $nextPaymentDate ?></strong>
                                        <?php else: ?>
                                            <strong>N/A</strong>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                    Last Loan Taken:
                                        <?php if ($lastLoan): ?>
                                            <strong>₱<?= number_format($lastLoan['amount'], 2) ?> on <?= date('M j, Y', strtotime($lastLoan['created_at'])) ?></strong>
                                        <?php else: ?>
                                            <strong>N/A</strong>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        Pending Loans:
                                        <strong>₱<?= number_format($totalPending, 2) ?></strong>    
                                    </li>
                                    <li>
                                       Overdue Payments:
                                        <strong> ₱<?= number_format($totalOverdue, 2) ?></strong>
                                    </li>

                                    <li>
                                        Total Applications:
                                    <strong>  <?= $totalApps ?> 
                                    (<?= $approvedCount ?> approved, <?= $rejectedCount ?> rejected)</strong>
                                  
                                    </li>


                                </ul>

                                 <button class="btn_sum" href="loan-payment"> <a  href="loan-payment.php">Make a Loan Payment</a> </button>
                        
                    </div>
                </div>
                <!-- ────────────────────────────────── END QUICK SUMMARY ────────────────────────────── -->
            </div>

            <!-- ────────────────────────────────── YOUR LOANS LIST ─────────────────────────────── -->
            <div class="loan_list">
                <h2>Your Loans</h2>

                <?php if (empty($loans)): ?>
                    <p>You have no loans at the moment.</p>
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
                                <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                <td><?= $loan['interest_rate'] ?>%</td>
                                <td><?= $loan['term_months'] ?> months</td>
                                <td class="status-<?= $loan['status'] ?>">
                                    <?= ucfirst($loan['status']) ?>
                                </td>
                                <td>
                                    <?= date('M j, Y', strtotime($loan['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <!-- ────────────────────────────────── END YOUR LOANS LIST ─────────────────────────── -->

        </main>
    </div>

    <script src="../assets/js/session.js"></script>
</body>
</html>
