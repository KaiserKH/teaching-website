<?php
require_once __DIR__ . '/../config.php';
$cfg = require __DIR__ . '/../config.php';

function mailer_send($to, $subject, $body, $from = null) {
    $cfg = require __DIR__ . '/../config.php';
    $from = $from ?? ($cfg['mail']['mail_from'] ?? 'no-reply@localhost');

    // Prefer PHPMailer if composer autoload exists
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // SMTP config
            if (!empty($cfg['mail']['smtp_host'])) {
                $mail->isSMTP();
                $mail->Host = $cfg['mail']['smtp_host'];
                $mail->SMTPAuth = true;
                $mail->Username = $cfg['mail']['smtp_user'];
                $mail->Password = $cfg['mail']['smtp_pass'];
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $cfg['mail']['smtp_port'] ?: 587;
            }
            $mail->setFrom($from);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            return $mail->send();
        } catch (Exception $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        }
    }

    // Fallback to PHP mail()
    $headers = "From: " . $from . "\r\n" .
               "MIME-Version: 1.0\r\n" .
               "Content-Type: text/html; charset=UTF-8\r\n";
    return @mail($to, $subject, $body, $headers);
}
