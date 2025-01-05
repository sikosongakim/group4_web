<?php
session_start(); // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // Redirect to login if not logged in
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Get the logged-in staff ID
$staff_id = $_SESSION['staff_id'];

// Get today's schedule for the logged-in staff, including shift and off_day
$stmt = $conn->prepare("SELECT shift, off_day FROM schedules WHERE staff_id = ? ORDER BY work_date DESC");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Schedule</title>
    <link rel="stylesheet" href="staff/staffstyle1.css">
    <link rel="stylesheet" href="staff/staffstyle3.css">
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
            <a class="active" href="staffpage3.php">View Schedule</a>
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

    <!-- Schedule Table Section (Centered) -->
    <div class="schedule-container">
        <h2>Your Schedule</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="status-table">
                <tr>
                    <th>Shift</th>
                    <th>Off Day</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['shift']; ?></td> <!-- Display Shift -->
                        <td><?php echo $row['off_day']; ?></td> <!-- Display Off Day -->
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No schedule found for today.</p>
        <?php endif; ?>
    </div>

    <!-- Blue Footer -->
    <footer class="blue-footer">
        <div class="footer-content">
            <p>&copy; 2024 ETS Staff Schedule. All rights reserved.</p>
            <nav class="footer-links">
            <a href="ScheduleFAQ.php">Staff Schedule FAQ</a>
            <a href="LeaveFAQ.php">Staff Leave FAQ</a>
            </nav>
        </div>
    </footer>

</body>
</html>
