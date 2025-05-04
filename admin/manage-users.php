<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

// Accept user (Approve and create an account)
if (isset($_GET['accept']) && is_numeric($_GET['accept'])) {
    $userId = $_GET['accept'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE user_id = ?");
        if (!$stmt->execute([$userId])) {
            throw new Exception("Failed to update user status.");
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE user_id = ?");
        $stmt->execute([$userId]);
        $hasAccount = $stmt->fetchColumn();

        if (!$hasAccount) {
            $accountNumber = generateUniqueAccountNumber($pdo);
            $stmt = $pdo->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, 0)");
            if (!$stmt->execute([$userId, $accountNumber])) {
                throw new Exception("Failed to create account for user.");
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "User approved and account created.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to approve user: " . $e->getMessage();
    }

    header("Location: manage-users.php");
    exit();
}

// Reject user
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

// Delete user (only if balance is 0)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = $_GET['delete'];

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

// Toggle user activation (Deactivation/Activation)
if (isset($_GET['toggle_active']) && is_numeric($_GET['toggle_active'])) {
    $userId = $_GET['toggle_active'];
    $newStatus = $_GET['status'] == '1' ? 0 : 1;

    try {
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
        $stmt->execute([$newStatus, $userId]);

        $_SESSION['success'] = $newStatus ? "User account activated." : "User account deactivated.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to change activation status: " . $e->getMessage();
    }

    header("Location: manage-users.php");
    exit();
}

// Fetch all users
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
    <title>SecureBank - Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        h1 {
            text-align: center;
        }
        .dashboard-nav {
            text-align: center;
            margin: 20px;
        }
        .dashboard-nav a {
            margin: 0 10px;
            text-decoration: none;
            color: blue;
        }
        .dashboard-nav a.active {
            font-weight: bold;
            color: darkblue;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Manage Users</h1>
        <a href="../logout.php" class="logout">Logout</a>
    </header>

    <nav class="dashboard-nav">
            <a href="manage-users.php">Manage Users</a>
            <a href="manage-loans.php">Manage Loans</a>
            <a href="manage-investments.php">Manage Investments</a>
            <a href="track-investments.php">Users Investments</a>
            <a href="role.php">Roles</a>
            <a href="recent_transactions.php">Transactions</a>
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
                        <th>Active</th>
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
                            <td>$<?= number_format($user['balance'] ?? 0, 2) ?></td>
                            <td><?= $user['status'] === 'approved' ? '✅ Approved' : '⏳ Pending' ?></td>
                            <td><?= $user['is_active'] ? '🟢 Active' : '🔴 Inactive' ?></td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['status'] !== 'approved'): ?>
                                    <a href="manage-users.php?accept=<?= $user['user_id'] ?>" class="btn btn-sm btn-success">Accept</a>
                                    <a href="manage-users.php?reject=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject and delete this user?')">Reject</a>
                                <?php endif; ?>

                                <?php if ($user['status'] === 'approved'): ?>
                                    <?php if ($user['is_active']): ?>
                                        <a href="manage-users.php?toggle_active=<?= $user['user_id'] ?>&status=1" class="btn btn-sm btn-warning" onclick="return confirm('Deactivate this user?')">Deactivate</a>
                                    <?php else: ?>
                                        <a href="manage-users.php?toggle_active=<?= $user['user_id'] ?>&status=0" class="btn btn-sm btn-success" onclick="return confirm('Activate this user?')">Activate</a>
                                    <?php endif; ?>

                                    <?php if ($user['balance'] == 0): ?>
                                        <a href="manage-users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    <?php endif; ?>
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
