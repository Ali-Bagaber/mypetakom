<?php
include('db_connect.php');

// Fetch only upcoming events
$sql = "SELECT * FROM events WHERE start_date >= CURDATE() ORDER BY start_date ASC";
$result = $conn->query($sql);

// Fetch recent committees and join with events to show event name
$sql_committees = "SELECT ec.committee_id, ec.role, e.title AS event_title, u.name AS student_name
                   FROM eventcommittee ec
                   JOIN events e ON ec.event_id = e.event_id
                   JOIN users u ON ec.user_id = u.user_id
                   ORDER BY ec.committee_id DESC 
                   LIMIT 5";


$result_committees = $conn->query($sql_committees);

// Total events
$sql_total_events = "SELECT COUNT(*) AS total FROM events";
$total_events = $conn->query($sql_total_events)->fetch_assoc()['total'];

// Active events
$sql_active_events = "SELECT COUNT(*) AS total FROM events WHERE event_status = 'Active'";
$active_events = $conn->query($sql_active_events)->fetch_assoc()['total'];

// Postponed/Cancelled events
$sql_postponed_events = "SELECT COUNT(*) AS total FROM events WHERE event_status IN ('Postponed', 'Cancelled')";
$postponed_events = $conn->query($sql_postponed_events)->fetch_assoc()['total'];

// Total committees
$sql_total_committees = "SELECT COUNT(*) AS total FROM eventcommittee";
$total_committees = $conn->query($sql_total_committees)->fetch_assoc()['total'];

// Merit Applications
$sql_merit_applications = "SELECT COUNT(*) AS total FROM meritapplication";
$merit_applications = $conn->query($sql_merit_applications)->fetch_assoc()['total'];


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Advisor Dashboard</title>
  <link rel="stylesheet" href="styleadvisor.css">
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logos">
        <img src="ump logo.png" alt="UMP Logo">
        <img src="petakom logo.png" alt="PETAKOM Logo">
      </div>
      <h2>Event Advisor Profile</h2>
      <div class="profile-pic"></div>
      <nav>
        <ul>
		  <li><a href="dashboard_advisor.php" class="active">Dashboard</a></li>
		  <li>Manage User Profile</li>
		  <li><a href="create_event.php">Create New Event</a></li>
		  <li><a href="create_committee.php">Register Commitee </a></li>
		  <li><a href="manage_event.php">Manage Events</a></li>
		  <li><a href="manage_committee.php">Manage Committees</a></li>
		  <li><a href="merit_approval.php">Merit Application Approval</a></li>
		  <li><a href="event_qr.php">Event QR Code</a></li>
		</ul>

      </nav>
    </aside>

    <main class="main-content">
      <header class="top-header">
        <h1>MyPetakom System</h1>
        <button class="logout">Log Out</button>
      </header>

      <section class="dashboard-header">
        <h2>Event Advisor Dashboard</h2>
        <button class="download-btn">Download Report</button>
      </section>

      <section class="stats">
		  <div class="card">Total Events<br><span><?php echo $total_events; ?></span></div>
		  <div class="card">Active Events<br><span><?php echo $active_events; ?></span></div>
		  <div class="card">Postponed/Cancelled<br><span><?php echo $postponed_events; ?></span></div>
		  <div class="card">Total Committees<br><span><?php echo $total_committees; ?></span></div>
		  <div class="card">Merit Application<br><span><?php echo $merit_applications; ?></span></div>
	  </section>


      <section class="content">
        <div class="charts">
          <h3>Charts/Graphs</h3>
          <div class="chart-box">[Event by Status]</div>
          <div class="chart-box">[Merit Application Status]</div>
          <div class="chart-box">[Event per Month]</div>
        </div>

        <div class="short-list">
          <h3>Short List</h3>
          <div class="table-wrapper">
            <!-- Upcoming Events Section -->
			<h4>Upcoming Events</h4>
				<table>
					<thead>
						<tr>
							<th>No</th>
							<th>Event Name</th>
							<th>Status</th>
							<th>Start Date</th>
							<th>Location</th>
							
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
								echo "<td class='" . strtolower($row['event_status']) . "-status'>" . htmlspecialchars($row['event_status']) . "</td>";
								echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
								echo "<td>" . htmlspecialchars($row['location']) . "</td>";
								
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='8'>No upcoming events found.</td></tr>";
						}
						?>
					</tbody>
				</table>

			<!-- Committee List Section -->
			<div class="table-wrapper">
			<h4>Committee List</h4>
			<table>
				<thead>
					<tr>
						<th>No</th>
						<th>Name</th>
						<th>Role</th>
						<th>Event</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($result_committees && $result_committees->num_rows > 0) {
						$no = 1;
						while ($row = $result_committees->fetch_assoc()) {
							echo "<tr>";
							echo "<td>" . $no++ . "</td>";
							echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
							echo "<td>" . htmlspecialchars($row['role']) . "</td>";
							echo "<td>" . htmlspecialchars($row['event_title']) . "</td>";
							echo "</tr>";
						}
					} else {
						echo "<tr><td colspan='4'>No committee data found.</td></tr>";
					}
					?>
				</tbody>
			</table>


          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
