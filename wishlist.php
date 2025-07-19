<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT product_name as name, price, image FROM user_wishlist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist = [];
while ($row = $result->fetch_assoc()) {
    $wishlist[] = $row;
}

echo json_encode(['success' => true, 'wishlist' => $wishlist]);
?>
