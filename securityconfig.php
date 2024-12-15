<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: stafflogin.php');
    exit();
}

// Include database configuration
include 'config.php';
require 'vendor/autoload.php'; // Autoload Composer dependencies

use OTPHP\TOTP;

// Fetch the logged-in staff's details
$staff_id = $_SESSION['staff_id'];
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

if (!$staff) {
    die("Invalid staff session.");
}

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'reset_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        if (!password_verify($current_password, $staff['password'])) {
            $message = '<p style="color:red;">Current password is incorrect.</p>';
        } elseif ($new_password !== $confirm_new_password) {
            $message = '<p style="color:red;">New passwords do not match.</p>';
        } else {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE staff SET password = ? WHERE staff_id = ?");
            $stmt->bind_param("si", $new_hash, $staff_id);
            $stmt->execute();
            $message = '<p style="color:green;">Password has been reset successfully.</p>';
        }
    }

    if ($_POST['action'] === 'enable_2fa') {
        // Enable 2FA
        $user_code = $_POST['2fa_code'];

        // Use the TOTP secret from the database
        $totp = TOTP::create($staff['totp_secret']);
        if ($totp->verify($user_code)) {
            $message = '<p style="color:green;">2FA has been enabled successfully.</p>';
        } else {
            $message = '<p style="color:red;">Invalid 2FA code.</p>';
        }
    }

    if ($_POST['action'] === 'disable_2fa') {
        // Disable 2FA
        $stmt = $conn->prepare("UPDATE staff SET totp_secret = NULL WHERE staff_id = ?");
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $message = '<p style="color:green;">2FA has been disabled successfully.</p>';
    }
}

// Generate a TOTP secret if one does not exist
if (empty($staff['totp_secret'])) {
    $totp = TOTP::create();
    $totp->setLabel($staff['email']); // Use the user's email
    $totp->setIssuer('ETS Staff Schedule'); // App name
    $totp_secret = $totp->getSecret(); // Generate the secret key

    // Save the secret to the database
    $stmt = $conn->prepare("UPDATE staff SET totp_secret = ? WHERE staff_id = ?");
    $stmt->bind_param("si", $totp_secret, $staff_id);
    $stmt->execute();

    // Update the staff record
    $staff['totp_secret'] = $totp_secret;
}

// Build the QR code URL
$totp = TOTP::create($staff['totp_secret']);
$totp->setLabel($staff['email']);
$totp->setIssuer('ETS Staff Schedule');
$qr_code_url = $totp->getProvisioningUri(); // otpauth:// URL
$qr_code_image = "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . urlencode($qr_code_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Configuration</title>
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <header class="header yellow-header">
        <div class="logo"><img src="ktm.png" alt="Logo"></div>
        <nav class="navbar">
            <a href="staffpage1.php">Home</a>
            <a href="staffpage2.php">Profile</a>
            <a href="staffpage3.php">Schedule</a>
            <a href="stafflogout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h1>Security Configuration</h1>
        <?php if ($message) echo $message; ?>

        <!-- Reset Password -->
        <section>
            <h2>Reset Password</h2>
            <form method="POST">
                <input type="hidden" name="action" value="reset_password">
                <label>Current Password:</label>
                <input type="password" name="current_password" required>
                <label>New Password:</label>
                <input type="password" name="new_password" required>
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_new_password" required>
                <button type="submit">Reset Password</button>
            </form>
        </section>

        <!-- Two-Factor Authentication -->
        <section>
            <h2>Two-Factor Authentication (2FA)</h2>
            <?php if ($staff['totp_secret']): ?>
                <p>2FA is currently enabled for your account.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="disable_2fa">
                    <button type="submit">Disable 2FA</button>
                </form>
            <?php else: ?>
                <p>Scan the QR code below using Google Authenticator:</p>
                <img src="<?php echo $qr_code_image; ?>" alt="2FA QR Code">
                <form method="POST">
                    <input type="hidden" name="action" value="enable_2fa">
                    <label>Enter Code:</label>
                    <input type="text" name="2fa_code" required>
                    <button type="submit">Enable 2FA</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
