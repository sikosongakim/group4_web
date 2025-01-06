<?php
session_start(); // Start the session

// Include database configuration
include('db_connection.php');

// Initialize error message
$error = "";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize the submitted credentials
    $staff_id = htmlspecialchars(trim($_POST['staff_id']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validate staff_id (ensure it's an integer)
    if (!filter_var($staff_id, FILTER_VALIDATE_INT)) {
        $error = "Invalid ID or password.";
    } else {
        try {
            // Prepare and execute the query to fetch the staff user
            $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = :staff_id");
            $stmt->bindParam(':staff_id', $staff_id, PDO::PARAM_INT);
            $stmt->execute();
            $staff = $stmt->fetch();

            if ($staff && password_verify($password, $staff['password'])) {
                session_regenerate_id(true); // Prevent session fixation

                // Store staff data in the session
                $_SESSION['staff_id'] = $staff['staff_id'];
                $_SESSION['staff_name'] = $staff['first_name'] . ' ' . $staff['last_name']; // Optional: Full staff name

                // Redirect to staff dashboard
                header('Location: staffpage1.php');
                exit();
            } else {
                $error = "Invalid ID or password."; // General error message
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
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
    <div class="login-container">
        <h1>Staff Login</h1>
        <form action="stafflogin.php" method="POST" class="login-form">
            <label for="staff_id">Staff ID:</label>
            <input type="text" name="staff_id" required placeholder="Enter your ID"><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required placeholder="Enter your password"><br>
            <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

