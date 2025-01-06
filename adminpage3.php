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
        // Fetch the specific schedule change request details
        $scheduleRequestStmt = $conn->prepare("SELECT * FROM schedule_requests WHERE request_id = ?");
        $scheduleRequestStmt->bind_param("i", $request_id);
        $scheduleRequestStmt->execute();
        $scheduleRequestResult = $scheduleRequestStmt->get_result();
        $request = $scheduleRequestResult->fetch_assoc();

        if ($request) {
            $staff_id = $request['staff_id'];
            $current_date = $request['current_date'];
            $new_date = $request['requested_date'];
            $new_shift = $request['shift'];

            // Update the schedule based on the approved request
            $updateScheduleStmt = $conn->prepare("UPDATE schedules SET work_date = ?, shift = ? WHERE staff_id = ? AND work_date = ?");
            $updateScheduleStmt->bind_param("ssis", $new_date, $new_shift, $staff_id, $current_date);
            $updateScheduleStmt->execute();

            // Mark the request as 'Approved'
            $updateRequestStmt = $conn->prepare("UPDATE schedule_requests SET status = 'Approved' WHERE request_id = ?");
            $updateRequestStmt->bind_param("i", $request_id);
            $updateRequestStmt->execute();
        }
    } else {
        // Mark the request as 'Rejected'
        $updateRequestStmt = $conn->prepare("UPDATE schedule_requests SET status = 'Rejected' WHERE request_id = ?");
        $updateRequestStmt->bind_param("i", $request_id);
        $updateRequestStmt->execute();
    }
}

// Handle approval or rejection of leave requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_request_id'])) {
    $leave_request_id = $_POST['leave_request_id'];
    $action = $_POST['action']; // 'Approve' or 'Reject'

    $stmt = $conn->prepare("UPDATE leave_requests SET leave_status = ? WHERE leave_request_id = ?");
    $stmt->bind_param("si", $action, $leave_request_id);
    $stmt->execute();
}

// Fetch schedules and leave requests
$selected_date = $_GET['filter_date'] ?? null;
$scheduleQuery = "
    SELECT 
    s.schedule_id AS id, 
    st.staff_id, 
    st.first_name, 
    st.last_name, 
    s.work_date AS work_date, 
    s.shift, 
    st.position, 
    st.off_day,
    'Scheduled' AS status
FROM schedules s
JOIN staff st ON s.staff_id = st.staff_id
WHERE (? IS NULL OR s.work_date = ?)
AND NOT EXISTS (
    SELECT 1 
    FROM leave_requests lr 
    WHERE lr.staff_id = s.staff_id 
    AND lr.leave_status = 'Approved' 
    AND lr.start_date <= s.work_date 
    AND lr.end_date >= s.work_date
)
UNION
SELECT 
    lr.leave_request_id AS id,
    st.staff_id, 
    st.first_name, 
    st.last_name, 
    lr.start_date AS work_date, 
    NULL AS shift, 
    st.position, 
    st.off_day,
    'On Leave' AS status
FROM leave_requests lr
JOIN staff st ON lr.staff_id = st.staff_id
WHERE lr.leave_status = 'Approved' 
AND (? IS NULL OR lr.start_date = ?)
ORDER BY work_date ASC;

";

$schedulesStmt = $conn->prepare($scheduleQuery);
$schedulesStmt->bind_param("ssss", $selected_date, $selected_date, $selected_date, $selected_date);
$schedulesStmt->execute();
$schedulesResult = $schedulesStmt->get_result();

// Fetch pending schedule change requests
$scheduleRequestsStmt = $conn->prepare("SELECT sr.request_id, sr.current_date, sr.requested_date, sr.shift, sr.reason, st.first_name, st.last_name 
                        FROM schedule_requests sr
                        JOIN staff st ON sr.staff_id = st.staff_id
                        WHERE sr.status = 'Pending'");
$scheduleRequestsStmt->execute();
$scheduleRequestsResult = $scheduleRequestsStmt->get_result();

// Fetch pending leave requests
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin/adminstyle3.css" rel="stylesheet">
</head>
<body>
    <!-- Train Background with Staff Information -->
    <div class="train-background">
    </div>
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
                        <i class='bx bx-grid-alt' style="height: 50px; min-width: 50px; border-radius: 12px; line-height: 50px; text-align: center; font-size: 25px; margin-left: -30px;"></i>
                        <span class="links_name">Dashboard</span>
                    </a>  
                </li> 
                <li>                
                    <a href="adminpage2.php" data-tooltip="Manage Staff"> <!-- Update href here -->
                        <i class='bx bx-user' style="height: 50px; min-width: 50px; border-radius: 12px; line-height: 50px; text-align: center; font-size: 25px; margin-left: -30px;"></i>
                        <span class="links_name">Manage Staff</span>
                    </a>
                </li>
                <li>
                    <a href="adminpage3.php" data-tooltip="Manage Schedules">
                        <i class='bx bx-calendar' style="height: 50px; min-width: 50px; border-radius: 12px; line-height: 50px; text-align: center; font-size: 25px; margin-left: -30px;"></i>
                        <span class="links_name">Manage Schedules</span>
                    </a>
                </li>
            </ul>
            <a href="adminlogout.php" data-tooltip="Logout">
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

        <form method="GET" action="adminpage3.php" class="d-flex align-items-center mb-3">
            <label for="filter_date" class="form-label me-2" style="font-weight: bold;">Filter by Date:</label>
            <input type="date" id="filter_date" name="filter_date" class="form-control me-2" style="width: 200px; height: 40px;" value="<?php echo htmlspecialchars($selected_date); ?>"><br>
            <button type="submit" class="btn btn-primary me-2" >Filter</button>
            <a href="adminpage3.php" class="btn btn-secondary" >Clear Filter</a>
        </form>

        <div class="tab-content mt-4">
            <!-- View Schedules Tab -->
            <div class="tab-pane fade show active" id="view-schedules" role="tabpanel">
                <h2>All Schedules</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Work Date</th>
                            <th>Shift</th>
                            <th>Position</th>
                            <th>Off Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $schedulesResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['staff_id']; ?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['work_date']; ?></td>
                        <td><?php echo $row['shift'] ?? 'N/A'; ?></td> <!-- Show N/A if shift is NULL -->
                        <td><?php echo $row['position']; ?></td>
                        <td><?php echo $row['off_day']; ?></td>
                        <td><?php echo $row['status']; ?></td> <!-- Dynamic Status -->
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Change Requests Tab -->
            <div class="tab-pane fade" id="change-requests" role="tabpanel">
                <h2>Change Requests</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Staff Name</th>
                            <th>Current Date</th>
                            <th>Request Date</th>
                            <th>New Shift</th>
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
                                        <button type="submit" name="action" value="Approve" class="btn btn-success btn-sm">Approve</button>
                                        <button type="submit" name="action" value="Reject" class="btn btn-danger btn-sm">Reject</button>
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
                                        <button type="submit" name="action" value="Approved" class="btn btn-success btn-sm">Approve</button>
                                        <button type="submit" name="action" value="Rejected" class="btn btn-danger btn-sm">Reject</button>
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
    <script>
        const sidebar = document.querySelector(".sidebar");
        const toggleBtn = document.querySelector("#btn");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("closed"); // Toggle the 'closed' class
        });
    </script>
</body>
</html>
