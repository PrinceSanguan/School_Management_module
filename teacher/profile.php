<?php
include "../database/database.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is a teacher
if ($_SESSION['userRole'] !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

// Fetch the teacher ID from the session
$userId = $_SESSION['userId'];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT firstName, lastName, email, userRole FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle password change
$passwordChangeMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->bind_param("si", $hashedPassword, $userId);
        $updateStmt->execute();
        $updateStmt->close();
        $_SESSION['success'] = "Password updated successfully!";
    } else {
        $_SESSION['error'] = "Passwords do not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Student Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
        }
        .profile-info {
            background: #f4f4f4;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .password-change {
            background: #e8e8e8;
            padding: 20px;
            border-radius: 5px;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background: #333;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #555;
        }
        .message {
            color: #4CAF50;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
      <a href="../teacher/announcement.php">Announcement</a>
      <a href="../teacher/assign_subject.php" style="color: wheat;">Subject</a>
      <a href="../teacher/task.php">Task</a>
      <a href="../teacher/profile.php" style="color: wheat;">Profile</a>
      <a href="../controller/LogoutController/logOut.php">Logout</a>
    </div>

    <div class="container">
        <h1>Teahcer Profile</h1>
        
        <div class="profile-info">
            <h2>Personal Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['userRole']); ?></p>
        </div>

        <div class="password-change">
            <h2>Change Password</h2>
            <form method="POST">
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <input type="submit" value="Change Password">
            </form>
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