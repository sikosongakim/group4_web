<?php
// Database Connection
$host = 'localhost';
$db = 'your_database_name';
$user = 'your_database_user';
$password = 'your_database_password';

$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['leave_request_id'])) {
        $leave_request_id = intval($_POST['leave_request_id']);
        $action = $_POST['action']; // 'Approve' or 'Reject'

        // Update the leave request status
        $stmt = $conn->prepare("UPDATE leave_requests SET leave_status = ? WHERE leave_request_id = ?");
        $stmt->bind_param("si", $action, $leave_request_id);

        if ($stmt->execute()) {
            echo "<script>alert('Leave request successfully updated.');</script>";
        } else {
            echo "<script>alert('Error updating leave request: " . $stmt->error . "');</script>";
        }
    }
}

// Fetch Leave Requests
$leaveRequests = $conn->query("SELECT 
    lr.leave_request_id,
    lr.start_date,
    lr.end_date,
    lr.reason,
    lr.leave_status,
    st.first_name,
    st.last_name
FROM leave_requests lr
JOIN staff st ON lr.staff_id = st.staff_id
ORDER BY lr.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Leave Management</h1>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Staff Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $leaveRequests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['leave_request_id']; ?></td>
                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['end_date']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td><?php echo $row['leave_status']; ?></td>
                        <td>
                            <?php if ($row['leave_status'] === 'Pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="leave_request_id" value="<?php echo $row['leave_request_id']; ?>">
                                    <button type="submit" name="action" value="Approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="Reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No Action Needed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
