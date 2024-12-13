<?php
session_start();  // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // If not logged in, redirect to login page
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Get the logged-in staff's ID
$staff_id = $_SESSION['staff_id'];

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date']; // Start date for leave
    $end_date = $_POST['end_date']; // End date for leave
    $reason = $_POST['reason']; // Reason for leave

    // Insert the leave request into the leave_requests table
    $stmt = $conn->prepare("INSERT INTO leave_requests (staff_id, start_date, end_date, reason, leave_status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isss", $staff_id, $start_date, $end_date, $reason);
    $stmt->execute();

    $message = "Leave request submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Leave</title>
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
            <a href="staffpage1.php">Home</a>
            <a href="staffpage3.php">View Schedule</a>
            <a href="staffpage4.php">Change Schedule</a>
            <a href="staffpage5.php">Request Leave</a>
            <div class="profile-dropdown">
                <a href="#profile" class="profile-icon">
                    <i class="fas fa-user"></i> <!-- User icon -->
                </a>
                <div class="dropdown-menu">
                    <a href="staffpage2.php">Profile</a> <!-- Link to edit profile -->
                    <a href="stafflogout.php">Log Out</a> <!-- Log out link -->
                </div>
            </div>
        </nav>
    </header>

    <!-- Red Header -->
    <header class="header red-header"></header>

    <!-- Train Background with Status Table -->
    <div class="train-background">
        <img src="train.jpg" alt="Train">
    </div>

    <!-- Leave Request Form -->
    <div class="schedule-status">
        <h2>Request Leave</h2>
        
        <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

        <form method="POST" action="staffpage5.php">
            <label for="start_date">Leave Start Date:</label>
            <input type="date" name="start_date" required><br>

            <label for="end_date">Leave End Date:</label>
            <input type="date" name="end_date" required><br>

            <label for="reason">Reason for Leave:</label>
            <textarea name="reason" rows="4" required></textarea><br>

            <button type="submit">Submit Leave Request</button>
        </form>
    </div>

    <!-- Blue Footer -->
    <footer class="blue-footer">
        <div class="footer-content">
            <p>&copy; 2024 ETS Staff Schedule. All rights reserved.</p>
            <nav class="footer-links">
                <a href="#Add navigation">Navigation</a>
                <a href="#Add navigation">Navigation</a>
                <a href="#Add navigation">Navigation</a>
            </nav>
        </div>
    </footer>
</body>
</html>
