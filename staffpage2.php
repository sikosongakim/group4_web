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

// Get the staff ID from session
$staff_id = $_SESSION['staff_id'];

// Query to get staff details from the database
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Handle profile update (if form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $shift = $_POST['shift'];

    // Update staff details in database
    $update_stmt = $conn->prepare("UPDATE staff SET first_name = ?, last_name = ?, email = ?, gender = ?, position = ?, shift = ? WHERE staff_id = ?");
    $update_stmt->bind_param("ssssssi", $first_name, $last_name, $email, $gender, $position, $shift, $staff_id);
    $update_stmt->execute();

    // Redirect after update
    header('Location: staffpage1.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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

    <!-- Train Background -->
    <div class="train-background">
        <img src="train.jpg" alt="Train">
    </div>

    <!-- Profile Form Section (Centered) -->
    <div class="schedule-container profile-form-container">
        <h2>Edit Profile</h2>
        <form method="POST" action="staffpage2.php">
            <table class="status-table">
                <tr>
                    <td><label for="first_name">First Name: </label></td>
                    <td><input type="text" name="first_name" value="<?php echo $staff['first_name']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="last_name">Last Name: </label></td>
                    <td><input type="text" name="last_name" value="<?php echo $staff['last_name']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="email">Email: </label></td>
                    <td><input type="email" name="email" value="<?php echo $staff['email']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="gender">Gender: </label></td>
                    <td>
                        <select name="gender">
                            <option value="Male" <?php if ($staff['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($staff['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ($staff['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="position">Position: </label></td>
                    <td>
                        <select name="position">
                            <option value="Driver" <?php if ($staff['position'] == 'Driver') echo 'selected'; ?>>Driver</option>
                            <option value="Stewardess" <?php if ($staff['position'] == 'Stewardess') echo 'selected'; ?>>Stewardess</option>
                            <option value="Customer Service" <?php if ($staff['position'] == 'Customer Service') echo 'selected'; ?>>Customer Service</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="shift">Shift: </label></td>
                    <td>
                        <input type="text" name="shift" value="<?php echo $staff['shift']; ?>" required>
                    </td>
                </tr>
            </table>

            <button type="submit">Update Profile</button>
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
