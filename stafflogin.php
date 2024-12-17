<?php
session_start(); // Start the session

// Include database configuration
include('config.php');

// Initialize error message
$error = "";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize the submitted credentials
    $staff_id = htmlspecialchars(trim($_POST['staff_id']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check the database for the user
    $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id); // 'i' for integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if ($password === $row['password']) { // Plain text password
            $_SESSION['staff_id'] = $row['staff_id'];

            // Redirect to staff page
            header('Location: staffpage1.php');
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid staff ID.";
    }
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
        <label for="staff_id">Staff ID:</label>
        <input type="text" name="staff_id" required placeholder="Enter your staff ID"><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required placeholder="Enter your password"><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
