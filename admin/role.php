<?php 
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

try {
    // Fetch all users with their current role (admin or user) with pagination
    $perPage = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;
    $totalCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalPages = ceil($totalCount / $perPage);
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, is_admin FROM users ORDER BY user_id LIMIT :perPage OFFSET :offset");
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    die("An error occurred while fetching user data. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-role.css">

    <script src="../assets/js/sidebar.js"></script>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="Logos-cont">
                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
            </div>

            <nav class="dashboard-nav">
                <a href="dashboard.php" class="btn">Dashboard</a>
                <a href="manage-users.php" class="btn">Manage Users</a>
                <a href="manage-loans.php" class="btn">Manage Loans</a>
                <a href="manage-investments.php" class="btn">Manage Investments</a>
                <a href="track-investments.php" class="btn">Users Investments</a>
                <a href="role.php" class="btn dash-text">Roles</a>
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
                <h1>Manage User Roles</h1>
                <button class="hamburger">&#9776;</button>
            </header>

            <div class="content">
                <h2>User Roles</h2>

                <?php if (empty($users)): ?>
                    <p>No users found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td data-label="User ID"><?= htmlspecialchars($user['user_id']) ?></td>
                                    <td data-label="Full Name"><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                                    <td data-label="Role"><?= $user['is_admin'] == 1 ? 'Admin' : 'User' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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
