<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function sendResetEmail($to, $link) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'roozbeh.k571@gmail.com'; 
        $mail->Password   = 'tgzj ddma ngbn kobi';     
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom('roozbeh.k571@gmail.com', 'Appointment System'); // ✅ Use your Gmail
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = '🔐 Reset your password';
        $mail->Body    = "Click this link to reset your password:<br><a href=\"$link\">$link</a>";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}