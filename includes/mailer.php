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
        $mail->Username   = '0323-4199@lspu.edu.ph'; // Your Gmail
        $mail->Password   = 'vopqytjrodlodxhe'; // App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('0323-4199@lspu.edu.ph');
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

function sendEmail($to, $subject, $body, $replyToEmail = null, $replyToName = null) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '0323-4199@lspu.edu.ph'; // Your Gmail
        $mail->Password   = 'vopqytjrodlodxhe'; // App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('0323-4199@lspu.edu.ph', 'Nexus Bank');
        $mail->addAddress($to);
        if ($replyToEmail) {
            $mail->addReplyTo($replyToEmail, $replyToName);
        }

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>