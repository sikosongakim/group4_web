<?php
session_start(); // Start the session

// Include database configuration
include('config.php');

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted credentials
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];

    // Prepare and execute the query to check credentials
    $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ? AND password = ?");
    $stmt->bind_param("is", $staff_id, $password); // 'i' for integer, 's' for string
    $stmt->execute();
    $result = $stmt->get_result();

    // If valid staff, store session and redirect
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['staff_id'] = $row['staff_id'];

        // Get today's date and staff's default shift
        $today = date("Y-m-d");
        $shift = $row['shift'];  // Assuming the staff member has a default shift

        // Check if schedule exists for today
        $stmt_schedule = $conn->prepare("SELECT * FROM schedules WHERE staff_id = ? AND work_date = ?");
        $stmt_schedule->bind_param("is", $staff_id, $today);
        $stmt_schedule->execute();
        $result_schedule = $stmt_schedule->get_result();

        // If no schedule exists for today, create one
        if ($result_schedule->num_rows == 0) {
            $stmt_insert_schedule = $conn->prepare("INSERT INTO schedules (staff_id, work_date, shift, status) VALUES (?, ?, ?, 'Scheduled')");
            $stmt_insert_schedule->bind_param("iss", $staff_id, $today, $shift);
            $stmt_insert_schedule->execute();
        }

        // Redirect to staff page (schedule page)
        header('Location: staffpage1.php');
        exit();
    } else {
        $error = "Invalid staff ID or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
</head>
<body>
    <h2>Staff Login</h2>
    <form method="POST" action="stafflogin.php">
        <label for="staff_id">Staff ID: </label>
        <input type="text" name="staff_id" required>
        <label for="password">Password: </label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>

    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

</body>
</html>
