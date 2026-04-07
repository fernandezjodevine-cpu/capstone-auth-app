<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

class Mailer {
    private $fromEmail = 'yna09fernandez@gmail.com';
    private $fromName = 'Email Auth System';

    public function sendVerificationEmail($toEmail, $toName, $verifyLink) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yna09fernandez@gmail.com';
            $mail->Password   = 'esbtjdtkgjoffnyk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email';
            $mail->Body    = $this->getVerificationEmailTemplate($toName, $verifyLink);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function sendPasswordResetEmail($toEmail, $toName, $resetLink) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yna09fernandez@gmail.com';
            $mail->Password   = 'esbtjdtkgjoffnyk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = $this->getPasswordResetEmailTemplate($toName, $resetLink);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Mailer Error: {$mail->ErrorInfo}";
        }
    }

    private function getVerificationEmailTemplate($name, $verifyLink) {
        return "<h3>Email Verification</h3>
                <p>Hi <strong>$name</strong>,</p>
                <p>Thank you for creating an account. Please verify your email address to complete your registration.</p>
                <p>Click below to verify your account:</p>
                <a href='$verifyLink' style='background:#0f472c;color:white;padding:12px 24px;text-decoration:none;border-radius:4px;display:inline-block;margin:15px 0;'>Verify Email Address</a>
                <p>Or copy this link:</p>
                <p><small>$verifyLink</small></p>
                <p>This link will expire in 24 hours.</p>";
    }

    private function getPasswordResetEmailTemplate($name, $resetLink) {
        return "<h3>Reset Your Password</h3>
                <p>Hi <strong>$name</strong>,</p>
                <p>We received a request to reset your password. Click the link below to create a new password.</p>
                <p><a href='$resetLink' style='background:#0f472c;color:white;padding:12px 24px;text-decoration:none;border-radius:4px;display:inline-block;margin:15px 0;'>Reset Password</a></p>
                <p>Or copy this link:</p>
                <p><small>$resetLink</small></p>
                <p>This link will expire in 1 hour. If you did not request this, please ignore this email.</p>";
    }
}
