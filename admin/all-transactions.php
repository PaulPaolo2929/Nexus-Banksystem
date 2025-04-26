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

// Fetch all transactions from database
$sql = "SELECT * FROM transactions ORDER BY created_at DESC";
$result = $conn->query($sql);

$transactions = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Transactions</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">

    <div style="max-width: 1000px; margin: auto; background-color: white; padding: 20px; border-radius: 10px;">
        <h1 style="text-align: center; color: #333;">All Transactions</h1>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #007bff; color: white;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Transaction ID</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Account ID</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Type</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Amount</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Description</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Related Account</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $transaction['transaction_id']; ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $transaction['account_id']; ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo "₱" . number_format($transaction['amount'], 2); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $transaction['related_account_id']; ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date("Y-m-d H:i:s", strtotime($transaction['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 10px; text-align: center;">No transactions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" style="display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Back to Dashboard</a>
    </div>

</body>
</html>
