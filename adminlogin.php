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
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if ($password === $row['password']) { // Plain text password
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['username'];

            // Redirect to admin page
            header('Location: adminpage1.php');
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }
}
?>

