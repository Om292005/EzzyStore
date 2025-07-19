<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch latest delivery ID for this user
$delivery_sql = "SELECT id FROM delivery WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$delivery_stmt = $conn->prepare($delivery_sql);
$delivery_stmt->bind_param("i", $user_id);
$delivery_stmt->execute();
$delivery_result = $delivery_stmt->get_result();

if (!$delivery_result || $delivery_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Delivery details not found.']);
    exit;
}

$delivery_row = $delivery_result->fetch_assoc();
$delivery_id = $delivery_row['id'];

// You can replace this with the actual cart total (for now hardcoded)
$total_amount = 999.00;
$payment_status = "Paid";
$delivery_status = "Processing";

// Insert into orders table
$order_sql = "INSERT INTO orders (user_id, delivery_id, total_amount, payment_status, delivery_status)
              VALUES (?, ?, ?, ?, ?)";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("iisss", $user_id, $delivery_id, $total_amount, $payment_status, $delivery_status);

if ($order_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order placed successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order.']);
}
?>
