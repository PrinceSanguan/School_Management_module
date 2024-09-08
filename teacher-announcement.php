<?php
include 'db_connect.php'; 

// Query to fetch the data
$sql = "SELECT * FROM teacher_announcement"; 

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
    <title>Teacher Announcement</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
    body {
        padding-top: 56px; /* Adjust padding to avoid content being hidden under the navbar */
        font-family: "New Amsterdam", sans-serif;
        background-color: #0D1B2A;
        color: white;
    }
</style>

<body>
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
                    <a class="nav-link active" href="teacher-announcement.php">Teacher Announcement</a>
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
                    <a class="nav-link" href="teachers-admin.php">Teachers List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Create Teacher Announcement</h2>
        <form id="announcementForm" action="teacher-announcement-post.php" method="post">
            <div class="form-group">
                <label for="title">Announcement Title</label>
                <input type="text" class="form-control border-2 border-black" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="a1">Announcement 1</label>
                <input type="text" class="form-control border-2 border-black" id="a1" name="a1" required>
            </div>
            <div class="form-group">
                <label for="a2">Announcement 2</label>
                <input type="text" class="form-control border-2 border-black" id="a2" name="a2" required>
            </div>
            <div class="form-group">
                <label for="a3">Announcement 3</label>
                <input type="text" class="form-control border-2 border-black" id="a3" name="a3" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Create</button>
        </form>

        <h2 class="mt-5">Announcements List</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Announcement 1</th>
                    <th>Announcement 2</th>
                    <th>Announcement 3</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Use the fetched results to populate the table
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["title"]) . "</td>
                                <td>" . htmlspecialchars($row["a1"]) . "</td>
                                <td>" . htmlspecialchars($row["a2"]) . "</td>
                                <td>" . htmlspecialchars($row["a3"]) . "</td>
                                <td>
                                    <form class='delete-form' method='post' action='delete-announcement-teacher.php'>
                                        <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No announcements found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        // Handle status message from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: 'Announcement added successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else if (status === 'error') {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to add announcement.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else if (status === 'deleted') {
            Swal.fire({
                title: 'Success!',
                text: 'Announcement deleted successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else if (status === 'exists') {
            Swal.fire({
                title: 'Oops!',
                text: 'You can only add one announcement at a time. Please delete the existing one before creating a new one.',
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
                text: "You won't be able to recover this announcement!",
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
