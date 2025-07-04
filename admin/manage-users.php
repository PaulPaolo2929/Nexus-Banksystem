<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/notification.php'; // NEW

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

        // Send email notification
        $stmt = $pdo->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user) {
            $email = $user['email'];
            $name = $user['full_name'];

            $subject = "Your Nexus Bank Account Has Been Approved";
            $body = "<p>Hi <strong>$name</strong>,</p>
                     <p>Your Nexus Bank account has been <strong>approved</strong> and is now active. You can now log in and use our services.</p>
                     <p>Thank you for joining Nexus Bank!</p>";

            sendNotification($email, $subject, $body);
        }

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

    $stmt = $pdo->prepare("SELECT balance, email, full_name FROM accounts LEFT JOIN users ON accounts.user_id = users.user_id WHERE users.user_id = ?");
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

        // Send email notification for account deletion
        $email = $account['email'];
        $name = $account['full_name'];

        $subject = "Your Nexus Bank Account Has Been Deleted";
        $body = "<p>Hi <strong>$name</strong>,</p>
                 <p>Your Nexus Bank account has been <strong>deleted</strong> by an administrator. All associated data has been removed from our system.</p>
                 <p>If you believe this is a mistake, please contact our support team immediately.</p>";

        sendNotification($email, $subject, $body);

        $_SESSION['success'] = "User deleted successfully and notified.";
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

        // Send activation/deactivation email
        $stmt = $pdo->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user) {
            $email = $user['email'];
            $name = $user['full_name'];
            $statusText = $newStatus ? 'activated' : 'deactivated';

            $subject = "Your Nexus Bank Account Has Been $statusText";
            $body = "<p>Hi <strong>$name</strong>,</p>
                     <p>Your Nexus Bank account has been <strong>$statusText</strong> by an administrator.</p>
                     <p>If you have any concerns, please contact support.</p>";

            sendNotification($email, $subject, $body);
        }

        $_SESSION['success'] = $newStatus ? "User account activated." : "User account deactivated.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to change activation status: " . $e->getMessage();
    }

    header("Location: manage-users.php");
    exit();
}

// Fetch all users
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$totalCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = ceil($totalCount / $perPage);
$users = $pdo->prepare("
    SELECT u.*, a.account_number, a.balance, iv.id_type, iv.id_file_path, iv.verification_status
    FROM users u 
    LEFT JOIN accounts a ON u.user_id = a.user_id 
    LEFT JOIN id_verifications iv ON u.user_id = iv.user_id
    ORDER BY u.created_at DESC
    LIMIT :perPage OFFSET :offset
");
$users->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$users->bindValue(':offset', $offset, PDO::PARAM_INT);
$users->execute();
$users = $users->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nexus Bank - Manage Users</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-users.css">

       <script src="../assets/js/sidebar.js"></script>
</head>
<body>
    <div class="wrapper">
            <aside class="sidebar">
                        
                            <div class="Logos-cont">
                                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
                            </div>

                            <nav class="dashboard-nav">
                                <a href="dashboard.php" class="active btn ">Dashboard</a>
                                <a href="manage-users.php" class="btn dash-text">Manage Users</a>
                                <a href="manage-loans.php" class="btn">Manage Loans</a>
                                <a href="manage-investments.php" class="btn">Manage Investments</a>
                                <a href="track-investments.php" class="btn">Users Investments</a>
                                <a href="role.php" class="btn">Roles</a>
                                <a href="recent_transactions.php" class="btn">Transactions</a>
                                <a href="loan-history.php" class="btn">Loan History</a>
                                <a href="login-records.php" class="btn">Login Records</a>
                                <a href="manage-messages.php" class="btn">Contact Messages</a>
                            </nav>

                             <div class="logout-cont">
                                <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>


<main class="container">
    <header>
        <h1>Manage Users</h1>
        <a href="../logout.php" class="logout">Logout</a>
        <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
    </header>

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

        <div class="table-cont">
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
                        <th>ID Verification</th>
                        <th>Active</th>
                        <th>Joined On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="Name"><?= htmlspecialchars($user['full_name']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                            <td data-label="Account"><?= $user['account_number'] ?: 'N/A' ?></td>
                            <td data-label="Balance">₱<?= number_format($user['balance'] ?? 0, 2) ?></td>
                            <td data-label="Status"><?= $user['status'] === 'approved' ? '✅ Approved' : '⏳ Pending' ?></td>
                            <td data-label="ID Verification">
                                <?php if ($user['id_file_path']): ?>
                                    <span class="id-status <?= $user['verification_status'] ?>">
                                        <?= ucfirst($user['verification_status'] ?? 'pending') ?>
                                    </span>
                                    <a href="view-id.php?user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-info">View ID</a>
                                <?php else: ?>
                                    <span class="id-status pending">No ID Uploaded</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Active"><?= $user['is_active'] ? '🟢 Active' : '🔴 Inactive' ?></td>
                            <td data-label="Joined On"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td data-label="Actions">
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
        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <style>
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a { display: inline-block; margin: 0 4px; padding: 6px 12px; color: #007bff; background: #fff; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; transition: background 0.2s, color 0.2s; }
        .pagination a.btn-primary, .pagination a.active { background: #007bff; color: #fff; border-color: #007bff; pointer-events: none; }
        .pagination a:hover:not(.btn-primary):not(.active) { background: #f0f0f0; }
        </style>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'btn-primary active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

</div>
</body>
</html>
