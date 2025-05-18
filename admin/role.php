<?php 
// Connect to the database
$conn = new mysqli("localhost", "root", "", "securebank");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users with their current role (admin or user)
$sql = "SELECT user_id, full_name, email, is_admin FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles</title>
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

<!-- Navigation Menu -->
<nav class="dashboard-nav">
     <a href="dashboard.php">Admin Dashboard</a>
            <a href="manage-users.php">Manage Users</a>
            <a href="manage-loans.php">Manage Loans</a>
            <a href="manage-investments.php">Manage Investments</a>
            <a href="track-investments.php">Users Investments</a>
            <a href="role.php">Roles</a>
            <a href="recent_transactions.php">Transactions</a>
            <a href="login-records.php">Login Records</a>
</nav>

<h1>User Roles</h1>

<!-- Table to display users and their roles -->
<table>
    <tr>
        <th>User ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Role</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['user_id']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= $row['is_admin'] == 1 ? 'Admin' : 'User' ?></td> <!-- Display "Admin" or "User" based on is_admin value -->
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
