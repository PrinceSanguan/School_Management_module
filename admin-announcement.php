<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Announcements</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
            background-size: cover;
            color:white;
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
                    <a class="nav-link active" href="admin-announcement.php">Admin Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view-assignments.php">View Assignments</a>
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
        <h2 class="mb-4">Admin Announcements</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Announcement 1</th>
                    <th>Announcement 2</th>
                    <th>Announcement 3</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                include 'db_connect.php';

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // SQL query to fetch admin announcements
                $sql = "SELECT title, a1, a2, a3 FROM teacher_announcement";
                $result = $conn->query($sql);

                // Check if there are results
                if ($result->num_rows > 0) {
                    // Output data for each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["title"]) . "</td>
                                <td>" . htmlspecialchars($row["a1"]) . "</td>
                                <td>" . htmlspecialchars($row["a2"]) . "</td>
                                <td>" . htmlspecialchars($row["a3"]) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No announcements found.</td></tr>";
                }

                // Close connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
