<?php
session_start(); // Start the session

// Include database configuration
include('config.php');
require 'vendor/autoload.php'; // For 2FA library

use OTPHP\TOTP;

// Initialize variables
$error = "";
$login_attempts_key = 'login_attempts';
$max_attempts = 5;
$lockout_time = 100; // 5 minutes

// Rate limiting: Check login attempts
if (!isset($_SESSION[$login_attempts_key])) {
    $_SESSION[$login_attempts_key] = ['count' => 0, 'last_attempt' => time()];
} else {
    $attempts = $_SESSION[$login_attempts_key];
    if ($attempts['count'] >= $max_attempts && (time() - $attempts['last_attempt']) < $lockout_time) {
        die("Too many login attempts. Please try again after " . ($lockout_time - (time() - $attempts['last_attempt'])) . " seconds.");
    }
}

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $staff_id = htmlspecialchars(trim($_POST['staff_id']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Prepare and execute the query to fetch user data
    $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the staff exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            $_SESSION['staff_id'] = $row['staff_id'];

            // If 2FA is enabled, redirect to 2FA verification page
            if (!empty($row['totp_secret'])) {
                $_SESSION['temp_staff_id'] = $row['staff_id'];
                header('Location: verify_2fa.php');
                exit();
            }

            // Get today's date and staff's default shift
            $today = date("Y-m-d");
            $shift = $row['shift'];

            // Check if schedule exists for today
            $stmt_schedule = $conn->prepare("SELECT * FROM schedules WHERE staff_id = ? AND work_date = ?");
            $stmt_schedule->bind_param("is", $row['staff_id'], $today);
            $stmt_schedule->execute();
            $result_schedule = $stmt_schedule->get_result();

            // If no schedule exists for today, create one
            if ($result_schedule->num_rows == 0) {
                $stmt_insert_schedule = $conn->prepare("INSERT INTO schedules (staff_id, work_date, shift, status) VALUES (?, ?, ?, 'Scheduled')");
                $stmt_insert_schedule->bind_param("iss", $row['staff_id'], $today, $shift);
                $stmt_insert_schedule->execute();
            }

            // Reset login attempts and redirect to staff page
            $_SESSION[$login_attempts_key] = ['count' => 0, 'last_attempt' => time()];
            header('Location: staffpage1.php');
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid staff ID.";
    }

    // Increment login attempts
    $_SESSION[$login_attempts_key]['count'] += 1;
    $_SESSION[$login_attempts_key]['last_attempt'] = time();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="staff/staffstyle2.css">
</head>
<body>
<div class="train-background"></div>
    <!-- Staff Login Form -->
    <h1>Staff Login</h1>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form action="stafflogin.php" method="POST" class="login-form">
        <label for="staff_id">Staff ID: </label>
        <input type="text" name="staff_id" required placeholder="Eg. aa240192"><br>

        <label for="password">Password: </label>
        <input type="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>

