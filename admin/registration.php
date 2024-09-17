<?php
include "../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
  $_SESSION['error'] = "You do not have permission to access this page!.";
  header("Location: ../index.php");
  exit();
}

// Fetch teachers
$teachersQuery = "SELECT id, firstName, lastName FROM users WHERE userRole = 'teacher'";
$teachersResult = $conn->query($teachersQuery);

// Fetch subjects
$subjectsQuery = "SELECT id, subject FROM subject";
$subjectsResult = $conn->query($subjectsQuery);

// Fetch teachers and their assigned subjects along with subject_id
$query = "
    SELECT u.id as teacher_id, u.firstName, u.lastName, s.id as subject_id, s.subject
    FROM teacherSubject ts
    INNER JOIN users u ON ts.user_id = u.id
    INNER JOIN subject s ON ts.subject_id = s.id
    WHERE u.userRole = 'teacher'
";
$result = $conn->query($query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../asset/css/account-approval.css">
  <!-- Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Accounts</title>
</head>
<body>

  <div class="navbar">
    <a href="../admin/account-approval.php">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../admin/registration.php">Registration</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>

  <div class="container">
    <button class="button" id="openModal" style="margin-bottom: 10px;">Add Teacher Subject</button>

    <table>
    <thead>
      <tr>
        <th>Teacher</th>
        <th>Subject Handle</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "
              <tr>
                  <td>" . $row['firstName'] . " " . $row['lastName'] . "</td>
                  <td>" . $row['subject'] . "</td>
                  <td>
                      <form action='../controller/AdminController/delete-subject-teacher.php' method='post'>
                          <input type='hidden' name='teacher_id' value='" . $row['teacher_id'] . "'>
                          <input type='hidden' name='subject_id' value='" . $row['subject_id'] . "'> <!-- Include subject_id -->
                          <button type='submit' class='delete-button' style='background-color: #f44336; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>Delete</button>
                      </form>
                  </td>
              </tr>
              ";
          }
      } else {
          echo "<tr><td colspan='3'>No teacher-subject assignments found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
    
  </div>

  <!---------------ADD MODAL---------------------------->
  <div class="modal" id="accountModal">
    <div class="modal-content">
      <button class="modal-close" id="closeModal">&times;</button>
      <h2>Add Teacher's Subject</h2>

      <form action="../controller/AdminController/add-teacher-subject.php" method="post">
        <!-- Teacher Dropdown -->
        <select name="teacher" id="teacher">
          <option value="" disabled selected>Select Teacher</option>
          <?php
            if ($teachersResult->num_rows > 0) {
              while ($row = $teachersResult->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['firstName'] . " " . $row['lastName'] . "</option>";
              }
            } else {
              echo "<option value='' disabled>No teachers available</option>";
            }
          ?>
        </select>

        <!-- Subject Dropdown -->
        <select name="subject" id="subject">
          <option value="" disabled selected>Select Subject</option>
          <?php
            if ($subjectsResult->num_rows > 0) {
              while ($row = $subjectsResult->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['subject'] . "</option>";
              }
            } else {
              echo "<option value='' disabled>No subjects available</option>";
            }
          ?>
        </select>

        <button type="submit">Add Subject</button>
      </form>
    </div>
  </div>
  <!---------------ADD MODAL---------------------------->
  
  <script>
    document.getElementById('openModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'flex';
    });

    document.getElementById('closeModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
      if (event.target === document.getElementById('accountModal')) {
        document.getElementById('accountModal').style.display = 'none';
      }
    });
  </script>

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


