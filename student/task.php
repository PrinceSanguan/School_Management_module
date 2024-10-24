<?php
include "../database/database.php"; 

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is a student
if ($_SESSION['userRole'] !== 'student') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

// Fetch the student ID from the session
$userId = $_SESSION['userId'];

// Step 1: Fetch the student's section
$sectionQuery = "SELECT section_id 
                 FROM studentSection 
                 WHERE user_id = ?";

$sectionStmt = $conn->prepare($sectionQuery);
$sectionStmt->bind_param("i", $userId);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$studentSection = $sectionResult->fetch_assoc();

if (!$studentSection) {
    $_SESSION['error'] = "No section found for the student.";
    header("Location: ../student/announcement.php");
    exit();
}

$sectionId = $studentSection['section_id'];

// Step 2: Fetch unique subjects associated with the student's section that have tasks
$subjectQuery = "SELECT DISTINCT subject.id AS subject_id, subject.subject
                 FROM subject
                 JOIN task ON subject.id = task.subject_id
                 WHERE subject.section_id = ? AND task.status = 'active'";

$subjectStmt = $conn->prepare($subjectQuery);
$subjectStmt->bind_param("i", $sectionId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

// Initialize an array to store subjects
$subjects = [];

while ($subjectRow = $subjectResult->fetch_assoc()) {
    $subjects[] = [
        'id' => $subjectRow['subject_id'],
        'name' => $subjectRow['subject']
    ];
}

// Close statements and connection
$sectionStmt->close();
$subjectStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Student Subjects</title>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php">Modules</a>
    <a href="../student/task.php" style="color: wheat;">Task</a>
    <a href="../student/profile.php">Profile</a>
    <a href="../student/achievement.php">Achievement</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
    <div class="burger">&#9776;</div>
</div>

<div class="container">
    <h2>Your Subjects with Tasks</h2>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>View Tasks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($subjects)): ?>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?= htmlspecialchars($subject['name']) ?></td>
                        <td>
                            <a href="task_details.php?subject_id=<?= htmlspecialchars($subject['id']) ?>">View Tasks</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No subjects with tasks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!---- Sweet Alert ---->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle burger menu visibility
        const burger = document.querySelector('.burger');
        const navbar = document.querySelector('.navbar');
        burger.addEventListener('click', function () {
            navbar.classList.toggle('active');
        });

        // Check for success message
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        // Check for error message
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'Try Again'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });
</script>

</body>
</html>