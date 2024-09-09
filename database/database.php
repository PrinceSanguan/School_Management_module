<?php
/* $servername = "localhost";
$username = "u498169718_compassED_user";
$password = "1Tge*Yf9";
$dbase = "u498169718_compassED"; */

$servername = "localhost"; // Since you're running it locally
$username = "root"; // Default username for local development
$password = ""; // By default, XAMPP or WAMP setups don't use a password for root
$dbase = "school_db"; // Replace with your local database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbase);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>