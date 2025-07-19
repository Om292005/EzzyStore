<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit payment.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$card_holder = $_POST['card_holder'] ?? '';
$card_number = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
$expiry_date = $_POST['expiry_date'] ?? '';
$cvv = $_POST['cvv'] ?? '';

// Check if card is expired
$expiry_parts = explode('-', $expiry_date); // [YYYY, MM]
if (count($expiry_parts) === 2) {
    $exp_year = (int)$expiry_parts[0];
    $exp_month = (int)$expiry_parts[1];
    $current_year = (int)date('Y');
    $current_month = (int)date('m');

    if ($exp_year < $current_year || ($exp_year == $current_year && $exp_month < $current_month)) {
        echo json_encode(['success' => false, 'message' => 'Card has expired.']);
        exit;
    }
}

if (!$card_holder || !preg_match('/^[a-zA-Z ]+$/', $card_holder)) {
    echo json_encode(['success' => false, 'message' => 'Invalid cardholder name.']);
    exit;
}

if (!preg_match('/^\d{16}$/', $card_number)) {
    echo json_encode(['success' => false, 'message' => 'Invalid card number.']);
    exit;
}

if (!preg_match('/^\d{3}$/', $cvv)) {
    echo json_encode(['success' => false, 'message' => 'Invalid CVV.']);
    exit;
}

if (!$expiry_date) {
    echo json_encode(['success' => false, 'message' => 'Expiry date is required.']);
    exit;
}

// Insert into payments table
$stmt = $conn->prepare("INSERT INTO payments (user_id, card_holder, card_number, expiry_date, cvv, status) VALUES (?, ?, ?, ?, ?, 'Paid')");
$stmt->bind_param("issss", $user_id, $card_holder, $card_number, $expiry_date, $cvv);

if ($stmt->execute()) {
    // Get latest delivery
    $delivery_sql = "SELECT id, delivery_date, time_slot FROM delivery WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $delivery_stmt = $conn->prepare($delivery_sql);
    $delivery_stmt->bind_param("i", $user_id);
    $delivery_stmt->execute();
    $delivery_result = $delivery_stmt->get_result();

    if ($delivery = $delivery_result->fetch_assoc()) {
        $delivery_id = $delivery['id'];
        $delivery_date = $delivery['delivery_date'];
        $time_slot = $delivery['time_slot'];

        // Calculate total amount from cart
        $cart_stmt = $conn->prepare("SELECT SUM(price * quantity) AS total FROM user_cart WHERE user_id = ?");
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        $cart_data = $cart_result->fetch_assoc();
        $total_amount = $cart_data['total'] ?? 0.00;

        // Insert order
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, delivery_id, payment_status, delivery_date, time_slot, total_amount) VALUES (?, ?, 'Paid', ?, ?, ?)");
        $order_stmt->bind_param("iissd", $user_id, $delivery_id, $delivery_date, $time_slot, $total_amount);
        $order_stmt->execute();

        // Update delivery payment status
        $update_stmt = $conn->prepare("UPDATE delivery SET payment_status = 'Paid' WHERE id = ?");
        $update_stmt->bind_param("i", $delivery_id);
        $update_stmt->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Payment submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving payment.']);
}
?>
