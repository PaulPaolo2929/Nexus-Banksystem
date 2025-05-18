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
                                <a href="dashboard.php" class="active btn ">Dashboard</a>
                                <a href="manage-users.php" class="btn ">Manage Users</a>
                                <a href="manage-loans.php" class="btn">Manage Loans</a>
                                <a href="manage-investments.php" class="btn">Manage Investments</a>
                                <a href="track-investments.php" class="btn">Users Investments</a>
                                <a href="role.php" class="btn dash-text">Roles</a>
                                <a href="recent_transactions.php" class="btn">Transactions</a>
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
                            
                <h2>User Roles</h2>

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
                        <td data-label="User ID"><?= htmlspecialchars($row['user_id']) ?></td>
                        <td data-label="Full Name"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                        <td data-label="Role"><?= $row['is_admin'] == 1 ? 'Admin' : 'User' ?></td> <!-- Display "Admin" or "User" based on is_admin value -->
                    </tr>
                    <?php endwhile; ?>
                </table>
        </div>
        
</main>
</div>

</body>
</html>

<?php $conn->close(); ?>
