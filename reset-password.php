<?php
date_default_timezone_set('Asia/Manila');

session_start();
require_once 'database/database.php'; // Use require_once to avoid multiple inclusions

// Check if the token is set in the URL
if (!isset($_GET['token'])) {
    $_SESSION['error'] = "Invalid token.";
    header('Location: ../../login.php');
    exit();
}

$token = $_GET['token'];

// Verify the token
$user = verifyToken($token, $conn);  // Pass the MySQLi connection to the function
if (!$user) {
    $_SESSION['error'] = "Invalid or expired token.";
    header('Location: ../../login.php');
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate passwords
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
    } else {
        // Update the password in the database
        updatePassword($user['user_id'], password_hash($newPassword, PASSWORD_DEFAULT), $conn);  // Pass $conn

        // Delete the reset token after successful password reset
        deleteToken($token, $conn);

        // Redirect to login page with success message
        $_SESSION['success'] = "Password updated successfully. You can now log in.";
        header('Location: ../../login.php');
        exit();
    }
}

// Function to verify the token
function verifyToken($token, $conn) {  // Accept $conn as a parameter
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expire_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc(); // Fetch the user data (returns the row containing user_id)
}

// Function to update the password
function updatePassword($userId, $newPassword, $conn) {  // Accept $conn as a parameter
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $userId);
    return $stmt->execute();
}

// Function to delete the used reset token
function deleteToken($token, $conn) {
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    return $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="asset/css/login.css">
</head>
<body>

<nav class="navbar">
    <!-- LOGO -->
    <div class="logo">
      <a href=""><img src="asset/images/logo.webp" alt=""></a>
    </div>
    
  </nav>

<div class="container">

    <div class="container">
    <div class="box">
      <div class="logoform">
        <img src="asset/images/logo.webp" alt="">
      </div>
    </div>
    <div class="box">
      <h2>Change password</h2>

      <form action="" method="POST">
        <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
        <div class="forgot-pass">
          <a href="login.php" style="text-decoration: none">Already Change Password?</a><br>
        </div>
        <input type="submit" name="submit" value="Update Password">
      </form>

    </div>
  </div>

</div>

<!---- Sweet Alert ---->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      // Check for success message
      <?php if (isset($_SESSION['success'])): ?>
          Swal.fire({
              icon: 'info',
              title: 'Important Message',
              text: '<?php echo $_SESSION['success']; ?>',
              confirmButtonText: 'OK'
          });
          <?php unset($_SESSION['success']); // Clear the session variable ?>
      <?php endif; ?>

      // Check for error message
      <?php if (isset($_SESSION['error'])): ?>
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: '<?php echo $_SESSION['error']; ?>',
              confirmButtonText: 'Try Again'
          });
          <?php unset($_SESSION['error']); // Clear the session variable ?>
      <?php endif; ?>
  });
</script>
</body>
</html>

