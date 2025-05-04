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

// SQL query to fetch recent transactions (sorted by the latest date)
$sql = "SELECT * FROM transactions ORDER BY created_at DESC LIMIT 10";  // Get the 10 most recent transactions
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Transactions</title>
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
    <style>
        dashboard-nav{

        }
        </style>
    <!-- Navigation Menu -->
<nav class="dashboard-nav">
            <a href="manage-users.php">Manage Users</a>
            <a href="manage-loans.php">Manage Loans</a>
            <a href="manage-investments.php">Manage Investments</a>
            <a href="track-investments.php">Users Investments</a>
            <a href="role.php">Roles</a>
            <a href="recent_transactions.php">Transactions</a>
</nav>

<div style="max-width: 1000px; margin: 0 auto; padding: 20px; text-align: center;">
    <h1>Recent Transactions</h1>

    <!-- Transaction Table -->
    <table style="width: 100%; border-collapse: collapse; margin: 0 auto; text-align: left;">
        <thead>
            <tr style="background-color: #f4f4f4;">
                <th style="border: 1px solid #ccc; padding: 10px;">Transaction ID</th>
                <th style="border: 1px solid #ccc; padding: 10px;">Account ID</th>
                <th style="border: 1px solid #ccc; padding: 10px;">Type</th>
                <th style="border: 1px solid #ccc; padding: 10px;">Amount</th>
                <th style="border: 1px solid #ccc; padding: 10px;">Description</th>
                <th style="border: 1px solid #ccc; padding: 10px;">Related Account ID</th>
                <th style="border: 1px solid #ccc; padding: 10px;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo $transaction['transaction_id']; ?></td>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo $transaction['account_id']; ?></td>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo "₱" . number_format($transaction['amount'], 2); ?></td>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo htmlspecialchars($transaction['description']); ?></td>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo $transaction['related_account_id']; ?></td>
                        <td style="border: 1px solid #ccc; padding: 10px;"><?php echo date("Y-m-d H:i:s", strtotime($transaction['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="border: 1px solid #ccc; padding: 10px;">No recent transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="all-transactions.php" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">All Transactions</a>



</body>
</html>

