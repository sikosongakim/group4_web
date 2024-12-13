<?php 
session_start(); // Start the session

/* Check if admin is logged in */
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Check if the id is set in the URL
if (isset($_GET['staff_id'])) {
    $staff_id = $_GET['staff_id'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('The staff is deleted successfully!'); window.location.href='adminpage2.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No ID provided";
}

// Close the connection
$conn->close();
?>