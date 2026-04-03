<?php
// db.php — single connection, no duplicates
$conn = new mysqli("localhost", "root", "", "ezzystore");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}
?>
