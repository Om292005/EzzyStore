<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    die("User not logged in.");
}

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "ezzystore");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_email = $_SESSION['email'];
$rating = $_POST['rating'] ?? null;

if ($rating === null) {
    die("No rating submitted.");
}

// Step 1: Check for existing row with same email where rating is 0 or NULL
$check_sql = "SELECT id FROM feedback WHERE user_email = ? AND (rating = 0 OR rating IS NULL) LIMIT 1";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $user_email);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Step 2: Update existing row
    $update_sql = "UPDATE feedback SET rating = ?, submitted_at = CURRENT_TIMESTAMP WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $rating, $row['id']);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
} else {
    // Step 3: Insert new row
    $insert_sql = "INSERT INTO feedback (user_email, rating) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "si", $user_email, $rating);
    mysqli_stmt_execute($insert_stmt);
    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);

// Redirect or show confirmation
header("Location: FeedbackConfirm.html");
exit();
?>
