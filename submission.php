<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'upload/';
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $filePath = $uploadDir . $fileName;

        // Check if the file already exists in the submission table
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM submissions WHERE assignment_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $assignment_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $submission_message = 'You have already submitted an assignment for this task.';
        } else {
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $sql = "INSERT INTO submissions (assignment_id, user_id, file_name, file_path) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iiss', $assignment_id, $user_id, $fileName, $filePath);

                if ($stmt->execute()) {
                    $submission_message = 'File successfully uploaded.';
                } else {
                    $submission_message = 'File upload failed. Please try again.';
                }

                $stmt->close();
            } else {
                $submission_message = 'File move failed. Please try again.';
            }
        }

        $conn->close();
    } else {
        $submission_message = 'No file uploaded or there was an upload error.';
    }
}

// Fetch assignment data
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM assignments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment</title>
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
    <?php if (isset($submission_message) && !empty($submission_message)): ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Submission Status</h5>
                        <p class="card-text text-center"><?php echo htmlspecialchars($submission_message); ?></p>
                        <div class="text-center">
                            <a href="assignment.php" class="btn btn-primary">Back to Assignments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($assignment): ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($assignment['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($assignment['instruction'])); ?></p>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload Your Image</label>
                                <input class="form-control" type="file" id="file" name="file" accept="image/*" required>
                                <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment_id); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Error</h5>
                        <p class="card-text text-center">Assignment not found.</p>
                        <div class="text-center">
                            <a href="assignment.php" class="btn btn-primary">Back to Assignments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
