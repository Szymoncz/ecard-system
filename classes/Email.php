<?php
require_once 'config.php';
require_once 'vendor/autoload.php'; // Assumes PHPMailer is installed via Composer
use PHPMailer\PHPMailer\PHPMailer;

class Email {
    public function sendCard($recipient_email, $sender_name, $message, $card_image) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USER, 'E-Card System');
            $mail->addAddress($recipient_email);
            $mail->addAttachment(UPLOAD_DIR . $card_image);

            $mail->isHTML(true);
            $mail->Subject = 'You Received an E-Card!';
            $mail->Body = "<h2>E-Card from $sender_name</h2><p>$message</p><img src='cid:card_image'>";
            $mail->addEmbeddedImage(UPLOAD_DIR . $card_image, 'card_image');

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
?>