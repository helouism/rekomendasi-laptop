<?php
$server = "localhost";
$user = "root";
$pass = "";
$database = "db_laptop";
$conn = mysqli_connect($server, $user, $pass, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>