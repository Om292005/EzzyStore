<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login&registration.html");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "ezzystore");
$email = $_SESSION['email'];
$name = "";

if ($conn) {
    $res = mysqli_query($conn, "SELECT name FROM users WHERE email = '$email'");
    if ($row = mysqli_fetch_assoc($res)) {
        $name = $row['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | Ezzy Grocery Store</title>
    <link rel="stylesheet" href="homepage.css">
    <style>
        .welcome-section {
            max-width: 900px;
            margin: 50px auto;
            background-color: #ecf7ee;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 2px 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .welcome-section h1 {
            font-size: 2rem;
            color: #202020;
        }
        .welcome-section p {
            margin-top: 5px;
            font-size: 1.1rem;
            color: #555;
        }
        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 25px;
            background-color: #4eb060;
            color: white;
            text-transform: uppercase;
            border: none;
            border-radius: 30px;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background-color: #40aa54;
        }
        .logo {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            margin-top: 30px;
            color: #202020;
        }
        .logo span {
            color: #40aa54;
        }
    </style>
</head>
<body>

    <div class="logo">
        Ezzy <span>Grocery</span> Store<br>
        <img src="images/logo 1 edited.png" alt="Ezzy Grocery Store Logo" style="width: 150px; margin-top: 10px;">
    </div>

    <div class="welcome-section">
        <h1>Welcome, <?= htmlspecialchars($name) ?>!</h1>
        <p>Thank you for joining Ezzy Grocery Store.</p>
        <p>Enjoy shopping your daily essentials easily and quickly!</p>
        <a href="homepage.html" class="btn">Go to Homepage</a>
    </div>

</body>
</html>
