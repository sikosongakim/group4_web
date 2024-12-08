<?php
session_start();  // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // If not logged in, redirect to login page
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Get the staff ID from session
$staff_id = $_SESSION['staff_id'];

// Query to get staff details from the database
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Handle profile update (if form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $position = $_POST['position'];
    $shift = $_POST['shift'];

    // Update staff details in database
    $update_stmt = $conn->prepare("UPDATE staff SET first_name = ?, last_name = ?, email = ?, gender = ?, position = ?, shift = ? WHERE staff_id = ?");
    $update_stmt->bind_param("ssssssi", $first_name, $last_name, $email, $gender, $position, $shift, $staff_id);
    $update_stmt->execute();

    // Redirect after update
    header('Location: staffpage1.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    <form method="POST" action="staffpage2.php">
        <label for="first_name">First Name: </label>
        <input type="text" name="first_name" value="<?php echo $staff['first_name']; ?>" required>
        <label for="last_name">Last Name: </label>
        <input type="text" name="last_name" value="<?php echo $staff['last_name']; ?>" required>
        <label for="email">Email: </label>
        <input type="email" name="email" value="<?php echo $staff['email']; ?>" required>
        <label for="gender">Gender: </label>
        <select name="gender">
            <option value="Male" <?php if ($staff['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($staff['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if ($staff['gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select>
        <label for="position">Position: </label>
        <input type="text" name="position" value="<?php echo $staff['position']; ?>" required>
        <label for="shift">Shift: </label>
        <input type="text" name="shift" value="<?php echo $staff['shift']; ?>" required>
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
