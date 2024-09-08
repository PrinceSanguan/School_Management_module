<?php
include 'db_connect.php';




if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $contact = filter_var($_POST['contact'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admins SET email = ?, password = ?, contact = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $email, $password, $contact, $address, $id);
    } else {
        $sql = "UPDATE admins SET email = ?, contact = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $email, $contact, $address, $id);
    }

    if ($stmt->execute()) {
        header('Location: update-admin.php?status=success');
    } else {
        header('Location: update-admin.php?status=error');
    }
    $stmt->close();
}


$id = 1;
$sql = "SELECT email, contact, address FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Details</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #0D1B2A;
            font-family: "New Amsterdam", sans-serif;
            color:white;
        }
        .form-container {
            border: 2px solid #000;
            border-radius: 0.375rem;
            padding: 2rem;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ms-5" href="#">School Management</a> 
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="update-admin.php">Admin</a>
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
                    <a class="nav-link" href="teachers-admin.php">Teachers List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Admin Profile</h2>
        <form action="update-admin.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border-dark border-2" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (Leave blank if not changing)</label>
                <input type="password" class="form-control border-dark border-2" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number</label>
                <input type="text" class="form-control border-dark border-2" id="contact" name="contact" value="<?php echo htmlspecialchars($admin['contact']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control border-dark border-2" id="address" name="address" rows="3" required><?php echo htmlspecialchars($admin['address']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="users-admin.php" class="btn btn-primary">Back</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Update Successful',
                text: 'Admin details have been updated successfully.',
                confirmButtonText: 'OK',
                didClose: () => {
                    window.location.href = 'users-admin.php';
                }
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: 'There was an error updating the admin details. Please try again.',
                confirmButtonText: 'OK'
            });
        }
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>
