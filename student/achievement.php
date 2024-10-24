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

// Calculate progress percentage
$progressQuery = "SELECT u.progress, 
                        (SELECT COUNT(*) FROM subject_images WHERE status = 'publish') as total_modules
                 FROM users u 
                 WHERE u.id = ?";
                 
$stmt = $conn->prepare($progressQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$progressData = $result->fetch_assoc();

$progress = 0;
if ($progressData['total_modules'] > 0) {
    $progress = ($progressData['progress'] / $progressData['total_modules']) * 100;
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
    <title>Student Achievement</title>
    <style>
        .achievement-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .progress-bar-container {
            width: 100%;
            height: 30px;
            background-color: #f0f0f0;
            border-radius: 15px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            transition: width 0.5s ease-in-out;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .badges-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
        }

        .badge {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            filter: grayscale(100%);
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .badge.unlocked img {
            filter: grayscale(0%);
            opacity: 1;
        }

        .badge-label {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }

        .badge:hover {
            transform: scale(1.1);
        }

        .progress-info {
            text-align: center;
            margin: 20px 0;
            color: #333;
            font-size: 1.2em;
        }

        .achievement-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .badge-wrapper {
            text-align: center;
        }

        .locked-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php">Modules</a>
    <a href="../student/task.php">Task</a>
    <a href="../student/profile.php">Profile</a>
    <a href="../student/achievement.php" style="color: wheat;">Achievement</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
    <div class="burger">&#9776;</div>
</div>

<div class="achievement-container">
    <h1 class="achievement-title">Your Learning Achievements</h1>
    
    <div class="progress-info">
        Overall Progress: <?= round($progress, 1) ?>%
    </div>
    
    <div class="progress-bar-container">
        <div class="progress-bar" style="width: <?= $progress ?>%">
            <?= round($progress, 1) ?>%
        </div>
    </div>

    <div class="badges-container">
        <?php
        $badges = [
            ['name' => 'Beginner', 'threshold' => 20, 'image' => '../badges/20%.png'],
            ['name' => 'Explorer', 'threshold' => 40, 'image' => '../badges/40%.png'],
            ['name' => 'Achiever', 'threshold' => 60, 'image' => '../badges/60%.png'],
            ['name' => 'Expert', 'threshold' => 80, 'image' => '../badges/80%.png'],
            ['name' => 'Master', 'threshold' => 100, 'image' => '../badges/100%.png']
        ];

        foreach ($badges as $badge): ?>
            <div class="badge-wrapper">
                <div class="badge <?= $progress >= $badge['threshold'] ? 'unlocked' : '' ?>">
                    <img src="<?= $badge['image'] ?>" alt="<?= $badge['name'] ?> Badge">
                    <?php if ($progress < $badge['threshold']): ?>
                        <div class="locked-overlay">🔒</div>
                    <?php endif; ?>
                </div>
                <div class="badge-label">
                    <?= $badge['name'] ?><br>
                    (<?= $badge['threshold'] ?>%)
                </div>
            </div>
        <?php endforeach; ?>
    </div>
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