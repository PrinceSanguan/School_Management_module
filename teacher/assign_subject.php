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

// Query to get subjects associated with the teacher, grouped by subject
$query = "SELECT DISTINCT subject.id AS subject_id, subject.subject, 
          GROUP_CONCAT(DISTINCT subject_images.week ORDER BY subject_images.week) AS weeks,
          MAX(subject_images.status) AS status 
          FROM teacherSubject
          JOIN subject ON teacherSubject.subject_id = subject.id
          LEFT JOIN subject_images ON subject.id = subject_images.subject_id
          WHERE teacherSubject.user_id = ?
          GROUP BY subject.id, subject.subject";

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
    <title>Teacher Assigned Subjects</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: black;
        }
        .subject-link {
            color: #007bff;
            text-decoration: none;
        }
        .subject-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php">Announcement</a>
        <a href="../teacher/assign_subject.php" style="color: wheat;">Subject</a>
        <a href="../teacher/task.php">Task</a>
        <a href="../teacher/profile.php">Profile</a>
        <a href="../teacher/monitoring.php">Monitoring</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
        <div class="burger">&#9776;</div>
    </div>

    <div class="container">
        <h2>Assigned Subjects</h2>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Weeks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subjects)): ?>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td>
                                <a href="subject_modules.php?subject_id=<?= urlencode($subject['subject_id']) ?>" class="subject-link">
                                    <?= htmlspecialchars($subject['subject']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($subject['weeks']) ?></td>
                            <td><?= htmlspecialchars($subject['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No subjects found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Toggle burger menu visibility
        const burger = document.querySelector('.burger');
        const navbar = document.querySelector('.navbar');
        burger.addEventListener('click', function () {
            navbar.classList.toggle('active');
        });
    </script>
</body>
</html>

