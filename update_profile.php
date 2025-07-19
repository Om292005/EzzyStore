<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ezzystore");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    header("Location: login&registration.html");
    exit();
}

$email = $_SESSION['email'];

$name = $_POST['name'] ?? '';
$gender = $_POST['gender'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

$sql = "UPDATE users SET 
            name = '$name', 
            gender = '$gender', 
            birthday = '$birthday', 
            phone = '$phone', 
            address = '$address' 
        WHERE email = '$email'";

if (mysqli_query($conn, $sql)) {
    header("Location: Profile.php");
    exit();
} else {
    echo "Error updating profile: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
