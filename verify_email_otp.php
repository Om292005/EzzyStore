<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $_POST['email'];
$otp = $_POST['otp'];

$conn = new mysqli("localhost", "root", "", "ezzystore");

$result = $conn->query("SELECT * FROM otp_verification 
                        WHERE email='$email' AND otp_code='$otp' 
                        AND created_at >= NOW() - INTERVAL 5 MINUTE");

if ($result->num_rows > 0) {
    echo "OTP Verified";
} else {
    echo "Invalid or expired OTP";
}
?>
