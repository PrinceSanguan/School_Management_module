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
$query = "SELECT subject.subject, subject_images.week, subject_images.image_url, subject_images.youtube_url
          FROM subject
          JOIN subject_images ON subject.id = subject_images.subject_id
          WHERE subject.id = ?";

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
        /* Add your styles here */
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php">Announcement</a>
        <a href="../teacher/assign_subject.php">Subject</a>
        <a href="../teacher/task.php">Task</a>
        <a href="../teacher/profile.php">Profile</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
    </div>

    <div class="container">
        <h2>Modules for <?= htmlspecialchars($subjectName) ?></h2>
        <?php if (!empty($modules)): ?>
            <?php foreach ($modules as $module): ?>
                <div class="module">
                    <h3><?= htmlspecialchars($module['week']) ?></h3>
                    <?php if (!empty($module['image_url'])): ?>
                        <p><a href="<?= htmlspecialchars($module['image_url']) ?>" target="_blank">View PDF</a></p>
                    <?php endif; ?>
                    <?php if (!empty($module['youtube_url'])): ?>
                        <div><?= $module['youtube_url'] ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No modules found for this subject.</p>
        <?php endif; ?>
    </div>
</body>
</html>
