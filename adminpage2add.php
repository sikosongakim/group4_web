<?php   
session_start(); // Start the session

/* Check if admin is logged in */
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $shift = $_POST['shift'];
    $off_day = $_POST['off_day']; // Add the off_day from the form

    // Insert staff into the staff table
    $sql = "INSERT INTO staff (first_name, last_name, email, password, gender, position, shift) 
            VALUES ('$first_name', '$last_name', '$email', '$password', '$gender', '$position', '$shift')";
    
    if ($conn->query($sql)) {
        // Get the last inserted staff_id
        $staff_id = $conn->insert_id;

        // Insert a default schedule for the new staff member
        $work_date = date('Y-m-d'); // Use today's date for the work date
        $status = 'Active'; // You can set this to Active or any default status
        $sql_schedule = "INSERT INTO schedules (staff_id, work_date, shift, status, off_day) 
                         VALUES ('$staff_id', '$work_date', '$shift', '$status', '$off_day')";

        if ($conn->query($sql_schedule)) {
            // Redirect to admin page or success page
            header("Location: adminpage2.php");
        } else {
            echo "Error adding schedule: " . $conn->error;
        }
    } else {
        echo "Error adding staff: " . $conn->error;
    }
}
?>

<!-- HTML form for adding staff -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <link rel="stylesheet" href="admin/adminstyle4.css">
</head>
<body>
    <div class="addstaff-container">
        <h1>Add Staff</h1>
        <form method="POST">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <label>Gender:</label>
            <select name="gender">
                <option disabled selected value> -- Select a gender -- </option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <label>Position:</label>
            <select name="position">
                <option disabled selected value> -- Select a position -- </option>
                <option value="Driver">Driver</option>
                <option value="Stewardess">Stewardess</option>
                <option value="Customer Service">Customer Service</option>
            </select>
            <label>Shift:</label>
            <select name="shift">
                <option disabled selected value> -- Select a shift -- </option>
                <option value="5:00-11:00">5:00-11:00</option>
                <option value="11:00-17:00">11:00-17:00</option>
                <option value="17:00-23:00">17:00-23:00</option>
            </select>
            <label>Off Day:</label>
            <select name="off_day">
                <option disabled selected value> -- Select an off day -- </option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="submit" value="Add Staff">
        </form>
    </div>
</body>
</html>
