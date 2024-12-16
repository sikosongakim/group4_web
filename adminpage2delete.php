<?php 
session_start(); // Start the session

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Check if the id is set in the URL
if (isset($_GET['staff_id'])) {
    $staff_id = $_GET['staff_id'];

    // First, delete related leave requests
    $delete_leave_requests_sql = "DELETE FROM leave_requests WHERE staff_id = ?";
    $stmt = $conn->prepare($delete_leave_requests_sql);
    $stmt->bind_param("i", $staff_id);
    if ($stmt->execute()) {
        // Now, delete related schedule requests
        $delete_schedule_requests_sql = "DELETE FROM schedule_requests WHERE staff_id = ?";
        $stmt = $conn->prepare($delete_schedule_requests_sql);
        $stmt->bind_param("i", $staff_id);
        if ($stmt->execute()) {
            // Now, delete related schedules
            $delete_schedules_sql = "DELETE FROM schedules WHERE staff_id = ?";
            $stmt = $conn->prepare($delete_schedules_sql);
            $stmt->bind_param("i", $staff_id);
            if ($stmt->execute()) {
                // Now, delete the staff record
                $delete_staff_sql = "DELETE FROM staff WHERE staff_id = ?";
                $stmt = $conn->prepare($delete_staff_sql);
                $stmt->bind_param("i", $staff_id);
                if ($stmt->execute()) {
                    echo "<script>alert('The staff and their related schedules, schedule requests, and leave requests were deleted successfully!'); window.location.href='adminpage2.php';</script>";
                } else {
                    echo "Error deleting staff: " . $conn->error;
                }
            } else {
                echo "Error deleting related schedules: " . $conn->error;
            }
        } else {
            echo "Error deleting related schedule requests: " . $conn->error;
        }
    } else {
        echo "Error deleting related leave requests: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No ID provided";
}

// Close the connection
$conn->close();
?>
