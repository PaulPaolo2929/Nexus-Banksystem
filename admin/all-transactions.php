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
<style>
  /* Reset and base */
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 40px 20px;
    color: #212529;
  }
  nav.dashboard-nav {
    max-width: 1000px;
    margin: 0 auto 30px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
  }
  nav.dashboard-nav a {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }
  nav.dashboard-nav a:hover {
    background-color: #0056b3;
  }
  .container {
    max-width: 1000px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    padding: 30px 40px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  h1 {
    margin-bottom: 25px;
    color: #343a40;
    text-align: center;
    font-weight: 700;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
  }
  thead tr {
    background-color: #007bff;
    color: white;
  }
  th, td {
    padding: 12px 15px;
    border: 1px solid #dee2e6;
    text-align: left;
  }
  tbody tr:nth-child(odd) {
    background-color: #f1f3f5;
  }
  tbody tr:hover {
    background-color: #e9ecef;
  }
  a.btn-back {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 20px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }
  a.btn-back:hover {
    background-color: #495057;
  }
  @media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
      display: block;
    }
    thead tr {
      display: none;
    }
    tbody tr {
      margin-bottom: 20px;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      padding: 15px;
      background: white;
    }
    tbody td {
      border: none;
      padding-left: 50%;
      position: relative;
      white-space: pre-wrap;
      word-break: break-word;
    }
    tbody td::before {
      content: attr(data-label);
      position: absolute;
      left: 15px;
      top: 12px;
      font-weight: 700;
      color: #495057;
    }
  }
</style>
</head>
<body>
     <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
     <a href="recent_transactions.php" class="btn-back">Back to Recent Transactions</a>


<nav class="dashboard-nav">
    <a href="manage-users.php">Manage Users</a>
    <a href="manage-loans.php">Manage Loans</a>
    <a href="manage-investments.php">Manage Investments</a>
    <a href="track-investments.php">Users Investments</a>
    <a href="role.php">Roles</a>
    <a href="recent_transactions.php">Transactions</a>
    <a href="login-records.php">Login Records</a>
</nav>

<div class="container">
    <h1>All Transactions</h1>
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
        </tbody>
    </table>

   
</div>


</body>
</html>
