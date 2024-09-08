<?php
// Database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is set
if (!isset($_GET['id'])) {
    header("Location: teachers-admin.php?status=error");
    exit();
}

$id = intval($_GET['id']); // Get the ID from the query string

// Prepare and execute select statement
$sql = "SELECT * FROM teachers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: teachers-admin.php?status=error");
    exit();
}

$teacher = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Teacher</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
        }
        .form-container {
            border: 2px solid #000;
            border-radius: 0.375rem;
            padding: 2rem;
            background-color: #fff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ms-5" href="#">School Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link" href="update-admin.php">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teacher-announcement.php">Teacher Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student-announcement.php">Student Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view-subjects.php">View Subjects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users-admin.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="teachers-admin.php">Teachers List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <h2 class="mb-4 text-center text-white">Edit Teacher <?php echo htmlspecialchars($teacher['first_name']); ?>'s Information</h2>
        <form action="update-teacher-backend.php" method="POST" class="form-container">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($teacher['id']); ?>">
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control border-dark border-2" id="firstName" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control border-dark border-2" id="lastName" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border-dark border-2" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control border-dark border-2" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($teacher['contact_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control border-dark border-2" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="teachers-admin.php" class="btn btn-primary">Back</a>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq7F2L8QO4u7/82npS2lW+Q0ODN+OGZKb/4Xw0vI+FwX6lEo+0" crossorigin="anonymous"></script>
    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status === 'email_exists') {
                Swal.fire({
                    title: 'Email Exists!',
                    text: 'A teacher with this email already exists.',
                    icon: 'error'
                });
            } else if (status === 'updated') {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Teacher record has been updated successfully.',
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
</body>
</html>
