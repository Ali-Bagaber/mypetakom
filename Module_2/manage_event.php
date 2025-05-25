<?php
include '../../Databased/db_connect.php';

// Optional: use session to get current user's ID if needed
// session_start();
// $user_id = $_SESSION['user_id'];

// Example SQL: show all events. Add WHERE clause if needed
$sql = "SELECT * FROM events";
// Example filtered query: WHERE created_by = '$user_id'

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleadvisor.css">
    <title>Manage Events</title>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div class="logos">
            <img src="ump logo.png" alt="UMP Logo">
            <img src="petakom logo.png" alt="Petakom Logo">
        </div>
        <h2>Advisor Profile</h2>
        <div class="profile-pic"></div>
        <ul>
            <li><a href="dashboard_advisor.php">Dashboard</a></li>
            <li>Manage User Profile</li>
            <li><a href="create_event.php">Create New Event</a></li>
            <li><a href="manage_event.php" class="active">Manage Events</a></li>
            <li><a href="manage_committee.php">Manage Committees</a></li>
            <li><a href="merit_approval.php">Merit Application Approval</a></li>
            <li><a href="event_qr.php">Event QR Code</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-header">
            <h1>MyPetakom System</h1>
            <button class="logout">Log Out</button>
        </div>

        <h2>Manage Events</h2>
		
        <div class="event-list">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Event Name</th>
						<th>Description</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Location</th>
                        <th>Geolocation</th>
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
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
							echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td class='" . strtolower($row['event_status']) . "-status'>" . htmlspecialchars($row['event_status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                            
                            echo "<td>" . htmlspecialchars($row['geographic_location']) . "</td>";
                            echo "<td>
								<a href='edit_event.php?id=" . $row['event_id'] . "' class='edit-btn'>Edit</a>
								<a href='delete_event.php?id=" . $row['event_id'] . "' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this event?');\">Delete</a>
							  </td>";


                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No events found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div style="text-align:right;">
            <button type="submit" name="submit" class="logout" style="background-color:green;">Submit</button>
        </div>
    </div>
</div>

</body>
</html>
