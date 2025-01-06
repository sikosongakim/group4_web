<?php
session_start(); // Start the session

// Include database configuration
include('db_connection.php');

// Initialize error message
$error = "";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize the submitted credentials
    $admin_username = htmlspecialchars(trim($_POST['admin_username']));
    $password = htmlspecialchars(trim($_POST['password']));

    try {
        // Prepare and execute the query to fetch the admin user
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username");
        $stmt->bindParam(':username', $admin_username);
        $stmt->execute();
        $admin = $stmt->fetch();

        if ($admin) {
            // Verify the password using password_verify()
            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true); // Prevent session fixation attacks
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['username'];

                // Redirect to admin dashboard
                header('Location: adminpage1.php');
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
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
<body>
<div class="train-background"></div>
    <div class="login-container">
        <h1>Admin Login</h1>
        <form action="adminlogin.php" method="POST" class="login-form">
            <label for="admin_username">Username:</label>
            <input type="text" name="admin_username" required placeholder="Enter your username"><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password"><br>
            <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
