<?php
session_start(); // Start the session

// Include database configuration
include('config.php');

// Initialize variables
$error = "";
$login_attempts_key = 'admin_login_attempts';
$max_attempts = 5;
$lockout_time = 300; // 5 minutes

// Rate limiting: Check login attempts
if (!isset($_SESSION[$login_attempts_key])) {
    $_SESSION[$login_attempts_key] = ['count' => 0, 'last_attempt' => time()];
} else {
    $attempts = $_SESSION[$login_attempts_key];
    if ($attempts['count'] >= $max_attempts && (time() - $attempts['last_attempt']) < $lockout_time) {
        die("Too many login attempts. Please try again after " . ($lockout_time - (time() - $attempts['last_attempt'])) . " seconds.");
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $admin_username = htmlspecialchars(trim($_POST['admin_username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Prepare and execute the query to validate credentials
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If valid admin, verify password
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            // Successful login: Set session variables
            session_regenerate_id(true); // Prevent session fixation attacks
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];

            // Reset login attempts
            $_SESSION[$login_attempts_key] = ['count' => 0, 'last_attempt' => time()];

            // Redirect to admin page
            header('Location: adminpage1.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
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

