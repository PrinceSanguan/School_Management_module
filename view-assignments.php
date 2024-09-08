<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignments</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
            color:white;
        }
        .card {
            background-color: white;
            border: none;
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
                    <a class="nav-link" href="teacher-main.php">Create Assignment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-announcement.php">Admin Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="view-assignments.php">View Assignments</a>
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
    <h2 class="mb-4 text-center">View Assignments</h2>
    <div class="row">
        <?php
        include 'db_connect.php'; 

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM assignments";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-3">';
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">Date: ' . htmlspecialchars($row['date']) . '</h6>';
                echo '<p class="card-text">' . nl2br(htmlspecialchars($row['instruction'])) . '</p>';
                echo '<p class="card-text">Duration: ' . htmlspecialchars($row['duration']) . ' minutes</p>';
                echo '<a href="delete-assignment.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-danger btn-sm" onclick="return confirmDelete();">Delete</a>'; // Added delete button
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center">No assignments available.</p>';
        }

        $conn->close();
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this assignment?');
    }
    document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status === 'deleted') {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Assignment has been deleted.',
                    icon: 'success'
                });
            } else if (status === 'error') {
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error processing the request.',
                    icon: 'error'
                });
            }
        });
</script>

</script>
</body>
</html>
