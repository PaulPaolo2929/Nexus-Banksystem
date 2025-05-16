<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch available investment plans
$stmt = $pdo->query("SELECT * FROM investment_plans ORDER BY duration_months ASC");
$plans = $stmt->fetchAll();

// Fetch user's account balance
$stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();
if (!$account) {
    die("Account not found.");
}
$balance = $account['balance'];

// Handle new investment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_id'], $_POST['amount'])) {
    $planId = $_POST['plan_id'];
    $amount = (float) $_POST['amount'];

    $stmt = $pdo->prepare("SELECT * FROM investment_plans WHERE plan_id = ?");
    $stmt->execute([$planId]);
    $plan = $stmt->fetch();

    if (!$plan) {
        $error = "Invalid investment plan.";
    } elseif ($amount < $plan['min_amount']) {
        $error = "Amount must be at least $" . number_format($plan['min_amount'], 2);
    } elseif ($amount > $balance) {
        $error = "Insufficient balance.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE user_id = ?");
            $stmt->execute([$amount, $userId]);

            $stmt = $pdo->prepare("INSERT INTO investments (user_id, plan_id, amount, created_at, status) VALUES (?, ?, ?, NOW(), 'active')");
            $stmt->execute([$userId, $planId, $amount]);

            $pdo->commit();
            $_SESSION['success'] = "Investment of $" . number_format($amount, 2) . " placed in " . htmlspecialchars($plan['plan_name']) . "!";
            header("Location: investment.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error placing investment: " . $e->getMessage();
        }
    }
}

// Handle withdrawal of matured investment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw_investment_id'])) {
    $investmentId = $_POST['withdraw_investment_id'];

    $stmt = $pdo->prepare("
        SELECT inv.*, plans.interest_rate 
        FROM investments inv 
        JOIN investment_plans plans ON inv.plan_id = plans.plan_id
        WHERE inv.investment_id = ? AND inv.user_id = ? AND inv.status = 'matured' AND inv.withdrawn_at IS NULL
    ");
    $stmt->execute([$investmentId, $userId]);
    $investment = $stmt->fetch();

    if ($investment) {
        try {
            $pdo->beginTransaction();

            $totalReturn = $investment['amount'] + ($investment['amount'] * $investment['interest_rate'] / 100);

            $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE user_id = ?");
            $stmt->execute([$totalReturn, $userId]);

            $stmt = $pdo->prepare("UPDATE investments SET withdrawn_at = NOW(), status = 'withdrawn' WHERE investment_id = ?");
            $stmt->execute([$investmentId]);

            $pdo->commit();
            $_SESSION['success'] = "Successfully withdrawn $" . number_format($totalReturn, 2) . " from matured investment.";
            header("Location: investment.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Withdrawal failed: " . $e->getMessage();
        }
    } else {
        $error = "Invalid or already withdrawn investment.";
    }
}

// Update matured investments (once matured, mark them as such)
$stmt = $pdo->prepare("
    UPDATE investments inv
    JOIN investment_plans plans ON inv.plan_id = plans.plan_id
    SET inv.status = 'matured', inv.matured_at = NOW()
    WHERE inv.status = 'active' 
    AND DATE_ADD(inv.created_at, INTERVAL plans.duration_months MONTH) <= NOW()
");
$stmt->execute();

// Fetch user's investment history
$stmt = $pdo->prepare("
    SELECT inv.*, plans.plan_name, plans.interest_rate, plans.duration_months 
    FROM investments inv
    JOIN investment_plans plans ON inv.plan_id = plans.plan_id
    WHERE inv.user_id = ?
    ORDER BY inv.created_at DESC
");
$stmt->execute([$userId]);
$investments = $stmt->fetchAll();


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
    <title>SecureBank - Investments</title>
    <link rel="stylesheet" href="../assets/css/investment.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    
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

                    <a href="investment.php" class="btn dash-text">
                        <img 
                        src="../assets/images/hover-investment.png" 
                        alt="investment-logo" 
                        class="nav-icon"
                        data-default="../assets/images/hover-investment.png"
                        data-hover="../assets/images/hover-investment.png"
                        > 
                        Investment
                    </a>

                    <a href="loan.php" class="btn">
                        <img 
                        src="../assets/images/inactive-loans.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-loans.png"
                        data-hover="../assets/images/hover-loans.png"
                        > 
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
                    <h1>Investments</h1>
                    <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
                </header>

                <div class="content">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <h2>Make a New Investment</h2>
                    <form method="post">
                        <div class="form-group">
                            <label for="plan_id">Select Investment Plan:</label>
                            <select name="plan_id" id="plan_id" required>
                                <option value="">-- Choose a Plan --</option>
                                <?php foreach ($plans as $plan): ?>
                                    <option value="<?= $plan['plan_id'] ?>">
                                        <?= htmlspecialchars($plan['plan_name']) ?> - <?= $plan['interest_rate'] ?>% for <?= $plan['duration_months'] ?> months (Min: $<?= number_format($plan['min_amount'], 2) ?>)(Max: $<?= number_format($plan['max_amount'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount">Investment Amount ($):</label>
                            <input type="number" name="amount" id="amount" step="0.01" required>
                        </div>

                        <button type="submit" class="btn">Invest</button>
                    </form>

                    <h2>Your Investment History</h2>
                    <?php if (empty($investments)): ?>
                        <p>No investments yet.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Interest</th>
                                    <th>Duration</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($investments as $inv): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($inv['plan_name']) ?></td>
                                        <td>$<?= number_format($inv['amount'], 2) ?></td>
                                        <td><?= $inv['interest_rate'] ?>%</td>
                                        <td><?= $inv['duration_months'] ?> months</td>
                                        <td><?= date('Y-m-d', strtotime($inv['created_at'])) ?></td>
                                        <td>
                                            <?php if ($inv['status'] === 'matured'): ?>
                                                <span>Matured</span><br>
                                                <?php if (empty($inv['withdrawn_at'])): ?>
                                                    <form method="post">
                                                        <input type="hidden" name="withdraw_investment_id" value="<?= $inv['investment_id'] ?>">
                                                        <button type="submit" class="btn" style="margin-top: 5px;">Withdraw</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span>Withdrawn on <?= date('Y-m-d', strtotime($inv['withdrawn_at'])) ?></span>
                                                <?php endif; ?>
                                            <?php elseif ($inv['status'] === 'withdrawn'): ?>
                                                <span>Withdrawn</span><br>
                                                <?php if (!empty($inv['withdrawn_at'])): ?>
                                                    <span>on <?= date('Y-m-d', strtotime($inv['withdrawn_at'])) ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span>Active</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </main>
        </div>
</body>
</html>
