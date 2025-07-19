<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['quantity'])) {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
    exit;
}

$product_name = $data['name'];
$quantity = (int)$data['quantity'];

if ($quantity < 1) {
    echo json_encode(["success" => false, "error" => "Invalid quantity"]);
    exit;
}

$stmt = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_name = ?");
$stmt->bind_param("iis", $quantity, $user_id, $product_name);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to update quantity"]);
}
?>
