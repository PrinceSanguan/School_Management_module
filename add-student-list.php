<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a class="nav-link active" href="#">Student Lists and Grades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="#">Add Module Topic</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teacher-logout.php">Logout</a>
                </li>
            </ul>
        </div>
</nav>
<body>
    <div class="container mt-3 p-3">
        <h1 class="mb-4 text-center">Add New Student List</h1>
        <form action="student-list-backend.php" method="post" enctype="multipart/form-data" class="form-container">
            <div class="form-group">
                <label for="image">Student Image</label>
                <input type="file" class="form-control-file" name="image" id="image" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control border-dark border-2" name="first_name" id="first_name" required>
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control border-dark border-2" name="last_name" id="last_name" required>
            </div>
            <div class="mb-3">
                <label for="p1_grade">P1 Grade</label>
                <input type="number" class="form-control border-dark border-2" name="p1_grade" id="p1_grade">
            </div>
            <div class="mb-3">
                <label for="p2_grade">P2 Grade</label>
                <input type="number" class="form-control border-dark border-2" name="p2_grade" id="p2_grade">
            </div>
            <div class="mb-3">
                <label for="p3_grade">P3 Grade</label>
                <input type="number" class="form-control border-dark border-2" name="p3_grade" id="p3_grade">
            </div>
            <button type="submit" class="btn btn-primary">Add New Student List</button>
            <a href="teacher-main.php" class="btn btn-primary">Back</a>
        </form>
    </div>
</body>
</html>

</body>
</html>
