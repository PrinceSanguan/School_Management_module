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
    <title>Student Dashboard</title>
    <style>
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4CAF50;
        }
        .subject-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .subject-item {
            background-color: #f0f0f0;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .subject-item:hover {
            transform: scale(1.05);
            background-color: #e6ffe6;
        }
        .subject-item a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php" style="color: wheat;">Modules</a>
    <a href="../student/task.php">Task</a>
    <a href="../student/profile.php">Profile</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

<h2>Click the subject to view its modules</h2>

<div class="container">
    <div class="subject-list">
        <?php if ($subjectResult->num_rows > 0): ?>
            <?php while ($subject = $subjectResult->fetch_assoc()): ?>
                <div class="subject-item">
                    <a href="subject_page.php?subject_id=<?= htmlspecialchars($subject['subject_id']) ?>">
                        <?= htmlspecialchars($subject['subject']) ?>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No subjects found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
