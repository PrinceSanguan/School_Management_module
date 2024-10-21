<?php
include "../database/database.php";
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to access this page!";
    header("Location: ../index.php");
    exit();
}

// Ensure subject_id is provided
if (!isset($_GET['subject_id'])) {
    die("Subject ID is not provided.");
}

$subjectId = $_GET['subject_id'];

// Query to get modules for the specific subject
$query = "SELECT subject.subject, subject_images.subject_id, subject_images.id, subject_images.week, subject_images.image_url, 
          subject_images.youtube_url, subject_images.status
          FROM subject
          JOIN subject_images ON subject.id = subject_images.subject_id
          WHERE subject.id = ?
          ORDER BY subject_images.week";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $subjectId);
$stmt->execute();
$result = $stmt->get_result();

$modules = [];
$subjectName = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
        if (empty($subjectName)) {
            $subjectName = $row['subject'];
        }
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
    <title>Modules for <?= htmlspecialchars($subjectName) ?></title>
    <style>
        .module {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .action-buttons {
            margin-top: 10px;
        }
        .action-buttons button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php">Announcement</a>
        <a href="../teacher/assign_subject.php">Subject</a>
        <a href="../teacher/task.php">Task</a>
        <a href="../teacher/profile.php">Profile</a>
        <a href="../teacher/monitoring.php">Monitoring</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
    </div>

    <div class="container">
        <h2>Modules for <?= htmlspecialchars($subjectName) ?></h2>
        
        <!-- Add Module button moved outside the loop -->
        <button 
            style="background-color:#28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px; margin-bottom: 20px;" 
            class="openModalBtn"
            data-subject-id="<?= htmlspecialchars($subjectId) ?>"
            data-subject-name="<?= htmlspecialchars($subjectName) ?>"
        >
            Add Module
        </button>

        <?php if (!empty($modules)): ?>
            <?php foreach ($modules as $module): ?>
                <div class="module">
                    <h3><?= htmlspecialchars($module['week']) ?></h3>
                    <p>Status: <?= htmlspecialchars($module['status']) ?></p>
                    <?php if (!empty($module['image_url'])): ?>
                        <p><a href="<?= htmlspecialchars($module['image_url']) ?>" target="_blank">View PDF</a></p>
                    <?php endif; ?>
                    <?php if (!empty($module['youtube_url'])): ?>
                        <div><?= $module['youtube_url'] ?></div>
                    <?php endif; ?>
                    <div class="action-buttons">
                        <form method="POST" action="../controller/TeacherController/update_status.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($module['id']) ?>">
                            <?php if ($module['status'] == 'publish'): ?>
                                <input type="hidden" name="action" value="unpublish">
                                <button type="submit" style="background-color:#dc3545; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Unpublish</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="publish">
                                <button type="submit" style="background-color:#28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Publish</button>
                            <?php endif; ?>
                        </form>
                        <form method="POST" action="../controller/TeacherController/delete_module.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($module['id']) ?>">
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($module['subject_id']) ?>">
                            <input type="hidden" name="image_url" value="<?= htmlspecialchars($module['image_url']) ?>">
                            <button type="submit" style="background-color:#dc3545; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No modules found for this subject.</p>
        <?php endif; ?>
    </div>

    <!---------------ADD MODULE---------------------------->
<div class="modal" id="accountModal">
  <div class="modal-content">
    <button class="modal-close" id="closeModal">&times;</button>
    <h2>Add Module</h2>

    <form method="post" action="../controller/TeacherController/add_module.php" enctype="multipart/form-data">
      <!-- This will be populated with the subject name dynamically -->
      <label>Subject</label>
      <input type="hidden" id="modalSubjectName" value="subject_name" readonly>

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

      <!-- Select type of Content -->
      <label>Content Type</label>
      <select name="postType" id="postType" required>
        <option value="" disabled selected>Select Type</option>
        <option value="Image">PDF Module</option>
        <option value="Text">Embedded Video</option>
      </select>

      <!-- Video embed option -->
      <div id="textInput" style="display: none;">
        <label>Embed Youtube Video</label>
        <input type="text" name="youtube_url" id="youtube_url">
      </div>

      <!-- PDF Upload option -->
      <div id="imageUpload" style="display: none;">
        <label>Upload Module (PDF)</label>
        <input type="file" name="pdfFiles[]" id="pdfFiles" accept=".pdf" multiple>
      </div>

      <button type="submit">Add Module</button>
    </form>
  </div>
</div>
    <!---------------ADD MODULE---------------------------->

<script>
    // Show/hide inputs based on postType selection
    document.getElementById('postType').addEventListener('change', function() {
    const postType = this.value;
    const textInput = document.getElementById('textInput');
    const imageUpload = document.getElementById('imageUpload');
    const pdfFiles = document.getElementById('pdfFiles');
    const youtubeUrl = document.getElementById('youtube_url');

    if (postType === 'Image') {
        textInput.style.display = 'none';
        imageUpload.style.display = 'block';
        youtubeUrl.value = ''; // Clear youtube URL when PDF is selected
        youtubeUrl.required = false;
        pdfFiles.required = true;
    } else if (postType === 'Text') {
        imageUpload.style.display = 'none';
        textInput.style.display = 'block';
        pdfFiles.value = ''; // Clear file input when video is selected
        pdfFiles.required = false;
        youtubeUrl.required = true;
    }
    });
</script>
<!-- Script for hiding text and image-->
    
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