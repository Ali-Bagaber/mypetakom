<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Delete the user
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header("Location: Admin-ManageUserProfiles.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
