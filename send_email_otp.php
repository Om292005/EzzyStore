<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$email = $_POST['email'];
$otp = rand(100000, 999999);

// Setup mailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ezzygrocery@gmail.com';
    $mail->Password = 'lyfloidpebyskprk';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ezzygrocery@gmail.com', 'EzzyStore');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your EzzyStore OTP Code';
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; font-size: 16px; color: #333; padding: 10px;'>
        <h2 style='color: #4CAF50;'>EzzyStore OTP Verification</h2>
        <p>Dear customer,</p>
        <p>Your one-time password (OTP) is:</p>
        <h1 style='color: #000; font-size: 28px;'>$otp</h1>
        <p>This OTP is valid for 5 minutes. Do not share it with anyone.</p>
        <br>
        <p>Thank you,<br>EzzyStore Team</p>
    </div>
";

    // Try to send email first
    if ($mail->send()) {
        // Only insert into DB if email sent
        $conn = new mysqli("localhost", "root", "", "ezzystore");
        $conn->query("DELETE FROM otp_verification WHERE email='$email'");
        $conn->query("INSERT INTO otp_verification (email, otp_code) VALUES ('$email', '$otp')");
        echo "OTP Sent";
    } else {
        echo "Failed to send OTP.";
    }

} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
