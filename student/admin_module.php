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

// Fetch the section that is associated with the student
$sectionQuery = "SELECT section.section 
                 FROM studentSection 
                 JOIN section ON studentSection.section_id = section.id 
                 WHERE studentSection.user_id = ?";

$sectionStmt = $conn->prepare($sectionQuery);
$sectionStmt->bind_param("i", $userId);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$studentSection = $sectionResult->fetch_assoc();

// Check if a section was found
if (!$studentSection) {
    $_SESSION['error'] = "No section found for the student.";
    header("Location: announcement.php");
    exit();
}

// Fetch the subjects associated with the section
$subjectQuery = "SELECT subject.id AS subject_id, subject.subject 
                 FROM subject 
                 WHERE subject.section_id = (
                     SELECT section_id FROM studentSection WHERE user_id = ?
                 )";

$subjectStmt = $conn->prepare($subjectQuery);
$subjectStmt->bind_param("i", $userId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

// Initialize an empty array to store subjects and associated modules
$subjectsWithModules = [];

while ($subjectRow = $subjectResult->fetch_assoc()) {
    $subjectId = $subjectRow['subject_id'];
    $subjectName = $subjectRow['subject'];

    // Fetch published subject images (modules) for this subject, including youtube_url
    $moduleQuery = "SELECT week, image_url, youtube_url 
                    FROM subject_images 
                    WHERE subject_id = ? AND status = 'publish'";

    $moduleStmt = $conn->prepare($moduleQuery);
    $moduleStmt->bind_param("i", $subjectId);
    $moduleStmt->execute();
    $moduleResult = $moduleStmt->get_result();

    // Add the subject and its modules to the array
    while ($moduleRow = $moduleResult->fetch_assoc()) {
        $subjectsWithModules[] = [
            'subject' => $subjectName,
            'week' => $moduleRow['week'],
            'image_url' => $moduleRow['image_url'],
            'youtube_url' => $moduleRow['youtube_url']
        ];
    }
}

$sectionStmt->close();
$subjectStmt->close();
$moduleStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Student Dashboard</title>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php" style="color: wheat;">Admin Module</a>
    <a href="../student/task.php">Task</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Week</th>
                <th>View PDF/Embedded Video</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($subjectsWithModules)): ?>
                <?php foreach ($subjectsWithModules as $module): ?>
                    <tr>
                        <td><?= htmlspecialchars($module['subject']) ?></td>
                        <td><?= htmlspecialchars($module['week']) ?></td>
                        <td>
                            <?php if (!empty(trim($module['image_url']))): ?>
                                <!-- Display PDF link if image_url is not empty -->
                                <a href="<?= htmlspecialchars($module['image_url']) ?>" target="_blank">View PDF</a>
                            <?php elseif (!empty(trim($module['youtube_url']))): ?>
                                <!-- Display embedded YouTube video if youtube_url is not empty -->
                                 <?= $module['youtube_url'] ?>
                            <?php else: ?>
                                <!-- Fallback if both are empty -->
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No published modules found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>