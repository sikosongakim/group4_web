<?php 
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page if not logged in
    header('Location: adminlogin.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: adminlogin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="admin/adminstyle1.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo_content">
            <div class="logo">
                <div class="logo_name">ETS</div>
            </div>
            <i class='bx bx-menu' id="btn"></i>
        </div>
        <ul class="nav_list">
            <li>
                <a href="#" data-tooltip="Search">
                    <i class='bx bx-search'></i>
                    <input type="text" placeholder="Search...">
                </a>
            </li>
            <li>
                <a href="adminpage1.php" data-tooltip="Dashboard">
                    <i class='bx bx-grid-alt'></i>
                    <span class="adminpage1">Dashboard</span>
                </a>  
            </li>
            <li>
                <a href="#manage-staff" data-tooltip="Manage Staff">
                    <i class='bx bx-user'></i>
                    <span class="links_name">Manage Staff</span>
                </a>
            </li>
            <li>
                <a href="adminpage3.php" data-tooltip="Manage Schedules">
                    <i class='bx bx-calendar'></i>
                    <span class="schedule">Manage Schedules</span>
                </a>
            </li>
            <li>
                <a href="adminpage4.php" data-tooltip="Schedule Requests">
                    <i class='bx bx-edit-alt'></i>
                    <span class="links_name">Request Change Schedule</span>
                </a>
            </li>
            <li>
                <a href="adminpage5.php" data-tooltip="Request Leave from Staff">
                    <i class='bx bx-calendar-check'></i>
                    <span class="links_name">Request Leave from Staff</span>
                </a>
            </li>
            <li>
                <a href="#reports" data-tooltip="Reports">
                    <i class='bx bx-bar-chart'></i>
                    <span class="links_name">Reports</span>
                </a>
            </li>
        </ul>
        <a href="adminpage1.php?logout=true">
            <i class='bx bx-log-out' id="log_out"></i>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main_content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
        <!-- Stats Section -->
        <div class="stats">
            <div class="stat_card">
                <h2>45</h2>
                <p>Total Staff</p>
            </div>
            <div class="stat_card">
                <h2>120</h2>
                <p>Schedules Created</p>
            </div>
            <div class="stat_card">
                <h2>30</h2>
                <p>Active Sessions</p>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.querySelector(".sidebar");
        const toggleBtn = document.querySelector("#btn");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("closed");
        });
    </script>
</body>
</html>
