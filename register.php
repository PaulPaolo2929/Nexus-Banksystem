<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
// No OTP required at this stage unless you want OTP after approval

// Initialize variables
$errors = [];
$data = [
    'full_name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'age' => '',
    'birth_year' => '',
    'address' => '',
    'occupation' => '',
    'phone' => '',
    'id_type' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $data['full_name'] = sanitizeInput($_POST['full_name'] ?? '');
    $data['email'] = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $data['password'] = $_POST['password'] ?? '';
    $data['confirm_password'] = $_POST['confirm_password'] ?? '';
    $data['age'] = (int)($_POST['age'] ?? 0);
    $data['birth_year'] = (int)($_POST['birth_year'] ?? 0);
    $data['address'] = sanitizeInput($_POST['address'] ?? '');
    $data['occupation'] = sanitizeInput($_POST['occupation'] ?? '');
    $data['phone'] = sanitizeInput($_POST['phone'] ?? '');
    $data['id_type'] = sanitizeInput($_POST['id_type'] ?? '');

    // Validate ID file upload
    if (!isset($_FILES['id_file']) || $_FILES['id_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Please upload a valid ID document";
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['id_file']['type'], $allowedTypes)) {
            $errors[] = "Invalid file type. Please upload JPG, PNG, or PDF files only.";
        }

        if ($_FILES['id_file']['size'] > $maxFileSize) {
            $errors[] = "File size too large. Maximum size is 5MB.";
        }
    }

    // Validate password
    if (!validatePassword($data['password'])) {
        $errors[] = "Password must contain at least:<br>
                     - One uppercase letter<br>
                     - One lowercase letter<br>
                     - One number<br>
                     - One special character<br>
                     - Minimum 8 characters";
    }

    if ($data['password'] !== $data['confirm_password']) {
        $errors[] = "Passwords do not match";
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (!preg_match('/^\+?\d{7,15}$/', $data['phone'])) {
        $errors[] = "Invalid phone number format";
    }

    if ($data['age'] < 18 || $data['age'] > 120) {
        $errors[] = "Age must be between 18 and 120";
    }

    $currentYear = (int)date('Y');
    if ($data['birth_year'] < 1900 || $data['birth_year'] > $currentYear) {
        $errors[] = "Birth year must be between 1900 and $currentYear";
    }

    $calculatedAge = $currentYear - $data['birth_year'];
    if (abs($calculatedAge - $data['age']) > 1) {
        $errors[] = "Age and birth year don't match (you entered age {$data['age']} and birth year {$data['birth_year']}, which would make you approximately $calculatedAge years old)";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                $errors[] = "Email already registered";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "System error. Please try again later.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, age, birth_year, address, occupation, phone, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $data['full_name'],
                $data['email'],
                $passwordHash,
                $data['age'],
                $data['birth_year'],
                $data['address'],
                $data['occupation'],
                $data['phone']
            ]);

            $userId = $pdo->lastInsertId();

            // Handle ID file upload
            $uploadDir = 'uploads/id_verifications/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['id_file']['name'], PATHINFO_EXTENSION);
            $fileName = 'id_' . $userId . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['id_file']['tmp_name'], $filePath)) {
                $stmt = $pdo->prepare("INSERT INTO id_verifications (user_id, id_type, id_file_path) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $data['id_type'], $filePath]);
            } else {
                throw new Exception("Failed to upload ID file");
            }

            $pdo->commit();
            $success = "Registration submitted! Please wait for admin approval.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Registration error: " . $e->getMessage());
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - SecureBank</title>
    <link rel="stylesheet" href="./assets/css/register.css">
</head>
<body>

<div class="wrapper">
            <div class="left-panel">
                <div>
                <img src="./assets/images/Logo.png" alt="Nexus Logo" class="logo" />
                </div>
                <div class="handshake-container">
                    <img src="./assets/images/handshake.png" alt="Handshake" class="handshake" />
                </div>
            
            <div class="content">
                <h2 class="headline">Partnership for<br>Business Growth</h2>
                <p class="description">
                Welcome to Nexus Bank System, your trusted partner in secure and efficient banking solutions.
                </p>
            </div>
            </div>

        <div class="container">
              <div class="login-form">
                    <p style="text-align: start;"> Let's get you Started </p> 
                    <h1 style="text-align: start;">Create your Account</h1>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p><?= $error ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($success)): ?>
                            <div class="alert alert-success">
                                <p><?= $success ?></p>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="registrationForm" enctype="multipart/form-data">
            <div class="form-group">
                <div class="form-field">
                <input type="text" name="full_name" required placeholder="" value="<?= htmlspecialchars($data['full_name']) ?>">
                <label>Full Name</label>
                </div>

                <div class="form-row">
                <div class="form-field">
                <input type="email" name="email" required placeholder=" " value="<?= htmlspecialchars($data['email']) ?>">
                <label>Email</label>
                </div>
                <div class="form-field">
                <input type="text" name="address" required placeholder=" " value="<?= htmlspecialchars($data['address']) ?>">
                <label>Address</label>
                </div>
                </div>
                <div class="form-field">
                <input type="text" name="occupation" required placeholder=" " value="<?= htmlspecialchars($data['occupation']) ?>">
                <label>Occupation</label>
                </div>

                <div class="form-row">
                <div class="form-field">
                    <input type="tel" name="phone" required placeholder=" " value="<?= htmlspecialchars($data['phone']) ?>">
                    <label>Phone</label>
                </div>
                <div class="form-field">
                    <input type="number" name="age" min="18" max="120" required placeholder=" " value="<?= htmlspecialchars($data['age']) ?>">
                    <label>Age</label>
                </div>
                </div>

                <div class="form-row">
                <div class="form-field">
                    <input type="number" name="birth_year" min="1900" max="<?= date('Y') ?>" required placeholder=" " value="<?= htmlspecialchars($data['birth_year']) ?>">
                    <label>Birth Year</label>
                </div>
                </div>

                <div class="id-verification-fields">
                    <div class="form-field">
                        <label for="id_type">ID Type</label>
                        <select name="id_type" id="id_type" required>
                            <option value="">Select ID Type</option>
                            <option value="passport">Passport</option>
                            <option value="drivers_license">Driver's License</option>
                            <option value="national_id">National ID</option>
                            <option value="student_id">Student ID</option>
                            <option value="other">Other Government ID</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="id_file">Upload ID Document (JPG, PNG, or PDF)</label>
                        <input type="file" name="id_file" id="id_file" required accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>

                <div class="form-field">
                <input type="password" name="password" required placeholder=" ">
                <label>Password</label>
                </div>

                <div class="form-field">
                <input type="password" name="confirm_password" required placeholder=" ">
                <label>Confirm Password</label>
                </div>
            </div>

            <button type="submit" class="btn-submit">Register</button>
            </form>

                        <div class="login-link" style="text-align: center; margin-top: 20px; color: #6b7280;">
                            Already have an account? <a href="login.php" style="text-decoration: none;">Sign in</a>
                        </div>
        </div>
    </div>
</div>
</body>
</html>
