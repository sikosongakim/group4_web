<?php 
session_start(); // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Get the logged-in staff's ID
$staff_id = $_SESSION['staff_id'];

// Initialize message variable
$message = "";

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_date = $_POST['current_date'];
    $requested_date = $_POST['requested_date'];
    $shift = $_POST['shift'];  // Store selected shift
    $reason = $_POST['reason'];

    // Validate shift value
    $valid_shifts = ['5:00-11:00', '11:00-17:00', '17:00-23:00'];
    if (!in_array($shift, $valid_shifts)) {
        $message = "Invalid shift value.";
    } else {
        try {
            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO schedule_requests (staff_id, `current_date`, requested_date, shift, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
            // Execute the statement
            $stmt->execute([$staff_id, $current_date, $requested_date, $shift, $reason]);
            $message = "Schedule change request submitted successfully.";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage(); // Capture the error message
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Schedule Change</title>
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
            <a class="active" href="staffpage4.php">Change Schedule</a>
            <a href="staffpage5.php">Request Leave</a>
            <div class="profile-dropdown">
                <a href="#profile" class="profile-icon">
                    <i class="fas fa-user"></i> <!-- User icon -->
                </a>
                <div class="dropdown-menu">
                    <a href="staffpage2.php">Profile</a>
                    <a href="stafflogout.php">Log Out</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Red Header -->
    <header class="header red-header"></header>

    <!-- Train Background with Form Container -->
    <div class="train-background">
        <img src="train.jpg" alt="Train">
    </div>

    <!-- Request Form Container -->
    <div class="schedule-status">
        <h2>Request Schedule Change</h2>
        <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>

        <form action="staffpage4.php" method="POST">
            <label for="current_date">Current Schedule Date:</label>
            <input type="date" name="current_date" required><br>

            <label for="requested_date">Requested New Schedule Date:</label>
            <input type="date" name="requested_date" required><br>

            <label for="shift">Requested Shift:</label>
            <select name="shift" required>
                <option value="5:00-11:00">5:00-11:00</option>
                <option value="11:00-17:00">11:00-17:00</option>
                <option value ="17:00-23:00">17:00-23:00</option>
            </select><br>

            <label for="reason">Reason for Change:</label>
            <textarea name="reason" rows="4" required></textarea><br>

            <button type="submit">Submit Request</button>
        </form>
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