<?php
session_start(); // Start the session

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // Redirect to login if not logged in
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Get the logged-in staff ID
$staff_id = $_SESSION['staff_id'];

// Get today's schedule for the logged-in staff
$stmt = $conn->prepare("SELECT * FROM schedules WHERE staff_id = ? ORDER BY work_date DESC");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Schedule</title>
</head>
<body>
    <h2>Your Schedule</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Work Date</th>
                <th>Shift</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['work_date']; ?></td>
                    <td><?php echo $row['shift']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No schedule found for today.</p>
    <?php endif; ?>

</body>
</html>
