<?php
// Install PHPMailer first: composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = htmlspecialchars(trim($_POST["fullName"] ?? ''));
    $phone = htmlspecialchars(trim($_POST["phone"] ?? ''));
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $subject = htmlspecialchars(trim($_POST["subject"] ?? ''));
    $message = htmlspecialchars(trim($_POST["message"] ?? ''));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger'>❌ Invalid email address.</div>";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // ===== SMTP Configuration =====
        // Replace these with your SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'mail.mwanamama.org'; // e.g., smtp.gmail.com, smtp.office365.com, mail.yourdomain.com
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@mwanamama.com'; // Your SMTP username
        $mail->Password   = 'Pass@1234!!'; // Your SMTP password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // Use 465 for SSL, 587 for TLS

        // For some hosts, you might need:
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        // $mail->Port = 465;

        // ===== Email to Admin =====
        $mail->setFrom('info@mwanamama.com', 'Mwanamama Website');
        $mail->addAddress('info@mwanamama.com', 'Mwanamama Admin');
        $mail->addReplyTo($email, $fullName);

        $mail->isHTML(true);
        $mail->Subject = "New Contact Form: $subject";
        $mail->Body    = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h2 style='color: #333;'>New Message from Mwanamama Website</h2>
                <table style='border-collapse: collapse; width: 100%;'>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'><strong>Full Name:</strong></td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$fullName}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'><strong>Phone/WhatsApp:</strong></td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$phone}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'><strong>Email:</strong></td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$email}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'><strong>Subject:</strong></td>
                        <td style='padding: 10px; border: 1px solid #ddd;'>{$subject}</td>
                    </tr>
                </table>
                <h3 style='margin-top: 20px;'>Message:</h3>
                <p style='padding: 15px; background: #f5f5f5; border-left: 4px solid #007bff;'>{$message}</p>
            </body>
            </html>
        ";
        $mail->AltBody = "Name: {$fullName}\nPhone: {$phone}\nEmail: {$email}\nSubject: {$subject}\n\nMessage:\n{$message}";

        $mail->send();

        // ===== Confirmation Email to User =====
        $mail->clearAddresses();
        $mail->clearReplyTos();
        
        $mail->setFrom('info@mwanamama.com', 'Mwanamama Team');
        $mail->addAddress($email, $fullName);

        $mail->Subject = 'Thank you for contacting Mwanamama!';
        $mail->Body    = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h3 style='color: #333;'>Dear {$fullName},</h3>
                <p>Thank you for reaching out to Mwanamama. We've received your message and will respond shortly.</p>
                <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>
                    <strong>Your Message:</strong><br>
                    <p>{$message}</p>
                </div>
                <p>Kind regards,<br><strong>Mwanamama Team</strong></p>
                <hr style='margin: 20px 0;'>
                <p style='color: #666; font-size: 12px;'>
                    Mwanamama | P.O. Box 105-70101, Hola, Tana River County, Kenya<br>
                    Phone: 0700 424 396 | WhatsApp: 0736 814 360<br>
                    Email: info@mwanamama.com
                </p>
            </body>
            </html>
        ";
        $mail->AltBody = "Dear {$fullName},\n\nThank you for contacting Mwanamama. We've received your message and will respond shortly.\n\nYour Message:\n{$message}\n\nKind regards,\nMwanamama Team";

        $mail->send();

        echo "<div class='alert alert-success'>✅ Your message was sent successfully! Check your email for confirmation.</div>";

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Message could not be sent. Error: {$mail->ErrorInfo}</div>";
        
        // Log the error (optional)
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
    }
}
?>