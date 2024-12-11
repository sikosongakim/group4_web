<?php
session_start(); // Start the session

// Include database configuration
include('config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted credentials
    $admin_username = $_POST['admin_username'];
    $password = $_POST['password'];

    // Prepare and execute the query to validate credentials
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $admin_username, $password); // 's' for strings
    $stmt->execute();
    $result = $stmt->get_result();

    // If valid admin, store session and redirect
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['admin_id']; // Store admin ID in session
        $_SESSION['admin_name'] = $admin['name']; // Optional: Store admin name

        // Redirect to admin page
        header('Location: adminpage1.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin/adminstyle1.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
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
