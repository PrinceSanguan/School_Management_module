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

// Function to get subjects for a teacher
function getTeacherSubjects($conn, $userId) {
    $sql = "SELECT s.id AS subject_id, s.subject, ts.status
            FROM teacherSubject ts
            JOIN subject s ON ts.subject_id = s.id
            WHERE ts.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// Function to update subject status
function updateSubjectStatus($conn, $subjectId, $newStatus) {
    $updateSql = "UPDATE teacherSubject SET status = ? WHERE subject_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $newStatus, $subjectId);
    $success = $updateStmt->execute();
    $updateStmt->close();
    return $success;
}

// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id'])) {
    $subjectId = $_POST['subject_id'];
    $currentStatus = $_POST['current_status'];
    $newStatus = ($currentStatus === 'publish') ? 'unpublish' : 'publish';

    if (updateSubjectStatus($conn, $subjectId, $newStatus)) {
        $_SESSION['success'] = 'Status has been updated';
    } else {
        $_SESSION['error'] = 'Status has not been updated';
    }

    // Redirect to avoid form resubmission
    header("Location: assign_subject.php");
    exit();
}

// Get teacher subjects
$result = getTeacherSubjects($conn, $userId);

// Close the connection after all operations are done
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
        <a href="../teacher/dashboard.php">Dashboard</a>
        <a href="../teacher/assign_subject.php">Assigned Subject</a>
        <a href="../admin/announcement.php">Announcement</a>
        <a href="../admin/registration.php">Registration</a>
        <a href="../admin/student-registration.php">Student Registration</a>
        <a href="../admin/calendar.php">Calendar</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are results
                if ($result->num_rows > 0) {
                    // Fetch and display each row
                    while ($row = $result->fetch_assoc()) {
                        $buttonText = ($row['status'] === 'publish') ? 'Unpublish' : 'Publish';
                        $buttonColor = ($row['status'] === 'publish') ? 'red' : 'green';
                        $newStatus = ($row['status'] === 'publish') ? 'unpublish' : 'publish';
                        $subjectId = $row['subject_id'];

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['subject']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                        echo '<td>';
                        echo '<form method="POST" action="">'; // Start form
                        echo '<input type="hidden" name="subject_id" value="' . $subjectId . '">';
                        echo '<input type="hidden" name="current_status" value="' . htmlspecialchars($row['status']) . '">';
                        echo '<button type="submit" style="padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); background-color: ' . $buttonColor . '; color: white;">' . $buttonText . '</button>';
                        echo '</form>'; // End form
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3">No subjects found.</td></tr>';
                }
                ?>
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