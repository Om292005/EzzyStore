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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // Fetch the stored hashed password for this email
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the plain-text password against the stored hash
        if (password_verify($pass, $user['password'])) {
            $_SESSION['email']   = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    }

    $stmt->close();
}

$conn->close();
?>
