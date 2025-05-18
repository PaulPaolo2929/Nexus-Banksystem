<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "securebank";  // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch recent transactions with user info via accounts table
$sql = "
    SELECT 
        t.transaction_id, 
        t.account_id, 
        t.type, 
        t.amount, 
        t.description, 
        t.related_account_id, 
        t.created_at,
        u.user_id,
        u.full_name
    FROM transactions t
    JOIN accounts a ON t.account_id = a.account_id
    JOIN users u ON a.user_id = u.user_id
    ORDER BY t.created_at DESC
    LIMIT 10
";

$result = $conn->query($sql);

// Check if there are any transactions
if ($result->num_rows > 0) {
    // Output each transaction as an associative array
    $transactions = [];
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
} else {
    $transactions = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recent Transactions</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-recent-transactions.css">

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
            <h1>Manage Investments</h1>
            <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>

        <div class="content">
            <div>
        <h1>Recent Transactions</h1>

        <!-- Transaction Table -->
        <table>
            <thead>
                <tr style="background-color: #f4f4f4;">
                    <th>Transaction ID</th>
                    <th>Account ID</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Related Account ID</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td data-label="Transaction ID"><?php echo $transaction['transaction_id']; ?></td>
                            <td data-label="Account ID"><?php echo $transaction['account_id']; ?></td>
                            <td data-label="User ID"><?php echo $transaction['user_id']; ?></td>
                            <td data-label="User Name"><?php echo htmlspecialchars($transaction['full_name']); ?></td>
                            <td data-label="Type"><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td data-label="Amount"><?php echo "₱" . number_format($transaction['amount'], 2); ?></td>
                            <td data-label="Description"><?php echo !empty($transaction['description']) ? htmlspecialchars($transaction['description']) : 'N/A'; ?></td>
                            <td data-label="Related Account ID"><?php echo !empty($transaction['related_account_id']) ? $transaction['related_account_id'] : 'N/A'; ?></td>
                            <td data-label="Date">
                                <?php 
                                    echo !empty($transaction['created_at']) && strtotime($transaction['created_at']) 
                                        ? date("Y-m-d H:i:s", strtotime($transaction['created_at'])) 
                                        : 'N/A'; 
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No recent transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="all-transactions.php" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">All Transactions</a>
    </div>
        </div>
            
</main>
</div>
    
</body>
</html>
