<?php
session_start(); // Start the session

// Include database configuration
include('config.php');

// Initialize error message
$error = "";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize the submitted credentials
    $admin_username = htmlspecialchars(trim($_POST['admin_username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check the database for the admin
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $admin_username); // Bind the username
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) { // Use password_verify for hashed passwords
            session_regenerate_id(true); // Prevent session fixation attacks
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['username'];

            // Redirect to admin dashboard
            header('Location: adminpage1.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin/adminstyle2.css">
</head>
<body class="login-page">
<div class="train-background"></div>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <form action="adminlogin.php" method="POST" class="login-form">
            <label for="admin_username">Username:</label>
            <input type="text" name="admin_username" required placeholder="Enter your username"><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password"><br>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>


