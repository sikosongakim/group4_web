<?php
$servername = "localhost"; // Change if not using localhost
$username = "root";        // Default username for XAMPP/WAMP
$password = "";            // Default password (empty for XAMPP/WAMP)
$dbname = "ets";           // Your database name

try {
    // Create a MySQL database connection using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
