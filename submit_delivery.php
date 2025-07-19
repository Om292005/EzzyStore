<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit delivery details.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Sanitize & validate inputs
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$name = clean_input($_POST['name'] ?? '');
$address = clean_input($_POST['address'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$pincode = clean_input($_POST['pincode'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$delivery_date = clean_input($_POST['date'] ?? '');
$time_slot = clean_input($_POST['time'] ?? '');

$errors = [];

// Server-side validation
if (!$name || !preg_match("/^[a-zA-Z ]+$/", $name)) {
    $errors[] = "Invalid name.";
}
if (!$address) {
    $errors[] = "Address is required.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email.";
}
if (!preg_match("/^\d{6}$/", $pincode)) {
    $errors[] = "Invalid pincode.";
}
if (!preg_match("/^\d{10}$/", $phone)) {
    $errors[] = "Invalid phone number.";
}
if (!$time_slot) {
    $errors[] = "Please select a valid time slot.";
}

// Date format validation
$date_parts = explode("-", $delivery_date);
if (count($date_parts) !== 3) {
    $errors[] = "Invalid delivery date format.";
} else {
    $delivery_date_sql = $delivery_date; // yyyy-mm-dd format
    $today = date('Y-m-d');

    if ($delivery_date_sql < $today) {
        $errors[] = "Delivery date cannot be in the past.";
    }

    // If delivery date is today, check if selected time slot has passed
    if ($delivery_date_sql === $today) {
        $current_hour = date('H');

        // Extract start time from slot (e.g. 09:00 AM - 11:00 AM)
        preg_match("/^(\d{2}):(\d{2})/", $time_slot, $matches);
        $slot_hour = isset($matches[1]) ? (int)$matches[1] : null;

        if ($slot_hour !== null && $slot_hour <= (int)$current_hour) {
            $errors[] = "Selected time slot has already passed.";
        }
    }
}

// Check last submission time from database
$check_stmt = $conn->prepare("SELECT created_at FROM delivery WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $last_time = strtotime($row['created_at']);
    $current_time = time();
    $time_diff = $current_time - $last_time;

    if ($time_diff < 600) {
        $minutes_remaining = floor((600 - $time_diff) / 60);
        $seconds_remaining = (600 - $time_diff) % 60;
        echo json_encode([
            'success' => false,
            'message' => "You can submit again in {$minutes_remaining} minutes and {$seconds_remaining} seconds."
        ]);
        exit;
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO delivery (user_id, name, address, email, pincode, phone, delivery_date, time_slot)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssss", $user_id, $name, $address, $email, $pincode, $phone, $delivery_date_sql, $time_slot);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Delivery details submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving delivery details.']);
}
?>
