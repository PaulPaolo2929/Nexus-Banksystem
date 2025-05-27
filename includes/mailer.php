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
        $mail->Username   = 'nexusbanksystem@gmail.com'; // Your Gmail
        $mail->Password   = 'ntnl ttfo uayi vsxd'; // App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Enable debug output
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };

        // Recipients
        $mail->setFrom('nexusbanksystem@gmail.com', 'Nexus Bank');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Nexus Bank OTP Code';
        $mail->Body    = "Your OTP code is: <strong>$otpCode</strong><br>Valid for 5 minutes.";
        $mail->AltBody = "Your OTP code is: $otpCode (valid for 5 minutes)";

        $mail->send();
        error_log("OTP email sent successfully to $recipientEmail");
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        error_log("Exception details: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}
?>