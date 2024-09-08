<?php
// Database configuration
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve student ID from the query parameter
$student_id = intval($_GET['id']);

// Fetch current student data
$sql = "SELECT id, first_name, last_name, p1_grade, p2_grade, p3_grade, image FROM students_grades WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <!-- Bootstrap CSS -->
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body {
        padding-top: 56px; /* Adjust padding to avoid content being hidden under the navbar */
        font-family: "New Amsterdam", sans-serif;
        background-image: url('./images/background2.jpg'); /* Adjust path if needed */
    }
    .form-container {
        border: 2px solid #000; /* Dark border for the form */
        border-radius: 0.375rem; /* Rounded corners */
        padding: 2rem; /* Padding inside the form */
        background-color: #fff; /* White background for better readability */
    }
</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ms-5" href="#">Teacher's admin</a> 
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Student Lists and Grades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Add Module Topic</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teacher-logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-1">
        <h2 class="text-center">Edit Student</h2>
        <form action="update-student-list-backend.php" method="post" enctype="multipart/form-data" class="form-container">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
            <div class="form-group">
                <label for="image">Image</label>
                <?php if (!empty($student['image'])): ?>
                    <br>
                    <img src="<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" style="max-width: 30px; max-height: 30px;">
                    <br><br>
                <?php endif; ?>
                <input type="file" class="form-control-file" name="image" id="image" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control border-dark border-2" name="first_name" id="first_name" required value="<?php echo htmlspecialchars($student['first_name']); ?>">
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control border-dark border-2" name="last_name" id="last_name" required value="<?php echo htmlspecialchars($student['last_name']); ?>">
            </div>
            <div class="mb-3">
                <label for="p1_grade">P1 Grade</label>
                <input type="text" class="form-control border-dark border-2" name="p1_grade" id="p1_grade" value="<?php echo htmlspecialchars($student['p1_grade']); ?>">
            </div>
            <div class="mb-3">
                <label for="p2_grade">P2 Grade</label>
                <input type="text" class="form-control border-dark border-2" name="p2_grade" id="p2_grade" value="<?php echo htmlspecialchars($student['p2_grade']); ?>">
            </div>
            <div class="mb-3">
                <label for="p3_grade">P3 Grade</label>
                <input type="text" class="form-control border-dark border-2" name="p3_grade" id="p3_grade" value="<?php echo htmlspecialchars($student['p3_grade']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="teacher-main.php" class="btn btn-primary">Back</a>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
