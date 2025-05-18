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
$investmentPlans = $pdo->query("SELECT * FROM investment_plans")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Investments - SecureBank Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Investments</h1>
            <a href="dashboard.php" class="back">Back to Dashboard</a>
        </header>

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
                                <td><?= htmlspecialchars($plan['plan_name']) ?></td>
                                <td><?= htmlspecialchars($plan['interest_rate']) ?>%</td>
                                <td>$<?= number_format($plan['min_amount'] ?? 0, 2) ?></td>
                                <td>$<?= number_format($plan['max_amount'] ?? 0, 2) ?></td>
                                <td>
                                    <!-- Edit button for each plan -->
                                    <button onclick="openEditForm(<?= $plan['plan_id'] ?>, '<?= htmlspecialchars($plan['plan_name']) ?>', <?= $plan['interest_rate'] ?>, <?= $plan['min_amount'] ?>, <?= $plan['max_amount'] ?>, <?= $plan['duration_months'] ?>, '<?= htmlspecialchars($plan['risk_level']) ?>')">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
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
