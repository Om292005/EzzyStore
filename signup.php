<?php
session_start();
header('Content-Type: application/json');

// Suppress warnings so they don't corrupt JSON output
error_reporting(0);
ini_set('display_errors', 0);

$conn = new mysqli("localhost", "root", "", "ezzystore");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$name  = trim($_POST['name']  ?? '');
$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password']   ?? '';
$phone = trim($_POST['phone'] ?? '');

// Check for duplicate email using prepared statement
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$check) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit();
}
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "User already exists with this email. Use another email."]);
    $check->close();
    exit();
}
$check->close();

// Hash the password before storing
$hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

// Insert new user with prepared statement
$stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit();
}
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $phone);

if ($stmt->execute()) {
    // Set session so user is logged in immediately after signup
    $_SESSION['email']   = $email;
    $_SESSION['user_id'] = $conn->insert_id;
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
