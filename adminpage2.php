<?php
session_start(); // Start the session

/* Check if admin is logged in */
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Fetch staff data
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM staff WHERE first_name LIKE '%$search_query%' OR last_name LIKE '%$search_query%' OR gender LIKE '%$search_query%' OR position LIKE '%$search_query%' OR shift LIKE '%$search_query%'";
$staff_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link rel="stylesheet" href="adminstyle2.css">
</head>

<body>
    <div class="sidebar">
        <div class="logo_content">
            <div class="logo">
                <div class="logo_name">ADMIN</div>
            </div>
            <i class='bx bx-menu' id="btn"></i>
        </div>
        <ul class="nav_list">
            <li>
                <a href="adminpage1.php" data-tooltip="Dashboard">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="adminpage2.php" data-tooltip="Manage Staff">
                    <i class='bx bx-user'></i>
                    <span class="links_name">Manage Staff</span>
                </a>
            </li>
            <li>
                <a href="adminpage3.php" data-tooltip="Manage Schedules">
                    <i class='bx bx-calendar'></i>
                    <span class="links_name">Manage Schedules</span>
                </a>
            </li>
            <li>
                <a href="adminpage4.php" data-tooltip="Schedule Requests">
                    <i class='bx bx-edit-alt'></i>
                    <span class="links_name">Schedule Requests</span>
                </a>
            </li>
            <li>
                <a href="adminpage5.php" data-tooltip="Leave Requests">
                    <i class='bx bx-calendar-check'></i>
                    <span class="links_name">Leave Requests</span>
                </a>
            </li>
        </ul>
        <a href="adminlogout.php" data-tooltip="Logout">
            <i class='bx bx-log-out' id="log_out"></i>
        </a>
    </div>

    <script>
        const sidebar = document.querySelector(".sidebar");
        const toggleBtn = document.querySelector("#btn");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("closed"); // Toggle the 'closed' class
        });
    </script>

    <div class="container">
        <h1>Staff Management</h1>

        <!-- Search Bar -->
        <form method="GET">
            <input type="text" name="search" placeholder="Searching..." value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Add Staff Button -->
        <a href="adminpage2add.php"><button>Add Staff</button></a>

        <!-- Staff List -->
        <h2>Staff List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Position</th>
                    <th>Shift</th>
                    <th>Off Day</th> <!-- Add Off Day column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $staff_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['staff_id'] ?></td>
                        <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['gender'] ?></td>
                        <td><?= $row['position'] ?></td>
                        <td><?= $row['shift'] ?></td>
                        <td><?= $row['off_day'] ?></td> <!-- Display Off Day -->
                        <td>
                            <a href="adminpage2edit.php?staff_id=<?= $row['staff_id'] ?>">Edit</a> |
                            <a href="adminpage2delete.php?staff_id=<?= $row['staff_id'] ?>"
                                onclick="return confirm('Are you sure you want to delete the staff from the list?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
