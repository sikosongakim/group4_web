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
                    <a href="adminpage2.php" data-tooltip="Manage Staff"> <!-- Update href here -->
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
    </body>    
</html>
