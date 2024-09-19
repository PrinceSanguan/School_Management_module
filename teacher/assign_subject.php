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
                            <td ><?= htmlspecialchars($subject['subject']) ?></td>
                            <td><?= htmlspecialchars($subject['week']) ?></td>
                            <td><a href="<?= htmlspecialchars($subject['image_url']) ?>" target="_blank">View PDF</a></td>
                            <td><?= htmlspecialchars($subject['status']) ?></td>
                            <td>
                                <!-- Form for Publish/Unpublish -->
                                <form method="POST" action="../controller/TeacherController/update_status.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($subject['id']) ?>">
                                    <?php if ($subject['status'] == 'publish'): ?>
                                        <input type="hidden" name="action" value="unpublish">
                                        <button type="submit" style="background-color:#dc3545;  color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Unpublish</button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="publish">
                                        <button type="submit" style="background-color:#28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 5px;">Publish</button>
                                    <?php endif; ?>
                                </form>
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
</body>
</html>
