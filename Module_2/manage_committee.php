<?php

include('db_connect.php');

$student_id_card = isset($_GET['student_id_card']) ? $_GET['student_id_card'] : null;
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;
$no = 1;
$user_id = null;

// Get user_id using student_id_card
if ($student_id_card) {
    $student_query = mysqli_query($conn, "SELECT user_id FROM student WHERE student_id_card = '$student_id_card'");
    if ($student_query && mysqli_num_rows($student_query) > 0) {
        $student_row = mysqli_fetch_assoc($student_query);
        $user_id = $student_row['user_id'];
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Committees</title>
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
				 <li><a href="manage_profile_advisor.php">Manage User Profile</a></li>
				 <li><a href="create_event.php">Create New Event</a></li>
				 <li><a href="create_committee.php">Register Commitee </a></li>
				 <li><a href="manage_event.php">Manage Events</a></li>
				 <li><a href="manage_committee.php"class="active">Manage Committees</a></li>
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


                <!-- Committees List -->
                <h2>Committees List</h2>
				
				<form method="get" action="">
					<br>
					<h3><label for="event_id">Select Event:</label></h3>
					</br>
					<select name="event_id" id="event_id" onchange="this.form.submit()">
						<option value="">-- Select Event --</option>
						<?php
						$events = mysqli_query($conn, "SELECT event_id, title FROM events");
						while ($event = mysqli_fetch_assoc($events)) {
							$selected = (isset($_GET['event_id']) && $_GET['event_id'] == $event['event_id']) ? "selected" : "";
							echo "<option value='{$event['event_id']}' $selected>{$event['title']}</option>";
						}
						?>
					</select>
				</form>

					
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Position</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                        <tbody>
							<?php
							
							$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;
							$no = 1;
							$result = mysqli_query($conn, "SELECT ec.committee_id, s.student_id_card, s.student_name, ec.role 
								FROM eventcommittee ec 
								JOIN student s ON ec.user_id = s.user_id 
								WHERE ec.event_id = '$event_id'");



								if (mysqli_num_rows($result) > 0) {
									while ($row = mysqli_fetch_assoc($result)) {
										echo "<tr>";
										echo "<td>" . $no++ . "</td>";
										echo "<td>" . $row['student_id_card'] . "</td>";
										echo "<td>" . $row['student_name'] . "</td>";
										echo "<td>" . $row['role'] . "</td>";
										echo "<td>
											<a href='edit_committee.php?id=" . $row['committee_id'] . "'><button>Edit</button>
											<a href='delete_committee.php?id=" . $row['committee_id'] . "'><button>Delete</button>
											</td>";
										echo "</tr>";
									}
								} else {
									echo "<tr><td colspan='5'>No committee members found for this event.</td></tr>";
								}
							?>
						</tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
