<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/PHPMailer-6.8.0/src/Exception.php';
require '../vendor/phpmailer/PHPMailer-6.8.0/src/PHPMailer.php';
require '../vendor/phpmailer/PHPMailer-6.8.0/src/SMTP.php';

function sendEmail($to, $subject, $message, $from_name, $from_email) {
    try {
        // Get email configuration
        $config = require_once '../config/email_config.php';
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = $config['port'];

        // Recipients
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to);
        $mail->addReplyTo($from_email, $from_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        // Send the email
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
?> 