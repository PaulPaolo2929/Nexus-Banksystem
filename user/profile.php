<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error  = '';
$success= '';

// ─── Handle profile update ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $full_name  = htmlspecialchars($_POST['full_name'],  ENT_QUOTES, 'UTF-8');
    $email      = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $age        = intval($_POST['age']);
    $birth_year = intval($_POST['birth_year']);
    $address    = htmlspecialchars($_POST['address'],     ENT_QUOTES, 'UTF-8');
    $occupation = htmlspecialchars($_POST['occupation'],  ENT_QUOTES, 'UTF-8');
    $phone      = htmlspecialchars($_POST['phone'],       ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET full_name=?, email=?, age=?, birth_year=?, address=?, occupation=?, phone=?
            WHERE user_id=?
        ");
        $stmt->execute([
            $full_name,
            $email,
            $age,
            $birth_year,
            $address,
            $occupation,
            $phone,
            $userId
        ]);
        $success = "Profile updated successfully!";
    } catch (Exception $e) {
        $error = "Failed to update profile: " . $e->getMessage();
    }
}

// ─── Fetch user data ───
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// ─── Fetch profile picture ───
$profilePic = $user['profile_picture']
    ? '../uploads/' . $user['profile_picture']
    : '../assets/images/default-avatar.png';

// ─── (rest of your loans code unchanged) ───
$stmt = $pdo->prepare("
    SELECT * FROM loans 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$loans = $stmt->fetchAll();

$accountStmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$accountStmt->execute([$userId]);
$account = $accountStmt->fetch();
$balance = $account ? $account['balance'] : 0;

// CSRF token for loans
if (empty($_SESSION['loan_token'])) {
    $_SESSION['loan_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['loan_token'];

// Handle loan form submission (unchanged)…
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && (!isset($_POST['action']) || $_POST['action'] !== 'update_profile')
) {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['loan_token']) {
        $error = "Duplicate submission or invalid token.";
    } else {
        $amount  = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $term    = intval($_POST['term']);
        $purpose = htmlspecialchars($_POST['purpose'], ENT_QUOTES, 'UTF-8');

        if ($amount < 100) {
            $error = "Minimum loan amount is $100";
        } elseif ($term < 1 || $term > 60) {
            $error = "Loan term must be between 1 and 60 months";
        } else {
            $interestRate = 5.0;
            if ($amount > 10000) $interestRate = 4.5;
            if ($term > 36)     $interestRate += 1.0;

            try {
                $loanStmt = $pdo->prepare("
                    INSERT INTO loans (user_id, amount, interest_rate, term_months, status, purpose)
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $loanStmt->execute([$userId, $amount, $interestRate, $term, $purpose]);

                $_SESSION['loan_token'] = bin2hex(random_bytes(32));
                $success = "Loan application submitted successfully!";
            } catch (Exception $e) {
                $error = "Failed to submit loan: " . $e->getMessage();
            }
        }
    }
}

// Get user account information
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.*, a.account_number, a.balance 
    FROM users u 
    JOIN accounts a ON u.user_id = a.user_id 
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die('User account not found.');
}

// Check if the user has a profile picture
$stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
// After fetching the user from the database
$profilePic = (!empty($user['profile_picture']) && file_exists('../uploads/' . $user['profile_picture']))
    ? '../uploads/' . $user['profile_picture']
    : '../assets/images/default-avatars.png';



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Loans</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/profile.css">

    <!-- NAVIGATION EFFECTS -->
    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>

    <style>
    .profile-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 2px solid #ccc;
}
/* Modal Background (overlay) */
.modal {
    display: none;
    position: fixed;
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    overflow: auto; /* Enable scrolling if necessary */
}

/* Modal Dialog Box */
.modal-dialog {
    position: relative;
    top: 10%;
    margin: auto;
    width: 80%; /* Modal width */
    max-width: 900px; /* Maximum width to prevent large images from making it too wide */
}

/* Modal Content */
.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    position: relative;
}

/* Close Button (X) */
.close {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Modal Image */
.modal-body img {
    max-width: 100%; /* Make sure image doesn't overflow */
    max-height: 80vh; /* Adjust this value to control the image size */
    display: block;
    margin: 0 auto;
}

/* Back Button */
#backButton {
    margin-left: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
#backButton:hover {
    background-color: #0056b3;
}



</style>
</head>

<body>
    <div class="wrapper">

        <aside class="sidebar">
                        
           <div class="Logos-cont">
                    <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
                </div>
                
                <hr>

                <div class="profile-container">
                    <img src="<?= $profilePic ?>" alt="Profile Picture" class="img-fluid">
                    <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p><?= htmlspecialchars($user['account_number']) ?></p>
                </div>

                <hr>

            <nav>
                    <a href="dashboard.php" class="btn">
                        <img 
                        src="../assets/images/inactive-dashboard.png" 
                        alt="dashboard-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-dashboard.png"
                        data-hover="../assets/images/hover-dashboard.png"
                        > 
                        Dashboard
                    </a>

                    <a href="deposit.php" class="btn">
                        <img 
                        src="../assets/images/inactive-deposit.png" 
                        alt="deposit-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-deposit.png"
                        data-hover="../assets/images/hover-deposit.png"
                        > 
                        Deposit
                    </a>

                    <a href="withdraw.php" class="btn">
                        <img 
                        src="../assets/images/inactive-withdraw.png" 
                        alt="withdraw-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-withdraw.png"
                        data-hover="../assets/images/hover-withdraw.png"
                        > 
                        Withdraw
                    </a>

                    <a href="transfer.php" class="btn">
                        <img 
                        src="../assets/images/inactive-transfer.png" 
                        alt="transfer-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transfer.png"
                        data-hover="../assets/images/hover-transfer.png"
                        > 
                        Transfer
                    </a>

                    <a href="transactions.php" class="btn">
                        <img 
                        src="../assets/images/inactive-transaction.png" 
                        alt="transactions-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transaction.png"
                        data-hover="../assets/images/hover-transaction.png"
                        > 
                        Transactions
                    </a>

                    <a href="investment.php" class="btn">
                        <img 
                        src="../assets/images/inactive-investment.png" 
                        alt="investment-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-investment.png"
                        data-hover="../assets/images/hover-investment.png"
                        > 
                        Investment
                    </a>

                    <a href="loan.php" class="btn ">
                        <img 
                        src="../assets/images/inactive-loans.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-loans.png"
                        data-hover="../assets/images/inactive-loans.png"
                        > 
                        Loans
                    </a>

                    <a href="profile.php" class="btn dash-text">
                        <img 
                        src="../assets/images/hover-profile.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/hover-profile.png"
                        data-hover="../assets/images/hover-profile"
                        > 
                        Settings
                    </a>

                </nav>       
<hr>
            <div class="logout-cont">
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </aside>
    
        <main class="container">
                <header>
                    <h1>Profile Information</h1>
                    <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
                </header>

              <div class="content">
                        <!-- Heading and Instructions -->
                        <h2>Edit Profile</h2>
                        <p class="description">
                            You can update your personal information, upload a profile picture, and review your account details here.
                            Please make sure to click "Save Changes" after editing.
                        </p>

                        <!-- Success/Error Messages -->
                        <?php if ($error): ?>
                            <p class="alert alert-danger"><?= $error ?></p>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <p class="alert alert-success"><?= $success ?></p>
                        <?php endif; ?>

                        <!-- Navigation Tabs -->
                        <div class="tabs">
                            <span class="tab active">Edit Profile</span>
                            <span class="tab">Preferences</span>
                            <span class="tab">Security</span>
                        </div>

                        <!-- Profile Picture Section -->
                        <div class="profile-picture-section">
                            <img src="<?= $profilePic ?>" alt="Profile Picture" class="profile-picture" ">
                            <form action="upload_picture.php" method="POST" enctype="multipart/form-data">
                                <label>Upload Profile Picture:</label>
                                <input type="file" name="profile_picture" accept="image/*" required>
                                <button type="submit" class="upload-btn">Upload</button>
                            </form>
                        </div>

                        <!-- Image Modal -->
                        <div id="imageModal" class="modal">
                            <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                <span class="close">&times;</span>
                                <button id="backButton">Back</button>
                                </div>
                                <div class="modal-body">
                                <img src="<?= $profilePic ?>" alt="Profile Picture" class="img-fluid">
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Profile Form -->
                        <form id="profileForm" method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="profile-grid">
                            <!-- Read-Only -->
                            <div><label>Account Number</label><input type="text" value="<?= htmlspecialchars($user['account_number']) ?>" disabled></div>
                            <div><label>Password</label><input type="password" value="********" disabled></div>

                            <!-- Editable -->
                            <div><label>Full Name</label><input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" disabled></div>
                            <div><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
                            <div><label>Age</label><input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>" disabled></div>
                            <div><label>Birth Year</label><input type="number" name="birth_year" value="<?= htmlspecialchars($user['birth_year']) ?>" disabled></div>
                            <div><label>Phone</label><input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" disabled></div>
                            <div><label>Occupation</label><input type="text" name="occupation" value="<?= htmlspecialchars($user['occupation']) ?>" disabled></div>
                            <div><label>Account Status</label><input type="text" value="<?= htmlspecialchars($user['status']) ?>" disabled></div>
                            <div class="full-width"><label>Address</label><textarea name="address" disabled><?= htmlspecialchars($user['address']) ?></textarea></div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="form-actions">
                            <button type="button" id="editProfileBtn">Edit Profile</button>
                            <button type="submit" id="saveProfileBtn" disabled>Save Changes</button>
                            </div>
                        </form>
                        </div>

         </main>
    </div>

    <script>
    document.querySelectorAll('[data-toggle="modal"]').forEach(item => {
    item.addEventListener('click', function() {
        const modal = document.getElementById('imageModal');
        modal.style.display = "block";
        });
    });

        document.querySelector('.close').addEventListener('click', function() {
            const modal = document.getElementById('imageModal');
            modal.style.display = "none";
        });

        document.getElementById('backButton').addEventListener('click', function() {
            const modal = document.getElementById('imageModal');
            modal.style.display = "none";
        });

        // Close modal when clicking outside of the modal
        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        // Edit Profile button functionality
         
  const editBtn  = document.getElementById('editProfileBtn');
  const form     = document.getElementById('profileForm');
  const inputs   = form.querySelectorAll('input, textarea');
  const saveBtn  = document.getElementById('saveProfileBtn');

  editBtn.addEventListener('click', () => {
    inputs.forEach(i => i.disabled = false);
    saveBtn.disabled = true;  // still disabled until change
    editBtn.disabled = true;  // prevent re‐click
  });

  // Enable save button only once any field changes
  inputs.forEach(i => {
    i.addEventListener('input', () => {
      saveBtn.disabled = false;
    });
  });

</script>
</body>
</html>
