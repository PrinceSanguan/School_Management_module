<?php
include 'db_connect.php'; 

// Query to fetch the data
$sql = "SELECT * FROM recent_activities"; 

// Execute the query
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Activities</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
            color: white;
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
                    <a class="nav-link" href="view-submission.php">View Submissions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="recent-activities.php">Recent Activities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        <h2>Recent Activity Announcement</h2>
        <form id="activityForm" action="recent-activities-post.php" method="post">
        <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control border-2 border-black" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="r1">Recent Activity 1</label>
                <input type="text" class="form-control border-2 border-black" id="r1" name="r1" required>
            </div>
            <div class="form-group">
                <label for="r2">Recent Activity 2</label>
                <input type="text" class="form-control border-2 border-black" id="r2" name="r2" required>
            </div>
            <div class="form-group">
                <label for="r3">Recent Activity 3</label>
                <input type="text" class="form-control border-2 border-black" id="r3" name="r3" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Create</button>
        </form>

        <h2 class="mt-5">Recent Activities List</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Activity 1</th>
                    <th>Activity 2</th>
                    <th>Activity 3</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Use the fetched results to populate the table
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["r1"]) . "</td>
                                <td>" . htmlspecialchars($row["r2"]) . "</td>
                                <td>" . htmlspecialchars($row["r3"]) . "</td>
                                <td>
                                    <form class='delete-form' method='post' action='delete-recent.php'>
                                        <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No recent activities found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        // Handle status message from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: 'Activity added successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else if (status === 'error') {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to add activity.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else if (status === 'deleted') {
            Swal.fire({
                title: 'Success!',
                text: 'Activity deleted successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else if (status === 'exist') {
            Swal.fire({
                title: 'Oops!',
                text: 'You can only add one recent activities at a time. Please delete the existing one before creating a new one.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }

        // Handle form submission for deletion
        $('.delete-form').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            var form = $(this);
            var id = form.find('input[name="id"]').val();

            // Confirm deletion with SweetAlert2
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to recover this activity!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    form.off('submit').submit();
                }
            });
        });
    });
    </script>
</body>
</html>
