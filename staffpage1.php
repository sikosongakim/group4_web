<?php 
session_start();  // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // If not logged in, redirect to login page
    header('Location: stafflogin.php');
    exit();
}
// Set session variable to prevent video pop-up after login
if (!isset($_SESSION['video_shown'])) {
    $_SESSION['video_shown'] = true; // Video will be shown only once after login
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

// Query to get leave request details and leave_status
$leave_stmt = $conn->prepare("SELECT start_date, end_date, leave_status FROM leave_requests WHERE staff_id = ? ORDER BY leave_request_id DESC LIMIT 1");
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

<!-- Video Container Popup -->
<div id="videoContainer" class="video-container">
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <video controls autoplay>
                <source src="train.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</div>

    <!-- Train Background with Status Table -->
    <div class="train-background">
        <img src="train.jpg" alt="Train">
    </div>

    <!-- Schedule Status Table -->
    <div class="schedule-status">
        <h2>Request Status</h2>
        
        <!-- Table for Leave Requests -->
        <table class="status-table">
            <tr>
                <th>Leave Status</th>  <!-- Status column comes first -->
                <th>Leave Dates</th>
            </tr>
            <?php if ($leave_request): ?>
            <tr>
                <td><?php echo htmlspecialchars($leave_request['leave_status']); ?></td>  <!-- Display leave_status -->
                <td>From <?php echo htmlspecialchars($leave_request['start_date']); ?> to <?php echo htmlspecialchars($leave_request['end_date']); ?></td>
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
            <a href="ScheduleFAQ.php">Staff Schedule FAQ</a>
            <a href="LeaveFAQ.php">Staff Leave FAQ</a>
            </nav>
        </div>
    </footer>
    <script>
// Get modal elements
const videoContainer = document.getElementById("videoContainer");
const closeModal = document.getElementById("closeModal");
const videoElement = document.querySelector("video");  // Get the video element

// Check if the session variable video_shown is true
if (<?php echo isset($_SESSION['video_shown']) && $_SESSION['video_shown'] ? 'true' : 'false'; ?>) {
    // Show the modal when the page loads (only when the session variable is set)
    window.onload = function () {
        videoContainer.style.display = "flex"; // Show the container and modal
    };

    // Hide the modal and stop the video when the close button is clicked
    closeModal.onclick = function () {
        videoContainer.style.display = "none"; // Hide the container and modal
        videoElement.pause();  // Stop the video
        videoElement.currentTime = 0;  // Optionally reset the video to the beginning
    };

    // Hide the modal and stop the video if the user clicks outside the modal content
    window.onclick = function (event) {
        if (event.target === videoContainer) {
            videoContainer.style.display = "none"; // Close when clicking outside the modal
            videoElement.pause();  // Stop the video
            videoElement.currentTime = 0;  // Optionally reset the video to the beginning
        }
    };
}
    </script>
</body>
</html>
