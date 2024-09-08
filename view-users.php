<?php
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is set
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the ID from the query string

    // Prepare and execute select statement
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $users = $result->fetch_assoc();
    } else {
        echo "No teacher found";
        exit();
    }

    $stmt->close();
} else {
    echo "No ID provided";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
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
        <h2 class="text-center text-white">View <?php echo htmlspecialchars($users['first_name']); ?>'s Information</h2>
        <form class="form-container">
            <div class="mb-3">
                <label for="first_name" class="form-label">first_name</label>
                <input type="text" class="form-control border-2 border-black" id="first_name" value="<?php echo htmlspecialchars($users['first_name']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">last_name</label>
                <input type="last_name" class="form-control border-2 border-black" id="last_name" value="<?php echo htmlspecialchars($users['last_name']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">email</label>
                <input type="text" class="form-control border-2 border-black" id="email" value="<?php echo htmlspecialchars($users['email']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="lrn" class="form-label">lrn</label>
                <input type="text" class="form-control border-2 border-black" id="lrn" value="<?php echo htmlspecialchars($users['lrn']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="parent_name" class="form-label">parent name</label>
                <input type="text" class="form-control border-2 border-black" id="parent_name" value="<?php echo htmlspecialchars($users['parent_name']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="parent_email" class="form-label">parent email</label>
                <input type="text" class="form-control border-2 border-black" id="parent_email" value="<?php echo htmlspecialchars($users['parent_email']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="parent_contact" class="form-label">parent contact</label>
                <input type="text" class="form-control border-2 border-black" id="parent_contact" value="<?php echo htmlspecialchars($users['parent_contact']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="section" class="form-label">Section</label>
                <input type="text" class="form-control border-2 border-black" id="section" placeholder="To Be Announced" value="<?php echo htmlspecialchars($users['section']); ?>" readonly>
            </div>
            <a href="users-admin.php" class="btn btn-primary">Back</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
