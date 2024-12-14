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
    <link rel="stylesheet" href="staff/staffstyle4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
    <div class="container">
        <h1>Staff Schedule Frequently Asked Question</h1>
        <div class="faq-list">
            <div class="faq">
                <div class="ques">
                    <p>How can I request a schedule change?</p>
                    <i class='bx bx-plus'></i>
                </div>
                <div class="ans">
                    <p>To request a schedule change, go to the Change Schedule page. Fill in the Current 
                    Schedule Date, Requested New Schedule Date, Requested Shift, and Reason for 
                    Change, then click Submit Request. Your request will be reviewed by the admin.
                    </p>
                </div>
            </div>
            <div class="faq">
                <div class="ques">
                    <p>How do I view my current work schedule?</p>
                    <i class='bx bx-plus'></i>
                </div>
                <div class="ans">
                <p>To view your current schedule, click on the View Schedule tab in the navigation menu. 
                    Your assigned dates, shifts, and any updates will be displayed.
                    </p>
                </div>
            </div>
            <div class="faq">
                <div class="ques">
                    <p>What happens after I submit a schedule change request?</p>
                    <i class='bx bx-plus'></i>
                </div>
                <div class="ans">
                <p>Once you submit a schedule change request, it will be sent to the administrator for approval. 
                    The status of your request will be displayed on the Home page.
                    </p>
                </div>
            </div>
            <div class="faq">
                <div class="ques">
                    <p>Can I request a schedule change for multiple days at once?</p>
                    <i class='bx bx-plus'></i>
                </div>
                <div class="ans">
                <p>Yes, you can only request a schedule change for one date at a time. If you need to change 
                    multiple dates, please submit separate requests for each date to ensure proper processing.
                    </p>
                </div>
            </div>
            <div class="faq">
                <div class="ques">
                    <p>What should I do if I notice an error in my schedule?</p>
                    <i class='bx bx-plus'></i>
                </div>
                <div class="ans">
                <p>If you notice an error in your schedule, please contact the administrator immediately or 
                    submit a Request Change Schedule with the correct details. You can also use the "Reason for Change" 
                    field to explain the issue.
                    </p>
                </div>
            </div>     
        </div>
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
const faqs = document.querySelectorAll(".faq");

faqs.forEach((faq) => {
    faq.addEventListener("click", () => {
        // Close all other FAQs
        faqs.forEach((item) => {
            if (item !== faq) {
                item.classList.remove("active");
            }
        });

        // Toggle the clicked FAQ
        faq.classList.toggle("active");
    });
});

    </script>
</body>
</html>
