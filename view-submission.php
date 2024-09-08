<?php
require_once 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all submissions
$sql = "SELECT s.id, a.title, s.file_name, s.file_path, s.submission_date, u.first_name, u.last_name 
        FROM submissions s 
        JOIN assignments a ON s.assignment_id = a.id 
        JOIN users u ON s.user_id = u.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
            color: white;
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
                    <a class="nav-link" href="view-assignments.php">View Assignments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="view-submission.php">View Submissions</a>
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
    <h2 class="mb-4 text-center">All Submissions</h2>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text">Submitted by: <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
                            <p class="card-text">File: <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($row['file_name']); ?></a></p>
                            <p class="card-text">Submitted on: <?php echo htmlspecialchars($row['submission_date']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No submissions found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
