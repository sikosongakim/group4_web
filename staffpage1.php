<?php
session_start();

// Check if the staff is logged in (staff_id is stored in session)
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');  // Redirect to login page if not logged in
    exit();
}

include('config.php');

// Fetch schedule for the logged-in staff member
$staff_id = $_SESSION['staff_id'];
$sql = "SELECT work_date, shift, status FROM schedules WHERE staff_id = $staff_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETS Staff Schedule</title>
    <link rel="stylesheet" href="staff/staffstyle1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
</head>
<body>
    <!-- Yellow Header -->
    <header class="header yellow-header">
        <div class="logo">
            <img src="ktm.png" alt="Logo">
        </div>
        <nav class="navbar">
            <a href="#View Schedule">View Schedule</a>
            <a href="#Change Schedule">Change Schedule</a>
            <a href="#profile" class="profile-icon">
                <i class="fas fa-user"></i>
            </a>
        </nav>
    </header>

    <!-- Red Header -->
    <header class="header red-header"></header>

    <!-- Train Background -->
    <div class="train-background">
        <img src="train.jpg" alt="Train">
    </div>

    <!-- Staff Schedule View -->
    <section id="View Schedule">
        <h2>Your Schedule</h2>
        <table>
            <tr>
                <th>Work Date</th>
                <th>Shift</th>
                <th>Status</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["work_date"] . "</td>
                            <td>" . $row["shift"] . "</td>
                            <td>" . $row["status"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No schedule available</td></tr>";
            }
            ?>
        </table>
    </section>

    <!-- Blue Footer -->
    <footer class="blue-footer">
        <div class="footer-content">
            <p>&copy; 2024 ETS Staff Schedule. All rights reserved.</p>
            <nav class="footer-links">
                <a href="staffpage2.php">Edit Profile</a>
                <a href="staffpage3.php">View Schedule</a>
                <a href="staffpage4.php">Change Schedule</a>
                <a href="staffpage5.php">Request Leave</a>
            </nav>
        </div>
    </footer>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
