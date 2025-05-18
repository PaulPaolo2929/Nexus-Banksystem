<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/notification.php';

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

// CSRF token for forms
if (empty($_SESSION['loan_token'])) {
    $_SESSION['loan_token'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['password_token'])) {
    $_SESSION['password_token'] = bin2hex(random_bytes(32));
}
$loan_token = $_SESSION['loan_token'];
$password_token = $_SESSION['password_token'];

// Handle loan form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                // Profile update logic remains unchanged
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
                break;

            case 'change_password':
                if (!isset($_POST['password_token']) || $_POST['password_token'] !== $_SESSION['password_token']) {
                    $error = "Invalid security token for password change.";
                } else {
                    $currentPassword = $_POST['current_password'];
                    $newPassword = $_POST['new_password'];
                    $confirmPassword = $_POST['confirm_password'];

                    // Verify current password
                    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();

                    if (!password_verify($currentPassword, $user['password_hash'])) {
                        $error = "Current password is incorrect";
                    } elseif ($newPassword !== $confirmPassword) {
                        $error = "New passwords do not match";
                    } elseif (strlen($newPassword) < 8) {
                        $error = "Password must be at least 8 characters long";
                    } else {
                        try {
                            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                            $stmt->execute([$hashedPassword, $userId]);
                            
                            // Fetch fresh user data for the email
                            $userStmt = $pdo->prepare("SELECT full_name, email FROM users WHERE user_id = ?");
                            $userStmt->execute([$userId]);
                            $userData = $userStmt->fetch();
                            
                            if ($userData) {
                                $emailSubject = "Password Change Alert - Nexus Bank";
                                $emailMessage = "
                                    <html>
                                    <body>
                                        <h2>Password Change Notification</h2>
                                        <p>Dear " . htmlspecialchars($userData['full_name']) . ",</p>
                                        <p>Your password was recently changed on your Nexus Bank account.</p>
                                        <p>If you did not make this change, please contact our support team immediately or reply to this email.</p>
                                        <p>Time of change: " . date('Y-m-d H:i:s') . "</p>
                                        <br>
                                        <p>Best regards,</p>
                                        <p>Nexus Bank Security Team</p>
                                    </body>
                                    </html>
                                ";
                                
                                sendNotification($userData['email'], $emailSubject, $emailMessage);
                            }
                            
                            // Generate new token and set success message
                            $_SESSION['password_token'] = bin2hex(random_bytes(32));
                            $success = "Password updated successfully!";
                            
                            // Redirect to prevent form resubmission
                            header("Location: profile.php?tab=security&status=success&message=" . urlencode($success));
                            exit();
                        } catch (Exception $e) {
                            $error = "Failed to update password: " . $e->getMessage();
                        }
                    }
                }
                break;
        }
    } else {
        // Handle loan submission
        if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['loan_token']) {
            $error = "Invalid loan submission token.";
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
}

// Handle the success message from redirect
if (isset($_GET['status']) && $_GET['status'] === 'success' && isset($_GET['message'])) {
    $success = urldecode($_GET['message']);
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
$profilePic = $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../assets/images/default-avatar.png';
// Fetch user's profile information



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Profile</title>
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

.tab-content {
    display: none;
    margin-top: 20px;
}
.tab-content.active {
    display: block;
}
.tabs .tab {
    cursor: pointer;
    padding: 10px 20px;
    margin-right: 5px;
    border-bottom: 2px solid transparent;
}
.tabs .tab.active {
    border-bottom: 2px solid #007bff;
    color: #007bff;
}
.security-form {
    max-width: 500px;
    margin: 0 auto;
}
.security-form .form-group {
    margin-bottom: 20px;
}
.security-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.security-form input[type="password"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.security-form button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.security-form button:hover {
    background-color: #0056b3;
}

.security-section {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.security-section h3 {
    color: #333;
    margin-bottom: 10px;
}

.security-section .description {
    color: #666;
    margin-bottom: 25px;
}

.security-form {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.security-form .form-group {
    margin-bottom: 20px;
}

.security-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.security-form input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.security-form .form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}

.btn-change-password {
    background-color: #007bff;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-change-password:hover {
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
                            <span class="tab active" data-tab="profile">Edit Profile</span>
                            <span class="tab" data-tab="preferences">Preferences</span>
                            <span class="tab" data-tab="security">Security</span>
                        </div>

                        <!-- Profile Content -->
                        <div class="tab-content active" id="profile-content">
                            <!-- Profile Picture Section -->
                            <div class="profile-picture-section">
                                <img src="<?= $profilePic ?>" alt="Profile Picture" class="profile-picture" data-toggle="modal">
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
                                    <button type="submit" id="saveProfileBtn" style="display: none;">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Preferences Content -->
                        <div class="tab-content" id="preferences-content">
                            <!-- Add preferences content here later -->
                            <p>Preferences settings coming soon...</p>
                        </div>

                        <!-- Security Content -->
                        <div class="tab-content" id="security-content">
                            <div class="security-section">
                                <h3>Change Password</h3>
                                <p class="description">Update your password to keep your account secure. Make sure to use a strong password that's at least 8 characters long.</p>
                                
                                <form id="passwordForm" method="POST" class="security-form">
                                    <input type="hidden" name="action" value="change_password">
                                    <input type="hidden" name="password_token" value="<?= htmlspecialchars($password_token) ?>">
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" id="new_password" name="new_password" required>
                                        <small class="form-text">Password must be at least 8 characters long</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn-change-password">Change Password</button>
                                    </div>
                                </form>
                            </div>
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

        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching functionality
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Show selected content
                    const contentId = tab.getAttribute('data-tab') + '-content';
                    const content = document.getElementById(contentId);
                    if (content) {
                        content.classList.add('active');
                    }
                });
            });

            // Profile edit functionality
            const editBtn = document.getElementById('editProfileBtn');
            const saveBtn = document.getElementById('saveProfileBtn');
            const form = document.getElementById('profileForm');
            const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], textarea');

            if (editBtn && saveBtn) {
                editBtn.addEventListener('click', () => {
                    inputs.forEach(input => {
                        if (input.name) { // Only enable inputs with name attribute
                            input.disabled = false;
                        }
                    });
                    editBtn.style.display = 'none';
                    saveBtn.style.display = 'inline-block';
                });
            }

            // Password validation
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPass = document.getElementById('new_password').value;
                    const confirmPass = document.getElementById('confirm_password').value;

                    if (newPass !== confirmPass) {
                        e.preventDefault();
                        alert('New passwords do not match!');
                        return false;
                    }

                    if (newPass.length < 8) {
                        e.preventDefault();
                        alert('Password must be at least 8 characters long!');
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>
