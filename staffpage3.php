<?php
// Include the database connection file
include('db_connection.php');

// Start session to access staff_id
session_start();

// Check if the staff is logged in
if (!isset($_SESSION['staff_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id']; // Get staff_id from session (set during login)

// Fetch schedule from the database
$query = "SELECT * FROM schedules WHERE staff_id = ? ORDER BY work_date DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$staff_id]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>
    <link rel="stylesheet" href="staff/staffstyle1.css">
</head>
<body>
    <!-- Yellow Header -->
    <header class="header yellow-header">
        <div class="logo">
            <img src="ktm.png" alt="Logo">
        </div>
        <nav class="navbar">
            <a href="#View Schedule" class="active">View Schedule</a>
            <a href="staffpage4.php">Change Schedule</a>
            <a href="staffpage5.php">Request Leave</a>
        </nav>
    </header>

    <!-- Main Content: View Schedule -->
    <div class="main-content">
        <h1>Your Schedule</h1>

        <?php if (count($schedules) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($schedule['work_date']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['shift']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No schedules found.</p>
        <?php endif; ?>
    </div>

    <!-- Blue Footer -->
    <footer class="blue-footer">
        <div class="footer-content">
            <p>&copy; 2024 ETS Staff Schedule. All rights reserved.</p>
            <nav class="footer-links">
                <a href="#Add navigation">Navigation</a>
                <a href="#Add navigation">Navigation</a>
                <a href="#Add navigation">Navigation</a>
            </nav>
        </div>
    </footer>
</body>
</html>
