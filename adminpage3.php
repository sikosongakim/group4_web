<?php 
session_start(); // Start the session

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Handle approval or rejection of schedule change requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'Approve' or 'Reject'

    // Update the schedule_requests table based on the action
    if ($action == 'Approve') {
        $stmt = $conn->prepare("UPDATE schedule_requests SET status = 'Approved' WHERE request_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE schedule_requests SET status = 'Rejected' WHERE request_id = ?");
    }
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
}

// Handle approval or rejection of leave requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_request_id'])) {
    $leave_request_id = $_POST['leave_request_id']; // The ID of the leave request
    $action = $_POST['action']; // 'Approve' or 'Reject'

    // Update the leave_requests table based on the action
    if ($action == 'Approve') {
        $stmt = $conn->prepare("UPDATE leave_requests SET status = 'Approved' WHERE leave_request_id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE leave_requests SET status = 'Rejected' WHERE leave_request_id = ?");
    }
    $stmt->bind_param("i", $leave_request_id);
    $stmt->execute();
}

// Fetch all pending schedule change requests
$scheduleRequestsStmt = $conn->prepare("SELECT sr.request_id, sr.current_date, sr.requested_date, sr.shift, sr.reason, s.first_name, s.last_name 
                        FROM schedule_requests sr
                        JOIN staff s ON sr.staff_id = s.staff_id
                        WHERE sr.status = 'Pending'");
$scheduleRequestsStmt->execute();
$scheduleRequestsResult = $scheduleRequestsStmt->get_result();

// Fetch all pending leave requests
$leaveRequestsStmt = $conn->prepare("SELECT lr.leave_request_id, lr.leave_date, lr.reason, s.first_name, s.last_name 
                        FROM leave_requests lr
                        JOIN staff s ON lr.staff_id = s.staff_id
                        WHERE lr.status = 'Pending'");
$leaveRequestsStmt->execute();
$leaveRequestsResult = $leaveRequestsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="admin/adminstyle3.css" rel="stylesheet">
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

    <div class="container mt-5">
        <h1 class="text-center mb-4">Manage Schedules</h1>

        <!-- Tabs for Navigation -->
        <ul class="nav nav-tabs" id="scheduleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="view-schedules-tab" data-bs-toggle="tab" data-bs-target="#view-schedules" type="button" role="tab">
                    View Schedules
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="add-schedule-tab" data-bs-toggle="tab" data-bs-target="#add-schedule" type="button" role="tab">
                    Add Schedule
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="change-requests-tab" data-bs-toggle="tab" data-bs-target="#change-requests" type="button" role="tab">
                    Change Requests
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="leave-requests-tab" data-bs-toggle="tab" data-bs-target="#leave-requests" type="button" role="tab">
                    Leave Requests
                </button>
            </li>
        </ul>

        <div class="tab-content mt-4">
            <!-- View Schedules Tab -->
            <div class="tab-pane fade show active" id="view-schedules" role="tabpanel">
                <h2>All Schedules</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Schedule ID</th>
                            <th>Staff Name</th>
                            <th>Work Date</th>
                            <th>Shift</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Example data, replace with backend logic to fetch actual schedules -->
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>2024-12-15</td>
                            <td>5:00-11:00</td>
                            <td>Scheduled</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add Schedule Tab -->
            <div class="tab-pane fade" id="add-schedule" role="tabpanel">
                <h2>Add a New Schedule</h2>
                <form action="add_schedule.php" method="POST">
                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff ID</label>
                        <input type="number" class="form-control" id="staff_id" name="staff_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="work_date" class="form-label">Work Date</label>
                        <input type="date" class="form-control" id="work_date" name="work_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="shift" class="form-label">Shift</label>
                        <select class="form-select" id="shift" name="shift" required>
                            <option value="5:00-11:00">5:00-11:00</option>
                            <option value="11:00-17:00">11:00-17:00</option>
                            <option value="17:00-23:00">17:00-23:00</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Scheduled">Scheduled</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">Admin ID</label>
                        <input type="number" class="form-control" id="admin_id" name="admin_id" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </form>
            </div>

            <!-- Change Requests Tab -->
            <div class="tab-pane fade" id="change-requests" role="tabpanel">
                <h2>Schedule Change Requests</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Staff Name</th>
                            <th>Current Shift</th>
                            <th>New Shift</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $scheduleRequestsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['request_id']; ?></td>
                                <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td><?php echo $row['current_date']; ?></td>
                                <td><?php echo $row['requested_date']; ?></td>
                                <td><?php echo $row['shift']; ?></td>
                                <td><?php echo $row['reason']; ?></td>
                                <td>
                                    <form method="POST" action="adminpage3.php" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                        <button type="submit" name="action" value="Approve">Approve</button>
                                        <button type="submit" name="action" value="Reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Leave Requests Tab -->
            <div class="tab-pane fade" id="leave-requests" role="tabpanel">
                <h2>Leave Requests</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Staff Name</th>
                            <th>Leave Date</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $leaveRequestsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['leave_request_id']; ?></td>
                                <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td><?php echo $row['leave_date']; ?></td>
                                <td><?php echo $row['reason']; ?></td>
                                <td>
                                    <form method="POST" action="adminpage3.php" style="display:inline;">
                                        <input type="hidden" name="leave_request_id" value="<?php echo $row['leave_request_id']; ?>">
                                        <button type="submit" name="action" value="Approve">Approve</button>
                                        <button type="submit" name="action" value="Reject">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.querySelector(".sidebar");
        const toggleBtn = document.querySelector("#btn");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("closed");
        });
    </script>
</body>
</html>
