<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $allowed = ['image/jpeg', 'image/jpg', 'image/png'];

    if (in_array($file['type'], $allowed) && $file['size'] <= 2 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'profile_' . $userId . '_' . time() . '.' . $ext;
        $uploadPath = '../uploads/' . $newName;

        if (!is_dir('../uploads')) {
            mkdir('../uploads', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->execute([$newName, $userId]);

            header("Location: profile.php?upload=success");
            exit;
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Invalid file type or size too large.";
    }
}
?>