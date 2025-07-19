<?php
session_start();

header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            o.order_id AS order_id, 
            o.total_amount, 
            o.payment_status, 
            o.delivery_date, 
            o.time_slot, 
            d.name,
            d.address,
            d.pincode,
            d.phone,
            d.delivery_status
        FROM orders o
        JOIN delivery d ON o.delivery_id = d.id
        WHERE o.user_id = ?
        ORDER BY o.order_id DESC"; // Make sure this column name is correct

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(['success' => true, 'orders' => $orders]);
?>
