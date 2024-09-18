<?php
include "../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
  $_SESSION['error'] = "You do not have permission to access this page!.";
  header("Location: ../index.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../asset/css/account-approval.css">
  <title>Teacher Dashboard</title>
</head>
<body>
  <div class="navbar">
    <a href="../teacher/dashboard.php">Dashboard</a>
    <a href="../teacher/assign_subject.php">Assigned Subject</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../admin/registration.php">Registration</a>
    <a href="../admin/student-registration.php">Student Registration</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>
</body>
</html>