<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="container mt-5">
        <h2 class="text-center text-white">Add Teacher</h2>
        <form id="teacherForm" action="teacher-post.php" method="POST" class="form-container">
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control border-dark border-2" id="firstName" name="first_name" required>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control border-dark border-2" id="lastName" name="last_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border-dark border-2" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="number" class="form-control border-dark border-2" id="contact_number" name="contact_number" required>
            </div>
            <div class="d-flex gap-1">
                <button type="submit" class="btn btn-primary">Add</button>
                <a href="teachers-admin.php" class="btn btn-primary">Back</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    <script>
document.getElementById('teacherForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('teacher-post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result === 'duplicate') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Email already exists!',
                confirmButtonText: 'Okay'
            });
        } else if (result === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Teacher added successfully!',
                confirmButtonText: 'Okay'
            }).then(() => {
                window.location.href = 'teachers-admin.php?status=success';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was a problem processing your request.',
                confirmButtonText: 'Okay'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again later.',
            confirmButtonText: 'Okay'
        });
    });
});
</script>

</body>
</html>
