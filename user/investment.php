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

            $stmt = $pdo->prepare("UPDATE investments SET withdrawn_at = NOW() WHERE investment_id = ?");
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SecureBank - Investments</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container { max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select {
            width: 100%; padding: 8px;
        }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background-color: #4CAF50; color: white; }
        .alert-danger { background-color: #f44336; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .btn { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Investments</h1>
        <a href="../logout.php" class="logout">Logout</a>
    </header>

    <nav class="dashboard-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="deposit.php">Deposit</a>
        <a href="withdraw.php">Withdraw</a>
        <a href="transfer.php">Transfer</a>
        <a href="loan-payment.php">Loan Payment</a>
        <a href="transactions.php">Transactions</a>
    </nav>

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
                        <?= htmlspecialchars($plan['plan_name']) ?> - <?= $plan['interest_rate'] ?>% for <?= $plan['duration_months'] ?> months (Min: $<?= number_format($plan['min_amount'], 2) ?>)
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
                                <?php if ($inv['withdrawn_at'] === null): ?>
                                    <form method="post">
                                        <input type="hidden" name="withdraw_investment_id" value="<?= $inv['investment_id'] ?>">
                                        <button type="submit" class="btn" style="margin-top: 5px;">Withdraw</button>
                                    </form>
                                <?php else: ?>
                                    <span>Withdrawn on <?= date('Y-m-d', strtotime($inv['withdrawn_at'])) ?></span>
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
</body>
</html>
