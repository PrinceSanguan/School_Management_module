<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is a teacher or the verified user
if ($_SESSION['userRole'] !== 'teacher' && $_SESSION['userId'] != $subject['userId']) {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

$userId = $_SESSION['userId']; // Get the user ID from session

// Query to get subjects associated with the teacher
$query = "SELECT subject.subject 
          FROM teacherSubject
          JOIN subject ON teacherSubject.subject_id = subject.id
          WHERE teacherSubject.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$subjects = []; // Initialize an array to store the fetched data
if ($result->num_rows > 0) {
    // Fetching results
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject']; // Storing only the subject name
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Task</title>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php">Announcement</a>
        <a href="../teacher/assign_subject.php">Assigned Subject</a>
        <a href="../teacher/task.php" style="color: wheat;">Task</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
    </div>

    <div class="container">
    
    <button class="button" id="openModal" style="margin-bottom: 10px;">Add Task</button>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Task Title</th>
                <th>Content</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
              <td>Math</td>
              <td>Science Quiz</td>
              <td>
                <button>View</button>
              </td>
              <td>September 3, 2024</td>
              <td>Active</td>
              <td>
                <button>Edit</button>
              </td>
        </tbody>
    </table>
</div>

<!---------------ADD TASK---------------------------->
<div class="modal" id="accountModal">
      <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Add Task</h2>

        <form id="addAccountForm" method="post" action="">

          <!-- This is for Subject Module -->
          <label>Subject:</label>
          <select name="subject" required>
              <option value="" disabled selected>Select Subject</option>
              <?php if (!empty($subjects)): ?>
                  <?php foreach ($subjects as $subject): ?>
                      <option value="<?php echo htmlspecialchars($subject); ?>">
                          <?php echo htmlspecialchars($subject); ?>
                      </option>
                  <?php endforeach; ?>
              <?php else: ?>
                  <option value="" disabled>No subjects assigned</option>
              <?php endif; ?>
          </select>

            <!-- Select type of Content -->
            <select name="" required>
              <option value="" disabled selected>Select Type</option>
              <option value="student">Image</option>
              <option value="teacher">Text</option>
            </select>

          <!-- Show this image if this is select hide if not -->
          <label for="pdfFiles">Upload Task:</label>
          <input type="file" name="pdfFiles[]" id="pdfFiles" accept=".pdf" multiple>

          <!-- Show this textarea if this is select hide if not -->
          <label>Write your task</label>
          <textarea name="" rows="5" cols="65" placeholder="Place your task here"></textarea>

          <input type="date" name="" id="">

          <button type="submit">Add Task</button>
        </form>
      </div>
    </div>
     <!---------------ADD TASK---------------------------->

     <!---------------ADD TASK MODAL---------------------------->
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
   <!---------------ADD TASK MODAL---------------------------->
  
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