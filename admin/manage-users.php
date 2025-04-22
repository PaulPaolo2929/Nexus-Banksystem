<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotAdmin();

// Accept user
if (isset($_GET['accept']) && is_numeric($_GET['accept'])) {
    $userId = $_GET['accept'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE user_id = ?");
        $stmt->execute([$userId]);

        $accountNumber = generateAccountNumber();

        $stmt = $pdo->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, 0)");
        $stmt->execute([$userId, $accountNumber]);

        $pdo->commit();
        $_SESSION['success'] = "User approved and account created.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to approve user: " . $e->getMessage();
    }

    header("Location: manage-users.php");
    exit();
}

// Reject user (deletes user)
if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $userId = $_GET['reject'];

    try {
        $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$userId]);
        $_SESSION['success'] = "User rejected and deleted.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to reject user: " . $e->getMessage();
    }

    header("Location: manage-users.php");
    exit();
}

// Delete user manually
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = $_GET['delete'];

    // Check user's balance first
    $stmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $account = $stmt->fetch();

    if (!$account || $account['balance'] > 0) {
        $_SESSION['error'] = "Cannot delete user. Balance must be 0.";
        header("Location: manage-users.php");
        exit();
    }

    try {
        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM accounts WHERE user_id = ?")->execute([$userId]);
        $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$userId]);

        $pdo->commit();
        $_SESSION['success'] = "User deleted successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to delete user: " . $e->getMessage();
    }

    header("Location: manage-users.php");
    exit();
}

// Get all users
$users = $pdo->query("
    SELECT u.*, a.account_number, a.balance 
    FROM users u 
    LEFT JOIN accounts a ON u.user_id = a.user_id 
    ORDER BY u.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Manage Users</h1>
        <a href="../logout.php" class="logout">Logout</a>
    </header>

    <nav class="dashboard-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage-users.php" class="active">Manage Users</a>
        <a href="manage-loans.php">Manage Loans</a>
    </nav>

    <div class="content">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <h2>All Users</h2>

        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table class="users-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Account</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Joined On</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['account_number'] ?: 'N/A' ?></td>
                        <td>$<?= $user['balance'] ? number_format($user['balance'], 2) : '0.00' ?></td>
                        <td>
                            <?= $user['status'] === 'approved' ? '✅ Approved' : '⏳ Pending' ?>
                        </td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['status'] !== 'approved'): ?>
                                <a href="manage-users.php?accept=<?= $user['user_id'] ?>" class="btn btn-sm btn-success">Accept</a>
                                <a href="manage-users.php?reject=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject and delete this user?')">Reject</a>
                            <?php endif; ?>
                            <?php if ($user['balance'] == 0): ?>
                                <a href="manage-users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <?php else: ?>
                                <span class="btn btn-sm btn-secondary" title="User must have zero balance to delete">🔒 Can't Delete</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
