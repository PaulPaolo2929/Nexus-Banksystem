<?php
// Include necessary files and check if the user is an admin
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure the user is an admin
redirectIfNotAdmin();

// Handle adding a new investment plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    // Sanitize user input
    $plan_name = htmlspecialchars($_POST['plan_name']);
    $interest_rate = (float) $_POST['interest_rate'];
    $min_amount = (float) $_POST['min_amount'];
    $max_amount = (float) $_POST['max_amount'];
    $duration_months = (int) $_POST['duration_months'];
    $risk_level = htmlspecialchars($_POST['risk_level']);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO investment_plans (plan_name, interest_rate, min_amount, max_amount, duration_months, risk_level) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$plan_name, $interest_rate, $min_amount, $max_amount, $duration_months, $risk_level]);

    header("Location: manage-investments.php"); // Redirect to refresh the page after adding
    exit();
}

// Handle editing an existing investment plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_plan'])) {
    // Sanitize user input
    $plan_id = (int) $_POST['plan_id'];
    $plan_name = htmlspecialchars($_POST['plan_name']);
    $interest_rate = (float) $_POST['interest_rate'];
    $min_amount = (float) $_POST['min_amount'];
    $max_amount = (float) $_POST['max_amount'];
    $duration_months = (int) $_POST['duration_months'];
    $risk_level = htmlspecialchars($_POST['risk_level']);

    // Update the investment plan in the database
    $stmt = $pdo->prepare("UPDATE investment_plans SET plan_name = ?, interest_rate = ?, min_amount = ?, max_amount = ?, 
                           duration_months = ?, risk_level = ? WHERE plan_id = ?");
    $stmt->execute([$plan_name, $interest_rate, $min_amount, $max_amount, $duration_months, $risk_level, $plan_id]);

    header("Location: manage-investments.php"); // Redirect to refresh the page after editing
    exit();
}

// Fetch all investment plans
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$totalCount = $pdo->query("SELECT COUNT(*) FROM investment_plans")->fetchColumn();
$totalPages = ceil($totalCount / $perPage);
$investmentPlans = $pdo->prepare("SELECT * FROM investment_plans LIMIT :perPage OFFSET :offset");
$investmentPlans->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$investmentPlans->bindValue(':offset', $offset, PDO::PARAM_INT);
$investmentPlans->execute();
$investmentPlans = $investmentPlans->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Investments - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-investment.css">

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
                                <a href="manage-investments.php" class="btn dash-text">Manage Investments</a>
                                <a href="track-investments.php" class="btn">Users Investments</a>
                                <a href="role.php" class="btn">Roles</a>
                                <a href="recent_transactions.php" class="btn">Transactions</a>
                                <a href="recent_transactions.php" class="btn">Loan History</a>
                                <a href="login-records.php" class="btn">Login Records</a>
                                <a href="manage-messages.php" class="btn">Contact Messages</a>
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
            <!-- Add New Investment Plan Form -->
            <h2>Add New Investment Plan</h2>
            <form action="manage-investments.php" method="POST">
                <label for="plan_name">Plan Name:</label>
                <input type="text" id="plan_name" name="plan_name" required>
                
                <label for="interest_rate">Interest Rate (%):</label>
                <input type="number" id="interest_rate" name="interest_rate" required step="0.01">
                
                <label for="min_amount">Min Investment Amount:</label>
                <input type="number" id="min_amount" name="min_amount" required step="0.01">
                
                <label for="max_amount">Max Investment Amount:</label>
                <input type="number" id="max_amount" name="max_amount" required step="0.01">
                
                <label for="duration_months">Duration (Months):</label>
                <input type="number" id="duration_months" name="duration_months" required>
                
                <label for="risk_level">Risk Level:</label>
                <select id="risk_level" name="risk_level" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
                
                <button type="submit" name="add_plan">Add Plan</button>
            </form>

            <h2>Investment Plans</h2>
            <?php if (empty($investmentPlans)): ?>
                <p>No investment plans found.</p>
            <?php else: ?>
                <table class="investment-plans-table">
                    <thead>
                        <tr>
                            <th>Plan Name</th>
                            <th>Interest Rate</th>
                            <th>Min Investment</th>
                            <th>Max Investment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($investmentPlans as $plan): ?>
                            <tr>
                                <td data-label="Plan Name"><?= htmlspecialchars($plan['plan_name']) ?></td>
                                <td data-label="Interest Rate"><?= htmlspecialchars($plan['interest_rate']) ?>%</td>
                                <td data-label="Min Investment">₱<?= number_format($plan['min_amount'] ?? 0, 2) ?></td>
                                <td data-label="Max Investment">₱<?= number_format($plan['max_amount'] ?? 0, 2) ?></td>
                                <td data-label="Action">
                                    <!-- Edit button for each plan -->
                                    <button onclick="openEditForm(<?= $plan['plan_id'] ?>, '<?= htmlspecialchars($plan['plan_name']) ?>', <?= $plan['interest_rate'] ?>, <?= $plan['min_amount'] ?>, <?= $plan['max_amount'] ?>, <?= $plan['duration_months'] ?>, '<?= htmlspecialchars($plan['risk_level']) ?>')">Edit</button>
                                </td>
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
    
    <!-- Edit Investment Plan Modal -->
    <div id="edit-modal" style="display:none;">
        <h2>Edit Investment Plan</h2>
        <form action="manage-investments.php" method="POST">
            <input type="hidden" id="edit_plan_id" name="plan_id">
            
            <label for="edit_plan_name">Plan Name:</label>
            <input type="text" id="edit_plan_name" name="plan_name" required>
            
            <label for="edit_interest_rate">Interest Rate (%):</label>
            <input type="number" id="edit_interest_rate" name="interest_rate" required step="0.01">
            
            <label for="edit_min_amount">Min Investment Amount:</label>
            <input type="number" id="edit_min_amount" name="min_amount" required step="0.01">
            
            <label for="edit_max_amount">Max Investment Amount:</label>
            <input type="number" id="edit_max_amount" name="max_amount" required step="0.01">
            
            <label for="edit_duration_months">Duration (Months):</label>
            <input type="number" id="edit_duration_months" name="duration_months" required>
            
            <label for="edit_risk_level">Risk Level:</label>
            <select id="edit_risk_level" name="risk_level" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            
            <button type="submit" name="edit_plan">Save Changes</button>
            <button type="button" onclick="closeEditForm()">Cancel</button>
        </form>
    </div>
</main>
</div>
    <script>
        // Function to open the edit form modal and populate it with plan data
        function openEditForm(plan_id, plan_name, interest_rate, min_amount, max_amount, duration_months, risk_level) {
            document.getElementById('edit_plan_id').value = plan_id;
            document.getElementById('edit_plan_name').value = plan_name;
            document.getElementById('edit_interest_rate').value = interest_rate;
            document.getElementById('edit_min_amount').value = min_amount;
            document.getElementById('edit_max_amount').value = max_amount;
            document.getElementById('edit_duration_months').value = duration_months;
            document.getElementById('edit_risk_level').value = risk_level;
            
            document.getElementById('edit-modal').style.display = 'block';
        }

        // Function to close the edit form modal
        function closeEditForm() {
            document.getElementById('edit-modal').style.display = 'none';
        }
    </script>
</body>
</html>
