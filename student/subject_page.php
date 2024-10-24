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

// Fetch the subject ID from the URL
if (!isset($_GET['subject_id'])) {
    $_SESSION['error'] = "No subject selected.";
    header("Location: admin_module.php");
    exit();
}

$subjectId = $_GET['subject_id'];

// Fetch the subject name
$subjectQuery = "SELECT subject FROM subject WHERE id = ?";
$subjectStmt = $conn->prepare($subjectQuery);
$subjectStmt->bind_param("i", $subjectId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();
$subject = $subjectResult->fetch_assoc();

// Fetch published modules (images or videos) for the selected subject
$moduleQuery = "SELECT week, image_url, youtube_url 
                FROM subject_images 
                WHERE subject_id = ? AND status = 'publish'";

$moduleStmt = $conn->prepare($moduleQuery);
$moduleStmt->bind_param("i", $subjectId);
$moduleStmt->execute();
$moduleResult = $moduleStmt->get_result();

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>   
    <script>
        function updateProgress(subjectId, week) {
            $.ajax({
                url: 'update_progress.php',
                type: 'POST',
                data: {
                    subject_id: subjectId,
                    week: week
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>
    <title><?= htmlspecialchars($subject['subject']) ?> - Modules</title>
    <style>
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .youtube-video {
            width: 100%;
            height: 400px;
            border: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: black;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php">Modules</a>
    <a href="../student/task.php">Task</a>
    <a href="../student/profile.php">Profile</a>
    <a href="../student/achievement.php">Achievement</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
    <div class="burger">&#9776;</div>
</div>

<h2><?= htmlspecialchars($subject['subject']) ?> - Modules</h2>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>Week</th>
                <th>Module (PDF/Video)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($moduleResult->num_rows > 0): ?>
                <?php while ($module = $moduleResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($module['week']) ?></td>
                        <td>
                        <?php if (!empty($module['image_url'])): ?>
                            <?php
                            // Extract filename from the URL
                            $filename = basename($module['image_url']);
                            ?>
                            <p><a href="<?= htmlspecialchars($module['image_url']) ?>" target="_blank" onclick="updateProgress(<?= $subjectId ?>, '<?= $module['week'] ?>'); return true;"><?= htmlspecialchars($filename) ?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($module['youtube_url'])): ?>
                            <div><?= $module['youtube_url'] ?></div>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No modules found for this subject.</td>
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