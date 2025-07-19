<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
session_start();
require 'db.php'; // your DB connection

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed', 'details' => $conn->connect_error]);
    exit;
}


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in', 'session' => $_SESSION]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Check if item already exists for user
$stmt = $conn->prepare("SELECT id FROM user_cart WHERE user_id = ? AND product_name = ?");
$stmt->bind_param("is", $user_id, $data['name']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update quantity
    $stmt = $conn->prepare("UPDATE user_cart SET quantity = quantity + 1 WHERE user_id = ? AND product_name = ?");
    $stmt->bind_param("is", $user_id, $data['name']);
} else {
    // Insert new item
    $stmt = $conn->prepare("INSERT INTO user_cart (user_id, product_name, price, image, quantity, offer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsis", $user_id, $data['name'], $data['price'], $data['image'], $data['quantity'], $data['offer']);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Unknown server error']);
exit;
}
?>
