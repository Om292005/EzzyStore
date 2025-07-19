<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ezzystore";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ? AND password = ?");
    if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit();
}
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_id'] = $user['id']; // <-- THIS IS MISSING
    echo json_encode(["success" => true]);
}else {
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    }

    $stmt->close();
}

$conn->close();
?>
