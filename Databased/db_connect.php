<?php
$host = "localhost";
$username = "admin";
$password = "1234";
$database = "mypetakom"; 

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
