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

if (!isset($data['name'])) {
    echo json_encode(["success" => false, "error" => "Missing product name"]);
    exit;
}

$product_name = $data['name'];

$stmt = $conn->prepare("DELETE FROM user_cart WHERE user_id = ? AND product_name = ?");
$stmt->bind_param("is", $user_id, $product_name);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to remove item"]);
}
?>
