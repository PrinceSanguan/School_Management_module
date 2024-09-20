<?php
include "../database/database.php";
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to access this page!";
    header("Location: ../index.php");
    exit();
}

// Ensure user_id is set in the session
if (!isset($_SESSION['userId'])) {
    die("User ID is not set. Please log in.");
}

$userId = $_SESSION['userId']; // Get the user ID from session

// Query to get subjects associated with the teacher and their pdf files
$query = "SELECT subject.subject, subject_images.id, subject_images.week, subject_images.image_url, subject_images.status 
          FROM teacherSubject
          JOIN subject ON teacherSubject.subject_id = subject.id
          JOIN subject_images ON subject.id = subject_images.subject_id
          WHERE teacherSubject.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$subjects = []; // Initialize an array to store the fetched data
if ($result->num_rows > 0) {
    // Fetching results
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
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
    <title>Teacher Assigned Subject</title>
</head>
<body>
  <div class="navbar">
    <a href="../teacher/announcement.php">Announcement</a>
    <a href="../teacher/assign_subject.php" style="color: wheat;">Assigned Subject</a>
    <a href="../teacher/task.php">Task</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>


    <div class="container">
    
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Week</th>
                    <th>PDF File</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subjects)): ?>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?= htmlspecialchars($subject['subject']) ?></td>
                            <td><?= htmlspecialchars($subject['week']) ?></td>
                            <td><a href="<?= htmlspecialchars($subject['image_url']) ?>" target="_blank">View PDF</a></td>
                            <td><?= htmlspecialchars($subject['status']) ?></td>
                            <td>
                                <!-- Form for Publish/Unpublish -->
                                <form method="POST" action="../controller/TeacherController/update_status.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($subject['id']) ?>">
                                    <?php if ($subject['status'] == 'publish'): ?>
                                        <input type="hidden" name="action" value="unpublish">
                                        <button type="submit" style="background-color:#dc3545; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Unpublish</button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="publish">
                                        <button type="submit" style="background-color:#28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Publish</button>
                                    <?php endif; ?>
                                </form>
                                <br>
                                <!-- Pass subject_id and subject name to the modal using data attributes -->
                                <button 
                                    style="background-color:#28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;" 
                                    
                                    class="openModalBtn"
                                    data-subject-id="<?= htmlspecialchars($subject['id']) ?>"
                                    data-subject-name="<?= htmlspecialchars($subject['subject']) ?>"
                                >
                                    Add Module
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No subjects found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!---------------ADD MODULE---------------------------->
    <div class="modal" id="accountModal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Add Module</h2>

        <form method="post" action="../controller/TeacherController/add_module.php" enctype="multipart/form-data">
        <!-- This will be populated with the subject name dynamically -->
        <label>Subject</label>
        <input type="text" id="modalSubjectName" value="subject_name" readonly>

        <!-- Hidden input to store the subject ID -->
        <input type="hidden" id="modalSubjectId" name="subject_id">

        <!-- Week selection -->
        <label>Week</label>
        <select name="week" required>
            <option value="" disabled selected>Select Week</option>
            <option value="week1">Week 1</option>
            <option value="week2">Week 2</option>
            <option value="week3">Week 3</option>
            <option value="week4">Week 4</option>
        </select>

        <label>Module</label>
        <input type="file" name="pdfFiles[]" id="pdfFiles" accept=".pdf" multiple required>

        <button type="submit">Add Module</button>
        </form>
    </div>
    </div>
    <!---------------ADD MODULE---------------------------->


    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
          // Check for success message
          <?php if (isset($_SESSION['success'])): ?>
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: '<?= $_SESSION['success']; ?>',
                  confirmButtonText: 'OK'
              });
              <?php unset($_SESSION['success']); // Clear the session variable ?>
          <?php endif; ?>

          // Check for error message
          <?php if (isset($_SESSION['error'])): ?>
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: '<?= $_SESSION['error']; ?>',
                  confirmButtonText: 'Try Again'
              });
              <?php unset($_SESSION['error']); // Clear the session variable ?>
          <?php endif; ?>
      });
    </script>

<script>
    // Get the modal element
    const modal = document.getElementById('accountModal');

    // Get the close button element
    const closeModal = document.getElementById('closeModal');

    // Get modal input fields for subject ID and name
    const modalSubjectId = document.getElementById("modalSubjectId");
    const modalSubjectName = document.getElementById("modalSubjectName");

    // Add event listener to all buttons with class 'openModalBtn'
    document.querySelectorAll('.openModalBtn').forEach(button => {
        button.addEventListener('click', function() {
            // Get data attributes from the clicked button
            const subjectId = this.getAttribute('data-subject-id');
            const subjectName = this.getAttribute('data-subject-name');

            // Set the values in the modal input fields
            modalSubjectId.value = subjectId;
            modalSubjectName.value = subjectName;

            // Show the modal
            modal.style.display = "flex";
        });
    });

    // Close modal when clicking the close button
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close modal when clicking outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>
</body>
</html>
