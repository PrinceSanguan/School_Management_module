<?php
session_start();
require_once 'db_connect.php';

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$sql = "SELECT title, a1, a2, a3 FROM student_announcement LIMIT 1";
$result = $conn->query($sql);

$announcement = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

// Fetch recent activities
$sql = "SELECT title, r1, r2, r3 FROM recent_activities LIMIT 1";
$result = $conn->query($sql);

$activities = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

// Fetch user details
$sql = "SELECT first_name, last_name, email, lrn, parent_name, parent_email, parent_contact, section FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Redirect to login if user details are not found
if (!$user) {
    header('Location: login.php');
    exit();
}

$first_name = $user['first_name'];
$last_name = $user['last_name'];
$parent_contact = $user['parent_contact'];

// Close statement and connection
$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A; 
        }
        .dashboard-card {
            margin-bottom: 1rem;
            background-color: #227B94;
        }
        .gallery img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }
        .gallery .col-md-4 {
            margin-bottom: 1rem;
        }
        .zoom-effect {
            position: relative;
            overflow: hidden;
        }
        .zoom-effect img {
            transition: transform 0.5s ease;
        }
        .zoom-effect:hover img {
            transform: scale(1.1);
        }
        .list-unstyled{
            color: black;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">ANTIPOLO CITY SPED CENTER</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="user-logout-backend.php">Logout</a>
                    </li>
                </ul>
                <span class="navbar-text ms-auto">
                <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
                </span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="text-center text-white mb-4">Dashboard</h2>
        <div class="row">
            <!-- Quick Links -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="btn btn-light w-100 mb-2" data-bs-toggle="modal" data-bs-target="#subjectModal">Subjects</a></li>
                            <li><a href="assignment.php" class="btn btn-light w-100 mb-2">Assignments</a></li>
                            <li><a href="#" class="btn btn-light w-100 mb-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-md-8">
            <div class="card dashboard-card shadow-sm">
                <div class="card-body">
                    <?php if ($activities): ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($activities['title']); ?></h5>
                        <ul class="list-unstyled">
                            <li><strong><?php echo htmlspecialchars($activities['r1']); ?></strong></li>
                            <li><strong><?php echo htmlspecialchars($activities['r2']); ?></strong></li>
                            <li><strong><?php echo htmlspecialchars($activities['r3']); ?></strong></li>
                        </ul>
                    <?php else: ?>
                        <p>No recent activities today.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        </div>

        <!-- Announcements -->
        <div class="card dashboard-card shadow-sm">
        <div class="card-body">
        <?php if ($announcement): ?>
            <h5 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
            <ul class="list-unstyled">
                <li><strong><?php echo htmlspecialchars($announcement['a1']); ?></strong></li>
                <li><strong><?php echo htmlspecialchars($announcement['a2']); ?></strong></li>
                <li><strong><?php echo htmlspecialchars($announcement['a3']); ?></strong></li>
                </ul>
            <?php else: ?>
                <p>No announcements available at this time.</p>
            <?php endif; ?>
        </div>
    </div>

    </div>


        <!-- Image Gallery -->
        <div class="container mt-4">
            <div class="row gallery">
                <div class="col-md-4">
                    <div class="zoom-effect">
                        <img src="images/image1.jpg" alt="Activities" class="img-fluid">
                 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="zoom-effect">
                        <img src="images/image2.jpg" alt="Classroom Setup" class="img-fluid">
               
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="zoom-effect">
                        <img src="images/image3.jpg" alt="School Building" class="img-fluid">
            
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php include 'modals.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
         document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['show_alert']) && $_SESSION['show_alert']): ?>
            Swal.fire({
                title: 'Welcome back!',
                text: 'You can edit your information through the quick links below.',
                icon: 'info',
            }).then(() => {
                <?php unset($_SESSION['show_alert']); ?> // Clear the alert flag
            });
        <?php endif; ?>
        });
        function validateForm() {
            var password = document.getElementById('password').value;
            var confirm_password = document.getElementById('confirm_password').value;

            if (password === confirm_password) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Password Updated Successfully.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('editProfileForm').submit();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Passwords do not match.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
