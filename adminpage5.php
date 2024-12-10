<?php
session_start();  // Start the session

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

// Fetch all pending leave requests
$stmt = $conn->prepare("SELECT lr.leave_request_id, lr.leave_date, lr.reason, s.first_name, s.last_name 
                        FROM leave_requests lr
                        JOIN staff s ON lr.staff_id = s.staff_id
                        WHERE lr.status = 'Pending'");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leave Requests</title>
</head>
<body>
    <h1>Manage Leave Requests</h1>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
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
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['leave_request_id']; ?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['leave_date']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td>
                            <form method="POST" action="adminpage5.php" style="display:inline;">
                                <input type="hidden" name="leave_request_id" value="<?php echo $row['leave_request_id']; ?>">
                                <button type="submit" name="action" value="Approve">Approve</button>
                                <button type="submit" name="action" value="Reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending leave requests.</p>
    <?php endif; ?>
</body>
</html>
