<?php
session_start();  // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // If not logged in, redirect to login page
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration to get staff info
include('config.php');

// Get the staff ID from session
$staff_id = $_SESSION['staff_id'];

// Query to get staff details from the database
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETS Staff Schedule</title>
    <link rel="stylesheet" href="staff/staffstyle1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
</head>
<body>
    <!-- Yellow Header -->
    <header class="header yellow-header">
        <div class="logo">
            <img src="ktm.png" alt="Logo">
        </div>
        <nav class="navbar">
            <a href="staffpage2.php">Edit Profile</a>
            <a href="staffpage3.php">View Schedule</a>
            <a href="staffpage4.php">Change Schedule</a>
            <a href="staffpage5.php">Request Leave</a>
            <a href="stafflogout.php">Logout</a>  <!-- Logout link -->
        </nav>
    </header>

    <!-- Red Header -->
    <header class="header red-header"></header>

    <!-- Train Background -->
    <div class="train-background">
        <img src="train.jpg" alt="Train">
    </div>

    <!-- Blue Footer -->
    <footer class="blue-footer">
        <div class="footer-content">
            <p>&copy; 2024 ETS Staff Schedule. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>