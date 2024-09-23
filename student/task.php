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

// Check if a section was found
if (!$studentSection) {
    $_SESSION['error'] = "No section found for the student.";
    header("Location: ../student/announcement.php");
    exit();
}

$sectionId = $studentSection['section_id'];

// Step 2: Fetch the subjects associated with the section
$subjectQuery = "SELECT id AS subject_id, subject 
                 FROM subject 
                 WHERE section_id = ?";

$subjectStmt = $conn->prepare($subjectQuery);
$subjectStmt->bind_param("i", $sectionId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

// Initialize an array to store tasks
$tasks = [];

while ($subjectRow = $subjectResult->fetch_assoc()) {
    $subjectId = $subjectRow['subject_id'];
    $subjectName = $subjectRow['subject'];

    // Step 3: Fetch tasks associated with the subject
    $taskQuery = "SELECT task_title, content, image_path, deadline 
                  FROM task 
                  WHERE subject_id = ? AND status = 'active'";

    $taskStmt = $conn->prepare($taskQuery);
    $taskStmt->bind_param("i", $subjectId);
    $taskStmt->execute();
    $taskResult = $taskStmt->get_result();

    // Add tasks to the array
    while ($taskRow = $taskResult->fetch_assoc()) {
        $tasks[] = [
            'subject' => $subjectName,
            'task_title' => $taskRow['task_title'],
            'content' => $taskRow['content'],
            'image_path' => $taskRow['image_path'],
            'deadline' => $taskRow['deadline']
        ];
    }
}

// Close statements and connection
$sectionStmt->close();
$subjectStmt->close();
$taskStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Student Task</title>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php">Admin Module</a>
    <a href="../student/task.php" style="color: wheat;">Task</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Task Title</th>
                <th>Task Details</th>
                <th>Deadline</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['subject']) ?></td>
                        <td><?= htmlspecialchars($task['task_title']) ?></td>
                        <td>
                            <?php if (!empty($task['content'])): ?>
                                <?= htmlspecialchars($task['content']) ?>
                            <?php elseif (!empty($task['image_path'])): ?>
                                <img src="../<?= htmlspecialchars($task['image_path']) ?>" alt="Task Image" style="max-width: 200px; height: auto;">
                            <?php else: ?>
                                No details available.
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($task['deadline']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No tasks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>