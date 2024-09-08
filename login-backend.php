<?php
session_start();
include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $user_type = $conn->real_escape_string(trim($_POST['user_type']));

    if (empty($email) || empty($password) || empty($user_type)) {
        $error = "All fields are required.";
    } else {
        switch ($user_type) {
            case 'student':
                $table = 'users';
                $password_column = 'password';
                $redirect_page = 'main.php';
                break;
            case 'admin':
                $table = 'admins';
                $password_column = 'password';
                $redirect_page = 'users-admin.php';
                break;
            case 'teacher':
                $table = 'teachers';
                $password_column = 'password'; 
                $redirect_page = 'teacher-main.php';
                break;
            default:
                $error = "Invalid user type.";
                break;
        }

        if (empty($error)) {
            $sql = "SELECT * FROM $table WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user[$password_column])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['show_alert'] = 'success'; // Set session variable for alert
                    header("Location: $redirect_page");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No user found with this email.";
            }

            $stmt->close();
        }
    }

    $_SESSION['login_error'] = $error;
    $_SESSION['login_email'] = $email;
    $_SESSION['show_alert'] = 'error'; // Set session variable for alert

    header("Location: login.php");
    exit();
}

$conn->close();
?>
