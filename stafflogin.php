<?php
// Establish connection to SQLite database
$db = new SQLite3('ets.db');

// Get submitted form data
$staffid = $_POST['staffid'];
$password = $_POST['password'];

// Prepare the query to check the credentials
$stmt = $db->prepare('SELECT * FROM staff WHERE staffid = :staffid AND password = :password');
$stmt->bindValue(':staffid', $staffid, SQLITE3_TEXT);
$stmt->bindValue(':password', $password, SQLITE3_TEXT);

// Execute the query
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

// Check if user is found
if ($user) {
    // Successful login, redirect to staff dashboard or staff's page
    echo "Login Successful!";
    // Redirect to staff dashboard
    header('Location: staff_dashboard.php');
} else {
    // Invalid credentials
    echo "Invalid credentials!";
}
?>
