<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendNotification($recipientEmail, $subject, $messageHtml, $messagePlain = '') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nexusbanksystem@gmail.com'; // Your Gmail
        $mail->Password   = 'ntnl ttfo uayi vsxd'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('nexusbanksystem@gmail.com', 'Nexus Bank Notifications');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageHtml;
        $mail->AltBody = $messagePlain ?: strip_tags($messageHtml);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Notification Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
