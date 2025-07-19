<?php
$servername = "localhost";
$username = "root"; // or your MySQL username
$password = "";     // or your MySQL password
$dbname = "ezzystore"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
$conn = new mysqli("localhost", "root", "", "ezzystore");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

?>
