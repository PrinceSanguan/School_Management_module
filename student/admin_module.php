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
    <style>
        .progress-bar-container {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            margin-top: 20px;
            position: relative;
        }

        .progress-bar {
            height: 20px;
            width: 0;
            background-color: #4caf50;
            border-radius: 5px;
            transition: width 0.5s;
        }

        .progress-text {
            position: absolute;
            width: 100%;
            text-align: center;
            top: 0;
            line-height: 20px; /* Center vertically */
            color: #000;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php" style="color: wheat;">Modules</a>
    <a href="../student/task.php">Task</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

    <div class="progress-bar-container">
        <div class="progress-bar" id="progressBar"></div>
        <div class="progress-text" id="progressText">0% - You're just getting started</div>
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
                                <a href="<?= htmlspecialchars($module['image_url']) ?>" class="view-pdf" target="_blank">View PDF</a>
                            <?php elseif (!empty(trim($module['youtube_url']))): ?>
                                <?= $module['youtube_url'] ?>
                            <?php else: ?>
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

<script>
    let progress = 0;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const viewedPDFs = new Set();

    function updateProgress() {
        if (progress <= 100) {
            progressBar.style.width = progress + '%';
            const messages = [
                '0% - You\'re just getting started! Every great journey begins with a single step. Keep going!',
                '10% - You\'re making progress! Keep up the momentum, and soon you\'ll see the fruits of your hard work.',
                '20% - Great Job! You\'re well on your way. Remember, consistency is the key to success.',
                '30% - You\'re doing amazing! Stay focused and keep pushing forward. You\'re closer than you think.',
                '40% - Halfway there! You’ve come so far. Keep up the great work, and you’ll reach your goal.',
                '50% - Fantastic progress! The effort you’re putting in now will pay off big time. Keep it up!',
                '60% - You’re in the home stretch! Stay strong and keep your eye on the prize.',
                '70% - Almost there! Your dedication is inspiring. Just a little further to go!',
                '80% - You’re so close! Keep pushing, and you’ll soon achieve what you set out to do.',
                '100% - Congratulations! You’ve made it to the finish line. Celebrate your success. You’ve earned it!'
            ];
            progressText.textContent = messages[Math.floor(progress / 10)];

            if (progress < 100) {
                progress++;
            }
        }
    }

    document.querySelectorAll('.view-pdf').forEach(link => {
        link.addEventListener('click', (event) => {
            const pdfUrl = event.currentTarget.href;
            if (!viewedPDFs.has(pdfUrl)) {
                viewedPDFs.add(pdfUrl);
                if (progress < 100) {
                    progress += 10; // Increase progress by 10% on first click
                    updateProgress();
                }
            }
        });
    });
</script>

</body>
</html>