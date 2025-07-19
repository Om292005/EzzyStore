<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ezzystore";

header('Content-Type: application/json');

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed."]);
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$pass = $_POST['password'];
$phone = $_POST['phone'];

// Check for duplicate email
$check_sql = "SELECT id FROM users WHERE email = '$email'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "User already exists with this email. Use another email."]);
} else {
    $sql = "INSERT INTO users (name, email, password, phone) VALUES ('$name', '$email', '$pass', '$phone')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
}

$conn->close();
?>
