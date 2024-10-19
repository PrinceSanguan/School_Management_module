<?php
include "../../database/database.php";
session_start();

// Ensure user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    header("Location: ../../login.php");
    exit();
}

$userId = $_SESSION['userId'];

// Handle password change form submission
if (isset($_POST['submit'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if the passwords match
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: change_password.php");
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the user's password in the database and set changePassword to 'yes'
    $stmt = $conn->prepare("UPDATE users SET password = ?, changePassword = 'yes' WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $userId);

    if ($stmt->execute()) {
        // Password updated successfully, now redirect based on user role
        $_SESSION['changePassword'] = 'yes';
        $_SESSION['success'] = "You successfully changed your password!";

        if ($_SESSION['userRole'] == 'admin') {
            header("Location: ../../admin/account-approval.php");
        } elseif ($_SESSION['userRole'] == 'student') {
            header("Location: ../../student/announcement.php");
        } elseif ($_SESSION['userRole'] == 'teacher') {
            header("Location: ../../teacher/announcement.php");
        }
    } else {
        $_SESSION['error'] = "Error updating password. Please try again.";
        header("Location: change_password.php");
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../../asset/css/login.css">
</head>
<body>

<nav class="navbar">
    <!-- LOGO -->
    <div class="logo">
      <a href=""><img src="../../asset/images/logo.webp" alt=""></a>
    </div>
    
  </nav>

<div class="container">

    <div class="container">
    <div class="box">
      <div class="logoform">
        <img src="../../asset/images/logo.webp" alt="">
      </div>
    </div>
    <div class="box">
      <h2>Change password</h2>

      <form action="" method="POST">
        <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
        <div class="forgot-pass">
          <a href="../../login.php" style="text-decoration: none">Already Change Password?</a><br>
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
              icon: 'success',
              title: 'Success!',
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
