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
    header("Location: users-admin.php?status=error");
    exit();
}

$id = intval($_GET['id']); // Get the ID from the query string

// Prepare and execute select statement
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: users-admin.php?status=error");
    exit();
}

$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 56px; /* Adjust padding to avoid content being hidden under the navbar */
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
        }
        .form-container {
            border: 2px solid #000; /* Dark border for the form */
            border-radius: 0.375rem; /* Rounded corners */
            padding: 2rem; /* Padding inside the form */
            background-color: #fff; /* White background for better readability */
        }
    </style>
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
                    <a class="nav-link active" href="users-admin.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teachers-admin.php">Teachers List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-2">
        <h2 class="text-center text-white">Edit <?php echo htmlspecialchars($user['first_name']); ?>'s Information</h2>
        <form action="update-users-backend.php" method="POST" class="form-container">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <div class="mb-3">
                <label for="first_name" class="form-label">first_name</label>
                <input type="text" class="form-control border-dark border-2" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">last name</label>
                <input type="text" class="form-control border-dark border-2" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lrn" class="form-label">lrn</label>
                <input type="text" class="form-control border-dark border-2" id="lrn" name="lrn" value="<?php echo htmlspecialchars($user['lrn']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border-dark border-2" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="parent_name" class="form-label">parent name</label>
                <input type="text" class="form-control border-dark border-2" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($user['parent_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="parent_email" class="form-label">parent email</label>
                <input type="text" class="form-control border-dark border-2" id="parent_email" name="parent_email" value="<?php echo htmlspecialchars($user['parent_email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="parent_contact" class="form-label">parent contact</label>
                <input type="text" class="form-control border-dark border-2" id="parent_contact" name="parent_contact" value="<?php echo htmlspecialchars($user['parent_contact']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="section" class="form-label">Section</label>
                <input type="text" class="form-control border-dark border-2" id="section" placeholder="No Section Yet" name="section"  value="<?php echo htmlspecialchars($user['section']); ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (Leave blank to keep unchanged)</label>
                <input type="password" class="form-control border-dark border-2" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="users-admin.php" class="btn btn-primary">Back</a>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq7F2L8QO4u7/82npS2lW+Q0ODN+OGZKb/4Xw0vI+FwX6lEo+0" crossorigin="anonymous"></script>
    <!-- Custom Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'email_exists') {
                Swal.fire({
                    title: 'Email Exists!',
                    text: 'A user with this email already exists.',
                    icon: 'error'
                });
            } else if (status === 'error') {
                Swal.fire({
                    title: 'Update Failed!',
                    text: 'There was an error updating the user. Please try again.',
                    icon: 'error'
                });
            } else if (status === 'updated') {
                Swal.fire({
                    title: 'Update Successful!',
                    text: 'The user information has been updated successfully.',
                    icon: 'success'
                });
            }
        });
    </script>
</body>
</html>
