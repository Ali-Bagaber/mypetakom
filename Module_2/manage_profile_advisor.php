<?php
session_start();
include('db_connect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = 'Login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Optional: check if user is advisor
$sql = "SELECT * FROM eventadvisor WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Redirect directly to edit profile page for this advisor
    header("Location: edit_user_profile.php?id=" . $user_id);
    exit();
} else {
    echo "<script>alert('Advisor profile not found.'); window.location.href = 'dashboard_advisor.php';</script>";
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage User Profile</title>
    <link rel="stylesheet" href="styleadvisor.css">
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logos">
            <img src="ump logo.png" alt="UMP Logo">
            <img src="petakom logo.png" alt="Petakom Logo">
        </div>
        <h2>Advisor Profile</h2>
        <div class="profile-pic"></div>
        <ul>
            <li><a href="dashboard_advisor.php">Dashboard</a></li>
            <li><a href="manage_profile_advisor.php" class="active">Manage User Profile</a></li>
            <li><a href="create_event.php">Create New Event</a></li>
            <li><a href="create_committee.php">Register Committee</a></li>
            <li><a href="manage_event.php">Manage Events</a></li>
            <li><a href="manage_committee.php">Manage Committees</a></li>
            <li><a href="merit_approval.php">Merit Application Approval</a></li>
            <li><a href="event_qr.php">Event QR Code</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-header">
            <h1>MyPetakom System</h1>
            <button class="logout">Log Out</button>
        </div>

        <h2>Manage User Profile</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Advisor Name</th>
                        <th>Phone Number</th>
                        <th>Position</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['advisor_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['admin_phone_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['position_advisor']) . "</td>";
                            echo "<td>
                                <a href='edit_user_profile.php?id=" . $row['user_id'] . "'><button>Edit</button></a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No advisor profiles found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
