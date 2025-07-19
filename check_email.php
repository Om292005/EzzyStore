<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ezzystore";

header('Content-Type: application/json');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["exists" => false]);
    exit();
}

$email = $_POST['email'];
$sql = "SELECT id FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(["exists" => true]);
} else {
    echo json_encode(["exists" => false]);
}

$conn->close();
?>
