<?php 
// Include database connection
require_once '../includes/db.php';

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

try {
    // Count total transactions
    $countSql = "SELECT COUNT(*) FROM transactions";
    $totalTransactions = $pdo->query($countSql)->fetchColumn();
    $totalPages = ceil($totalTransactions / $perPage);

    // Fetch paginated transactions joined with accounts and users, and related account number
    $sql = "SELECT t.*, u.user_id, u.full_name, u.email, ra.account_number AS related_account_number 
            FROM transactions t
            JOIN accounts a ON t.account_id = a.account_id
            JOIN users u ON a.user_id = u.user_id
            LEFT JOIN accounts ra ON t.related_account_id = ra.account_id
            ORDER BY t.created_at DESC
            LIMIT :perPage OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching transactions: " . $e->getMessage());
    die("Error fetching transactions. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>All Transactions</title>
<link rel="stylesheet" href="../assets/css/admin-main.css">
<link rel="stylesheet" href="../assets/css/admin-all-transactions.css">

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
                                <a href="manage-users.php" class="btn ">Manage Users</a>
                                <a href="manage-loans.php" class="btn">Manage Loans</a>
                                <a href="manage-investments.php" class="btn">Manage Investments</a>
                                <a href="track-investments.php" class="btn">Users Investments</a>
                                <a href="role.php" class="btn">Roles</a>
                                <a href="recent_transactions.php" class="btn dash-text">Transactions</a>
                                <a href="loan-history.php" class="btn">Loan History</a>
                                <a href="login-records.php" class="btn">Login Records</a>
                            </nav>

                             <div class="logout-cont">
                                <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>

<main class="container">
  <header>
    <h1>All Transactions</h1>
    <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
  </header>
     <div class="content">
        <h3>All Transactions</h3>
        <br>
    <div class="table-cont">
     <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User ID</th>
                <th>User Full Name</th>
                <th>User Email</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Related Account</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td data-label="Transaction ID"><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                        <td data-label="User ID"><?php echo htmlspecialchars($transaction['user_id']); ?></td>
                        <td data-label="User Full Name"><?php echo htmlspecialchars($transaction['full_name']); ?></td>
                        <td data-label="User Email"><?php echo htmlspecialchars($transaction['email']); ?></td>
                        <td data-label="Type"><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td data-label="Amount"><?php echo "₱" . number_format($transaction['amount'], 2); ?></td>
                        <td data-label="Description"><?php echo !empty($transaction['description']) ? htmlspecialchars($transaction['description']) : 'N/A'; ?></td>
                        <td data-label="Related Account"><?php echo !empty($transaction['related_account_number']) ? htmlspecialchars($transaction['related_account_number']) : 'N/A'; ?></td>
                        <td data-label="Date"><?php echo !empty($transaction['created_at']) ? date("Y-m-d H:i:s", strtotime($transaction['created_at'])) : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding: 20px;">No transactions found.</td>
                </tr>
            <?php endif; ?>
            </div>
        </tbody>
    </table>
</div>
<!-- Pagination Controls -->
<?php if ($totalPages > 1): ?>
<style>
.pagination {
    text-align: center;
    margin: 20px 0;
}
.pagination a {
    display: inline-block;
    margin: 0 4px;
    padding: 6px 12px;
    color: #007bff;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
}
.pagination a.btn-primary, .pagination a.active {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
    pointer-events: none;
}
.pagination a:hover:not(.btn-primary):not(.active) {
    background: #f0f0f0;
}
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
   
</main>
</div>

</body>
</html>
