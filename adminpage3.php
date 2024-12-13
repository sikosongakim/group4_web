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

    if ($action == 'Approve') {
        // Fetch the request details to update the schedule
        $scheduleRequestStmt = $conn->prepare("SELECT sr.staff_id, sr.current_date, sr.shift 
                                              FROM schedule_requests sr 
                                              WHERE sr.request_id = ?");
        $scheduleRequestStmt->bind_param("i", $request_id);
        $scheduleRequestStmt->execute();
        $scheduleRequestResult = $scheduleRequestStmt->get_result();
        $request = $scheduleRequestResult->fetch_assoc();

        if ($request) {
            $staff_id = $request['staff_id'];
            $current_date = $request['current_date'];
            $new_shift = $request['shift'];

            // Update the schedule with the new shift for the same work date
            $updateScheduleStmt = $conn->prepare("UPDATE schedules SET shift = ? WHERE staff_id = ? AND work_date = ?");
            $updateScheduleStmt->bind_param("sis", $new_shift, $staff_id, $current_date);
            $updateScheduleStmt->execute();

            // Update the schedule request status to 'Approved'
            $updateRequestStmt = $conn->prepare("UPDATE schedule_requests SET status = 'Approved' WHERE request_id = ?");
            $updateRequestStmt->bind_param("i", $request_id);
            $updateRequestStmt->execute();
        }
    } else {
        // Reject the schedule change request (no schedule update)
        $updateRequestStmt = $conn->prepare("UPDATE schedule_requests SET status = 'Rejected' WHERE request_id = ?");
        $updateRequestStmt->bind_param("i", $request_id);
        $updateRequestStmt->execute();
    }
}

// Handle approval or rejection of leave requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_request_id'])) {
    $leave_request_id = $_POST['leave_request_id'];
    $action = $_POST['action']; // 'Approve' or 'Reject'

    // Update the leave_requests table based on the action
    $stmt = $conn->prepare("UPDATE leave_requests SET leave_status = ? WHERE leave_request_id = ?");
    $stmt->bind_param("si", $action, $leave_request_id);
    $stmt->execute();
}

// Fetch all schedules with leave request data
$schedulesStmt = $conn->prepare("
    SELECT 
        s.schedule_id, 
        st.staff_id, 
        st.first_name, 
        st.last_name, 
        s.work_date, 
        s.shift, 
        st.position, 
        st.off_day,
        lr.leave_status AS leave_status,
        lr.start_date AS leave_start_date,
        lr.end_date AS leave_end_date
    FROM schedules s
    JOIN staff st ON s.staff_id = st.staff_id
    LEFT JOIN leave_requests lr ON st.staff_id = lr.staff_id AND lr.leave_status = 'Approved'
    ORDER BY s.work_date ASC
");
$schedulesStmt->execute();
$schedulesResult = $schedulesStmt->get_result();

// Fetch all pending schedule change requests
$scheduleRequestsStmt = $conn->prepare("SELECT sr.request_id, sr.current_date, sr.requested_date, sr.shift, sr.reason, st.first_name, st.last_name 
                        FROM schedule_requests sr
                        JOIN staff st ON sr.staff_id = st.staff_id
                        WHERE sr.status = 'Pending'");
$scheduleRequestsStmt->execute();
$scheduleRequestsResult = $scheduleRequestsStmt->get_result();

// Fetch all pending leave requests (Updated to include start_date and end_date)
$leaveRequestsStmt = $conn->prepare("SELECT lr.leave_request_id, lr.start_date, lr.end_date, lr.reason, st.first_name, st.last_name 
                        FROM leave_requests lr
                        JOIN staff st ON lr.staff_id = st.staff_id
                        WHERE lr.leave_status = 'Pending'");
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
                <a href="adminpage1.php" data-tooltip="Dashboard">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Dashboard</span>
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
                    <span class="links_name">Manage Schedules</span>
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
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Shift</th>
                            <th>Position</th>
                            <th>Off Day</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $schedulesResult->fetch_assoc()): ?>
                            <?php
                                // Check if the current date is within an approved leave request period
                                $current_date = $row['work_date'];
                                if ($row['leave_status'] == 'Approved' && $current_date >= $row['leave_start_date'] && $current_date <= $row['leave_end_date']) {
                                    // If the staff member is on leave, do not display a "status" column
                                    continue; // You can still show a message for "On Leave" in another way if needed
                                }
                            ?>
                            <tr>
                                <td><?php echo $row['staff_id']; ?></td>
                                <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td><?php echo $row['shift']; ?></td>
                                <td><?php echo $row['position']; ?></td>
                                <td><?php echo $row['off_day']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
                            <th>Reason</th>
                            <th>Action</th>
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
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $leaveRequestsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['leave_request_id']; ?></td>
                                <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                                <td><?php echo $row['start_date']; ?></td>
                                <td><?php echo $row['end_date']; ?></td>
                                <td><?php echo $row['reason']; ?></td>
                                <td>
                                    <form method="POST" action="adminpage3.php" style="display:inline;">
                                        <input type="hidden" name="leave_request_id" value="<?php echo $row['leave_request_id']; ?>">
                                        <button type="submit" name="action" value="Approved">Approve</button>
                                        <button type="submit" name="action" value="Rejected">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
