<?php
    session_start();
    if (isset($_SESSION['show_alert'])) {
        $alert_type = $_SESSION['show_alert'];
        unset($_SESSION['show_alert']);
    } else {
        $alert_type = '';
    }

    if (isset($_SESSION['login_error'])) {
        $error_message = $_SESSION['login_error'];
        unset($_SESSION['login_error']);
    } else {
        $error_message = '';
    }

    if (isset($_SESSION['login_email'])) {
        $login_email = $_SESSION['login_email'];
        unset($_SESSION['login_email']);
    } else {
        $login_email = '';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
            background-size: cover;
            color:white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ms-5" href="#">Teacher's Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="teacher-main.php">Create Assignment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-announcement.php">Admin Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view-assignments.php">View Assignments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view-submission.php">View Submissions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recent-activities.php">Recent Activities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4 text-center text-white">Create New Assignment</h2>
        <form action="teacher-main-backend.php" method="post">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="instruction" class="form-label">Instruction</label>
                        <textarea class="form-control" id="instruction" name="instruction" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (in minutes)</label>
                        <input type="number" class="form-control" id="duration" name="duration" min="1" required>
                    </div>
                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">Create Assignment</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    <script>
         document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status');

                if (status) {
                    let title = '';
                    let text = '';
                    let icon = '';

                    switch (status) {
                        case 'success':
                            title = 'Success!';
                            text = 'Assignment created successfully.';
                            icon = 'success';
                            break;
                        case 'error':
                            title = 'Error!';
                            text = 'There was an error creating the assignment.';
                            icon = 'error';
                            break;
                        default:
                            return;
                    }

                    Swal.fire({
                        icon: icon,
                        title: title,
                        text: text,
                        confirmButtonText: 'Okay'
                    });
                }
            });
    </script>
</body>
</html>
