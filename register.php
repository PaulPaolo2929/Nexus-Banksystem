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
    'phone' => ''
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Create Your Account</h1>

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

    <form method="POST" id="registrationForm">
        <input type="text" name="full_name" placeholder="Full Name" required value="<?= htmlspecialchars($data['full_name']) ?>">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($data['email']) ?>">
        <input type="text" name="address" placeholder="Address" required value="<?= htmlspecialchars($data['address']) ?>">
        <input type="text" name="occupation" placeholder="Occupation" required value="<?= htmlspecialchars($data['occupation']) ?>">
        <input type="tel" name="phone" placeholder="Phone Number" required value="<?= htmlspecialchars($data['phone']) ?>">
        <input type="number" name="age" placeholder="Age" min="18" max="120" required value="<?= htmlspecialchars($data['age']) ?>">
        <input type="number" name="birth_year" placeholder="Birth Year" min="1900" max="<?= date('Y') ?>" required value="<?= htmlspecialchars($data['birth_year']) ?>">
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Sign in</a>
    </div>
</div>
</body>
</html>
