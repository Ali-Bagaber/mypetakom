<?php

include('db_connect.php');


if (isset($_POST['submit'])) {
    $student_id_card = $_POST['student_id_card'];
    $role = $_POST['role'];
    $event_title = $_POST['title'];  // corrected $_POST (was $_post)

    // Get event_id from events table using title
    $event_lookup = mysqli_query($conn, "SELECT event_id FROM events WHERE title = '$event_title'");
    
    if ($event_lookup && mysqli_num_rows($event_lookup) > 0) {
        $event_data = mysqli_fetch_assoc($event_lookup);
        $event_id = $event_data['event_id'];

        // Get user_id from student table using student_id_card
        $student_query = mysqli_query($conn, "SELECT user_id FROM student WHERE student_id_card = '$student_id_card'");
        
        if ($student_query && mysqli_num_rows($student_query) > 0) {
            $student = mysqli_fetch_assoc($student_query);
            $user_id = $student['user_id'];

            // Check if already assigned
            $check = mysqli_query($conn, "SELECT * FROM eventcommittee WHERE user_id = '$user_id' AND event_id = '$event_id'");
            if (mysqli_num_rows($check) > 0) {
                echo "<script>alert('This student is already assigned to this event.');</script>";
            } else {
                $insert = "INSERT INTO eventcommittee (event_id, user_id, role) VALUES ('$event_id', '$user_id', '$role')";
                if (mysqli_query($conn, $insert)) {
                    echo "<script>alert('Committee member added successfully.');</script>";
                } else {
                    echo "<script>alert('Insert failed: " . mysqli_error($conn) . "');</script>";
                }
            }
        } else {
            echo "<script>alert('Student not found.');</script>";
        }
    } else {
        echo "<script>alert('Event not found.');</script>";
    }
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Committee</title>
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
				 <li>Manage User Profile</li>
				 <li><a href="create_event.php">Create New Event</a></li>
				 <li><a href="create_committee.php" class="active" >Register Commitee </a></li>
				 <li><a href="manage_event.php">Manage Events</a></li>
				 <li><a href="manage_committee.php">Manage Committees</a></li>
				 <li><a href="merit_approval.php">Merit Application Approval</a></li>
				 <li><a href="event_qr.php">Event QR Code</a></li>
            </ul>
        </div>

        <!-- Main Content -->
		<main class="main-content">
			<header class="top-header">
				<h1>MyPetakom System</h1>
				<button class="logout" onclick="window.location.href='logout.php'">Log Out</button>
			</header>

            <section class="dashboard-header">
                <h2>Commitee Registration Form</h2>
            </section>

            <form method="POST" enctype="multipart/form-data" style="background:#ccc; padding:20px; border-radius:10px;">
				<div style="display:flex; gap:20px;">
					<div style="flex:1;">
						
				
						<label for="student-id">Student ID:</label><br>
						<input type="text" id="student-id" name="student_id_card" required><br><br>

						<label for="student-name">Student Name:</label><br>
						<input type="text" id="student-name" name="student_name" required><br><br>

						
					</div>
				
					<div style="flex:1;">
				 
						<label for="assigned-position">Assigned Position:</label><br>
						<input type="text" id="role" name="role" required><br><br>
						
						<label for="event">Select Event:</label><br>
							<select id="event" name="title" required><br><br>
								<option value="">-- Select an Event --</option>
								<?php
								$event_query = "SELECT title FROM events";
								$event_result = mysqli_query($conn, $event_query);
								while ($event = mysqli_fetch_assoc($event_result)) {
									echo "<option value='" . htmlspecialchars($event['title']) . "'>" . htmlspecialchars($event['title']) . "</option>";
								}
								?>
							</select>
					</div>
				</div>
				
						<div style="text-align:right;">
		
						<button type="submit" name="submit" class="logout" style="background-color:green;">Submit</button>
					</form>

                </div>
            </div>
        </div>
    </div>
</body>
</html>