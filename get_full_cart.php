<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT product_name, price, image, quantity, offer FROM user_cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = [
        'name' => $row['product_name'],
        'price' => (float)$row['price'],
        'image' => $row['image'],
        'quantity' => (int)$row['quantity'],
        'offer' => $row['offer']
    ];
}

echo json_encode($cart);
