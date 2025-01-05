<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: staffpage1.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $staff_id = $_POST['staff_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO staff (staff_id, email, password) VALUES ('$staff_id', '$email', '$hashedPassword')";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        ?>
            <script>
    alert("Registration Successful.");
    function navigateToPage() {
        window.location.href = 'stafflogin.php';
    }
    window.onload = function() {
        navigateToPage();
    }
</script>
        <?php 
    } else {
       echo "<script> alert('Registration Failed. Try Again');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Registration</title>
    <link rel="stylesheet" href="staff/staffstyle2.css">
    
</head>
<body>
<div class="train-background"></div>
        <form method="post" action="registration.php">
        <label for="">First Name:</label><br>
            <input type="text" name="first_name" placeholder="Enter Your First Name" required><br><br>

            <label for="email">Last Name:</label><br>
            <input type="text" name="last_name" placeholder="Enter Your Last Name" required><br><br>

            <label for="staff_id">Staff ID:</label><br>
            <input type="text" name="staff_id" placeholder="Enter Staff ID" required><br><br>

            <label for="email">Email:</label><br>
            <input type="text" name="email" placeholder="Enter Your Email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" placeholder="Enter Password" required><br><br>
            <input type="submit" name="register" value="Register"><br><br>
            <label>Already have an account? </label><a href="stafflogin.php">Login</a>
        </form>
    </div>

</body>
</html> 
