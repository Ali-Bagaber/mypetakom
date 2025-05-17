<?php
include 'Databased/db_connect.php';


$sql = "SELECT * FROM users"; // change based on your table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Connection successful and data found.";
} else {
    echo "Connected, but no data found.";
}
?>
