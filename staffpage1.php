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

// Query to get leave request status
$leave_stmt = $conn->prepare("SELECT leave_date, status FROM leave_requests WHERE staff_id = ? ORDER BY leave_request_id DESC LIMIT 1");
$leave_stmt->bind_param("i", $staff_id);
$leave_stmt->execute();
$leave_result = $leave_stmt->get_result();
$leave_request = $leave_result->fetch_assoc();

// Query to get schedule change request status
$schedule_stmt = $conn->prepare("SELECT current_date, requested_date, status FROM schedule_requests WHERE staff_id = ? ORDER BY request_id DESC LIMIT 1");
$schedule_stmt->bind_param("i", $staff_id);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();
$schedule_request = $schedule_result->fetch_assoc();
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

    <!-- Schedule Status Table -->
    <div class="schedule-status">
        <h2>Request Status</h2>
        
        <!-- Table for Leave Request Status -->
        <table class="status-table">
            <tr>
                <th>Leave Request Status</th>
                <th>Leave Date</th>
            </tr>
            <?php if ($leave_request): ?>
            <tr>
                <td><?php echo htmlspecialchars($leave_request['status']); ?></td>
                <td><?php echo htmlspecialchars($leave_request['leave_date']); ?></td>
            </tr>
            <?php else: ?>
            <tr>
                <td colspan="2">You have not requested any leave yet.</td>
            </tr>
            <?php endif; ?>
        </table>
        
        <!-- Table for Schedule Change Request Status -->
        <table class="status-table">
            <tr>
                <th>Schedule Change Request Status</th>
                <th>Requested Change</th>
            </tr>
            <?php if ($schedule_request): ?>
            <tr>
                <td><?php echo htmlspecialchars($schedule_request['status']); ?></td>
                <td>From <?php echo htmlspecialchars($schedule_request['current_date']); ?> to <?php echo htmlspecialchars($schedule_request['requested_date']); ?></td>
            </tr>
            <?php else: ?>
            <tr>
                <td colspan="2">You have not requested any schedule change yet.</td>
            </tr>
            <?php endif; ?>
        </table>
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
