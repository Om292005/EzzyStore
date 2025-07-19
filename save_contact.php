<?php
session_start();

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "ezzystore");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = $_POST['name'] ?? '';
$subject = $_POST['subject'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? ($_SESSION['email'] ?? '');
$comment = $_POST['message'] ?? '';

// First check if feedback already exists for this user with comment NULL
$check_sql = "SELECT * FROM feedback WHERE user_email = ? AND comment IS NULL LIMIT 1";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $email);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Update the existing row
    $update_sql = "UPDATE feedback SET comment = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $comment, $row['id']);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
} else {
    // Insert a new row
    $insert_sql = "INSERT INTO feedback (user_email, comment) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ss", $email, $comment);
    mysqli_stmt_execute($insert_stmt);
    mysqli_stmt_close($insert_stmt);
}

mysqli_stmt_close($check_stmt);
mysqli_close($conn);

// Redirect after success
header("Location: ContactUsConfirm.html");
exit();
?>
