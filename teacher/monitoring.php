<?php
include "../database/database.php";
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to access this page!";
    header("Location: ../index.php");
    exit();
}

// Fetch all subjects
$subjectQuery = "SELECT DISTINCT s.id, s.subject, sec.section
                 FROM subject s
                 JOIN section sec ON s.section_id = sec.id
                 ORDER BY sec.section, s.subject";
$subjectResult = $conn->query($subjectQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Monitoring</title>
    <style>
        .subject-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .subject-button {
            padding: 10px 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            cursor: pointer;
        }
        .subject-button.active {
            background-color: #007bff;
            color: white;
        }
        #studentProgress {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php">Announcement</a>
        <a href="../teacher/assign_subject.php">Subject</a>
        <a href="../teacher/task.php">Task</a>
        <a href="../teacher/profile.php">Profile</a>
        <a href="../teacher/monitoring.php" style="color: wheat;">Monitoring</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
        <div class="burger">&#9776;</div>
    </div>

    <div class="container">
        <h2>Monitoring</h2>
        
        <div class="subject-list">
            <?php 
            $currentSection = '';
            while ($subject = $subjectResult->fetch_assoc()):
                if ($currentSection != $subject['section']):
                    if ($currentSection != '') echo '</div><div class="subject-list">';
                    $currentSection = $subject['section'];
                    echo "<h3>{$subject['section']}</h3>";
                endif;
            ?>
                <button class="subject-button" data-subject-id="<?= $subject['id'] ?>" data-section="<?= $subject['section'] ?>">
                    <?= htmlspecialchars($subject['subject']) ?>
                </button>
            <?php endwhile; ?>
        </div>

        <div id="studentProgress">
            <p>Select a subject to view enrolled students.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectButtons = document.querySelectorAll('.subject-button');
            const studentProgressDiv = document.getElementById('studentProgress');

            subjectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const subjectId = this.getAttribute('data-subject-id');
                    const section = this.getAttribute('data-section');
                    
                    // Remove active class from all buttons
                    subjectButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    // Fetch enrolled students for the selected subject
                    fetch(`student_progress.php?subject_id=${subjectId}&section=${encodeURIComponent(section)}`)
                        .then(response => response.text())
                        .then(data => {
                            studentProgressDiv.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            studentProgressDiv.innerHTML = 'Error loading enrolled students.';
                        });
                });
            });
        });
    </script>

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