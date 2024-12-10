<?php 
session_start(); // Start the session

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Include database configuration
include('config.php');

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

// Fetch all pending requests
$stmt = $conn->prepare("SELECT sr.request_id, sr.current_date, sr.requested_date, sr.shift, sr.reason, s.first_name, s.last_name 
                        FROM schedule_requests sr
                        JOIN staff s ON sr.staff_id = s.staff_id
                        WHERE sr.status = 'Pending'");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule Requests</title>
</head>
<body>
    <h1>Schedule Change Requests</h1>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Staff Name</th>
                    <th>Current Date</th>
                    <th>Requested Date</th>
                    <th>Requested Shift</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['request_id']; ?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['current_date']; ?></td>
                        <td><?php echo $row['requested_date']; ?></td>
                        <td><?php echo $row['shift']; ?></td>
                        <td><?php echo $row['reason']; ?></td>
                        <td>
                            <form method="POST" action="adminpage4.php" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                <button type="submit" name="action" value="Approve">Approve</button>
                                <button type="submit" name="action" value="Reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending requests.</p>
    <?php endif; ?>
</body>
</html>
