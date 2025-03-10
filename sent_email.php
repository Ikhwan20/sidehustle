<?php
require_once 'vendor/autoload.php'; // Adjust the path as per your installation
require_once 'config.php'; // Include your configuration file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendApplicationStatusEmail($userEmail) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;

        //Recipients
        $mail->setFrom(SMTP_USERNAME, 'Side Hustle');
        $mail->addAddress($userEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Update';
        $mail->Body    = 'Your application status has been updated. Check your dashboard for details.';

        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        return false; // Failed to send email
    }
}
?>
