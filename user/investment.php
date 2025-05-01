<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch matured investments that are eligible for withdrawal
$stmt = $pdo->prepare("
    SELECT inv.investment_id, inv.amount, inv.interest_rate, inv.created_at, plans.plan_name
    FROM investments inv
    JOIN investment_plans plans ON inv.plan_id = plans.plan_id
    WHERE inv.user_id = ? AND inv.status = 'matured'
");
$stmt->execute([$userId]);
$maturedInvestments = $stmt->fetchAll();

// Handle withdrawal request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw_id'])) {
    $investmentId = $_POST['withdraw_id'];

    // Fetch investment details
    $stmt = $pdo->prepare("
        SELECT inv.*, plans.interest_rate, plans.duration_months 
        FROM investments inv
        JOIN investment_plans plans ON inv.plan_id = plans.plan_id
        WHERE inv.investment_id = ? AND inv.user_id = ?
    ");
    $stmt->execute([$investmentId, $userId]);
    $investment = $stmt->fetch();

    if (!$investment) {
        $error = "Investment not found.";
    } elseif ($investment['status'] !== 'matured') {
        $error = "Investment has not matured yet.";
    } else {
        try {
            // Begin transaction
            $pdo->beginTransaction();

            // Calculate total amount (investment + interest)
            $totalAmount = $investment['amount'] + ($investment['amount'] * $investment['interest_rate'] / 100);

            // Update user's account balance
            $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE user_id = ?");
            $stmt->execute([$totalAmount, $userId]);

            // Update investment status to 'withdrawn'
            $stmt = $pdo->prepare("UPDATE investments SET status = 'withdrawn', matured_at = NOW() WHERE investment_id = ?");
            $stmt->execute([$investmentId]);

            // Commit transaction
            $pdo->commit();
            $_SESSION['success'] = "Withdrawal of $" . number_format($totalAmount, 2) . " successful!";
            header("Location: investment.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error processing withdrawal: " . $e->getMessage();
        }
    }
}

// Fetch user's investments to show status and options for withdrawal
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
        .container {
            max-width: 800px;
            margin: auto;
        }
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
        <a href="investment.php" class="active">Investment</a>
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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($investments as $inv): ?>
                    <tr>
                        <td><?= htmlspecialchars($inv['plan_name']) ?></td>
                        <td>$<?= number_format($inv['amount'], 2) ?></td>
                        <td><?= $inv['interest_rate'] ?>%</td>
                        <td><?= $inv['duration_months'] ?> months</td>
                        <td><?= $inv['status'] ?></td>
                        <td>
                            <?php if ($inv['status'] === 'matured'): ?>
                                <form method="post" action="investment.php" style="display:inline;">
                                    <input type="hidden" name="withdraw_id" value="<?= $inv['investment_id'] ?>">
                                    <button type="submit" class="btn">Withdraw</button>
                                </form>
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
