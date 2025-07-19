<?php
ob_start();
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['price']) || !isset($data['image'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Incomplete data']);
    exit;
}

// Check if item already in wishlist
$stmt = $conn->prepare("SELECT id FROM user_wishlist WHERE user_id = ? AND product_name = ?");
$stmt->bind_param("is", $user_id, $data['name']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Remove from wishlist
    $stmt = $conn->prepare("DELETE FROM user_wishlist WHERE user_id = ? AND product_name = ?");
    $stmt->bind_param("is", $user_id, $data['name']);
    $stmt->execute();
    echo json_encode(['removed' => true]);
} else {
    // Add to wishlist
    $stmt = $conn->prepare("INSERT INTO user_wishlist (user_id, product_name, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $data['name'], $data['price'], $data['image']);
    if ($stmt->execute()) {
        echo json_encode(['added' => true]);
    } else {
        echo json_encode(['error' => 'DB error']);
    }
}
?>
