<?php
include "../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
  $_SESSION['error'] = "You do not have permission to access this page!.";
  header("Location: ../index.php");
  exit();
}

// Fetch students who are not already in a section
$studentsQuery = "
    SELECT u.id, u.firstName, u.lastName
    FROM users u
    LEFT JOIN studentSection ss ON u.id = ss.user_id
    WHERE u.userRole = 'student' AND ss.user_id IS NULL
";
$result = $conn->query($studentsQuery);

// Check if there are any results
$students = $result->fetch_all(MYSQLI_ASSOC);

// Fetch sections
$sectionsQuery = "SELECT id, section FROM section";
$sections = $conn->query($sectionsQuery);

// Fetch students and their assigned sections
$query = "
    SELECT ss.id, u.firstName, u.lastName, s.section
    FROM studentSection ss
    JOIN users u ON ss.user_id = u.id
    JOIN section s ON ss.section_id = s.id
";
$studentSections = $conn->query($query);


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../asset/css/account-approval.css">
  <!-- Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Student Registration</title>
</head>
<body>

  <div class="navbar">
    <a href="../admin/account-approval.php">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../admin/registration.php">Registration</a>
    <a href="../admin/student-registration.php">Student Registration</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>

  <div class="container">
    <button class="button" id="openModal" style="margin-bottom: 10px;">Register a Student</button>

  <table>
    <thead>
      <tr>
        <th>Student Name</th>
        <th>Designation Section</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
    <?php
     if (!empty($studentSections)): ?>
                <?php foreach ($studentSections as $studentSection): ?>
                    <tr>
                        <td><?= htmlspecialchars($studentSection['firstName'] . ' ' . $studentSection['lastName']) ?></td>
                        <td><?= htmlspecialchars($studentSection['section']) ?></td>
                        <td>
                            <form method="post" action="../controller/AdminController/delete_section_student.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?= $studentSection['id'] ?>">
                                <button type="submit" style='background-color: #f44336; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No students assigned to sections yet.</td>
                </tr>
            <?php endif; 
      ?>
    </tbody>
  </table>

  </div>

    <!---------------REGISTRATION OF STUDENT---------------------------->
    <div class="modal" id="accountModal">
    <div class="modal-content">
      <button class="modal-close" id="closeModal">&times;</button>
      <h2>Register a student</h2>

      <form action="../controller/AdminController/add_section_student.php" method="post">
        <!-- Student Dropdown -->
        <label for="student">Select a Student:</label>
        <select name="student" id="student" required>
            <?php if (empty($students)): ?>
                <option value="" disabled selected>All students are already assigned to sections</option>
            <?php else: ?>
                <option value="" disabled selected>Select a Student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= $student['firstName'] . ' ' . $student['lastName'] ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <br><br>

        <!-- Section Dropdown -->
        <label for="section">Select a Section:</label>
        <select name="section" id="section" required>
            <option value="" disabled selected>Select a Section</option>
            <?php foreach ($sections as $section): ?>
                <option value="<?= $section['id'] ?>"><?= $section['section'] ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <!-- Submit Button -->
        <button type="submit">Assign Student to Section</button>
    </form>

    </div>
  </div>
  <!---------------REGISTRATION OF STUDENT---------------------------->

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


