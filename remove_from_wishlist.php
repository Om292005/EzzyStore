<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'])) {
    echo json_encode(['success' => false, 'error' => 'Missing product name']);
    exit;
}

$name = $data['name'];

$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_name = ?");
$stmt->bind_param("is", $user_id, $name);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error']);
}
