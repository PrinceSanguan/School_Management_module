<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$sql = "SELECT title, a1, a2, a3 FROM student_announcement LIMIT 1"; // Adjust if you have more announcements
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    $announcement = $result->fetch_assoc();
} else {
    $announcement = null;
}

// Fetch user information
$sql = "SELECT first_name, last_name, email, lrn, parent_name, parent_email, parent_contact, section FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found";
    exit();
}

$first_name = $user['first_name'];
$last_name = $user['last_name'];
$parent_contact = $user['parent_contact'];

// Fetch assignments and check if submitted
$sql = "SELECT * FROM assignments";
$assignments = $conn->query($sql);

$submitted_assignments = [];
$check_submission_sql = "SELECT assignment_id FROM submissions WHERE user_id = ?";
$stmt = $conn->prepare($check_submission_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$submissions_result = $stmt->get_result();
while ($sub = $submissions_result->fetch_assoc()) {
    $submitted_assignments[] = $sub['assignment_id'];
}

$stmt->close();
$conn->close();
?>
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
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4 text-center">View Assignments</h2>
    <div class="row">
        <?php if ($assignments->num_rows > 0): ?>
            <?php while ($row = $assignments->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Date: <?php echo htmlspecialchars($row['date']); ?></h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['instruction'])); ?></p>
                            <p class="card-text">Duration: <?php echo htmlspecialchars($row['duration']); ?> minutes</p>
                            <?php
                            $isSubmitted = in_array($row['id'], $submitted_assignments);
                            if ($isSubmitted): ?>
                                <button class="btn btn-secondary" disabled>Already Submitted</button>
                            <?php else: ?>
                                <a href="submission.php?assignment_id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-success">Submit</a>
                            <?php endif; ?>
                            <a href="main.php" class="btn btn-primary ms-2">Back</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No assignments available.</p>
            <a href="main.php"><button class="btn btn-primary">Back</button></a>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
