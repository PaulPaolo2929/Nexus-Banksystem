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

// Fetch all transactions joined with accounts and users
$sql = "SELECT t.*, u.user_id, u.full_name, u.email 
        FROM transactions t
        JOIN accounts a ON t.account_id = a.account_id
        JOIN users u ON a.user_id = u.user_id
        ORDER BY t.created_at DESC";

$result = $conn->query($sql);

$transactions = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>All Transactions</title>
<link rel="stylesheet" href="../assets/css/admin-main.css">
<link rel="stylesheet" href="../assets/css/admin-all-transactions.css">
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
                        <td data-label="Related Account"><?php echo !empty($transaction['related_account_id']) ? htmlspecialchars($transaction['related_account_id']) : 'N/A'; ?></td>
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
   
</main>
</div>

</body>
</html>
