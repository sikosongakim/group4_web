<?php 
session_start(); // Start the session

/* Check if admin is logged in */
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

$staff_id = $_GET['staff_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $shift = $_POST['shift'];

    $sql = "UPDATE staff SET first_name='$first_name', last_name='$last_name', email='$email', password='$password', gender='$gender', position='$position', shift='$shift' WHERE staff_id=$staff_id";
    if ($conn->query($sql)) {
        header("Location: adminpage2.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$staff = $conn->query("SELECT * FROM staff WHERE staff_id=$staff_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <link rel="stylesheet" href="adminstyle3.css">
</head>
<body>
    <div class="editstaff-container">
        <h1>Edit Staff</h1>
        <form method="POST">
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo $staff['first_name']; ?>" required>
            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $staff['last_name']; ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $staff['email']; ?>" required>
            <label>Password:</label>
            <input type="password" name="password" value="<?php echo $staff['password']; ?>" required>
            <label>Gender:</label>
            <select name="gender">
            <option disabled selected value> -- Select a gender -- </option>
                <option value="Male" <?php if ($staff['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($staff['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>
            <label>Position:</label>
            <select name="position">
                <option disabled selected value> -- Select a position -- </option>
                <option value="Driver" <?php if ($staff['position'] == 'Driver') echo 'selected'; ?>>Driver</option>
                <option value="Stewardess" <?php if ($staff['position'] == 'Stewardess') echo 'selected'; ?>>Stewardess</option>
                <option value="Customer Service" <?php if ($staff['position'] == 'Customer Service') echo 'selected'; ?>>Customer Service</option>
            </select>
            <label>Shift:</label>
            <select name="shift">
                <option disabled selected value> -- Select a shift -- </option>
                <option value="5:00-11:00" <?php if ($staff['shift'] == '5:00-11:00') echo 'selected'; ?>>5:00-11:00</option>
                <option value="11:00-17:00" <?php if ($staff['shift'] == '11:00-17:00') echo 'selected'; ?>>11:00-17:00</option>
                <option value="17:00-23:00" <?php if ($staff['shift'] == '17:00-23:00') echo 'selected'; ?>>17:00-23:00</option>
            </select>
            <input type="submit" value="Update Staff">
        </form>
    </div>
</body>
</html>
