<?php
session_start();

// Prevent access if not logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location: login&registration.html");
    exit();
}

// DB connection
$conn = mysqli_connect("localhost", "root", "", "ezzystore");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];
$sql = "SELECT name, email, phone, gender, birthday, address FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link rel="stylesheet" href="Profile.css">
    <script src="https://kit.fontawesome.com/bfe54b35a2.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <div class="leftbox">
        <nav>
            <a onclick="tabs(0)" class="tab active"><i class="fas fa-user"></i></a>
            <a onclick="tabs(1)" class="tab"><i class="far fa-credit-card"></i></a>
            
        </nav>
    </div>
  

    <div class="rightbox">
        <div class="Profile tabShow">
            <form action="update_profile.php" method="POST">
            
                <h1>Personal Information</h1>

                <h2>Name</h2>
                <input type="text" class="input" name="name" value="<?= htmlspecialchars($user['name']) ?>">

                <h2>Gender</h2>
                <input type="text" class="input" name="gender" value="<?= htmlspecialchars($user['gender'] ?? '') ?>">

                <h2>Birthday</h2>
                <input type="date" class="input" name="birthday" value="<?= htmlspecialchars($user['birthday'] ?? '') ?>">

                <h2>Email</h2>
                <input type="email" class="input" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

                <h2>Phone</h2>
                <input type="text" class="input" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

                <h2>Address</h2>
                <input type="text" class="input" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                
                <div class="button-row">
                    <button type="submit" class="btn">Update</button>
                    </form> <!-- close the update form properly -->

                    <form action="logout.php" method="POST">
                    <button type="submit" class="btn logout-btn">Logout</button>
                    </form>
                </div>

            
        </div>
        <div class="Payment tabShow" style="display: none;">
            <h1>Shipment Information</h1>
            <h2>Billing Address</h2>
            <input type="text" class="input" placeholder="402, Sector 19, Noida, Uttar Pradesh">
            <h2>Pincode</h2>
            <input type="text" class="input" placeholder="190125">
            <h2>Last Order Shipped On</h2>
            <input type="text" class="input" placeholder="January 10, 2022">
            <button class="btn">Update</button>
        </div>
    </div>
</div>

<script>
function tabs(index) {
    var tabs = document.getElementsByClassName("tabShow");
    var icons = document.getElementsByClassName("tab");

    for (var i = 0; i < tabs.length; i++) {
        tabs[i].style.display = "none";
        icons[i].classList.remove("active");
    }

    tabs[index].style.display = "block";
    icons[index].classList.add("active");
}
tabs(0); // Show first tab by default
</script>

</body>
</html>
