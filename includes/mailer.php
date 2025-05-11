<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../vendor/autoload.php';

function sendOTP($recipientEmail, $otpCode) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shaison62@gmail.com'; // Your Gmail
        $mail->Password   = 'awxn tpnn ogsm grut'; // App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('shaison62@gmail.com', 'Nexus Bank');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Nexus Bank OTP Code';
        $mail->Body    = "Your OTP code is: <strong>$otpCode</strong><br>Valid for 5 minutes.";
        $mail->AltBody = "Your OTP code is: $otpCode (valid for 5 minutes)";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>