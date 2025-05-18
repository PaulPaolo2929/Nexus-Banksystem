<?php 
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/includes/mailer.php';
require_once __DIR__ . '/includes/config.php'; // Add config for email credentials

// Sanitize and validate inputs
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['contact_error'] = 'Please fill in all required fields.';
        header('Location: contact.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_error'] = 'Please enter a valid email address.';
        header('Location: contact.php');
        exit();
    }

    // Prepare email content
    $to = '0323-4199@lspu.edu.ph'; // Replace with your official support email
    $email_subject = "Contact Form: $subject";
    $email_body = "You have received a new message from the contact form on your website.\n\n" .
                  "Name: $name\n" .
                  "Email: $email\n" .
                  "Subject: $subject\n" .
                  "Message:\n$message\n";

    // Use PHPMailer from includes/mailer.php to send email
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0; // Set to 4 for debugging; 0 for production
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME; // From config
        $mail->Password   = SMTP_PASSWORD; // From config
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom(SMTP_USERNAME, 'Nexus Bank');
        $mail->addAddress($to);
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(false); // Set true if you want HTML formatting
        $mail->Subject = $email_subject;
        $mail->Body    = $email_body;

        $mail->send();
        $_SESSION['contact_success'] = 'Your message has been sent successfully.';
    } catch (Exception $e) {
        error_log("Contact form mail error: " . $mail->ErrorInfo);
        $_SESSION['contact_error'] = 'There was an error sending your message. Please try again later.';
        $_SESSION['contact_error_details'] = $mail->ErrorInfo;
    }

    header('Location: contact.php');
    exit();
} else {
    // Invalid request method
    header('Location: contact.php');
    exit();
}
?>
