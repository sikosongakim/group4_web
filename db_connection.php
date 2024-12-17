<?php
$servername = "localhost"; // Default MySQL server
$username = "root";        // Default MySQL username
$password = "";            // Default MySQL password (leave empty for XAMPP/WAMP)
$dbname = "ets";           // Ensure this matches the database name in phpMyAdmin

try {
    // Create a MySQL database connection using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>


