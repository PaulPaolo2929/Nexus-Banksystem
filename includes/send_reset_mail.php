<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendResetLink($recipientEmail, $resetLink) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shaison62@gmail.com'; // Your Gmail
        $mail->Password   = 'awxn tpnn ogsm grut'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('shaison62@gmail.com', 'SecureBank');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your SecureBank Password';
        $mail->Body    = "Click the link to reset your password:<br><a href='$resetLink'>$resetLink</a><br><br>This link expires in 1 hour.";
        $mail->AltBody = "Click the link to reset your password: $resetLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Reset Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
