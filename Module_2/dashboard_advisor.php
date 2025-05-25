<?php
// advisor_dashboard.php
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
      <h2>Advisor Profile</h2>
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
        <div class="card">Total Events<br><span>10</span></div>
        <div class="card">Active Events<br><span>7</span></div>
        <div class="card">Postponed/Cancelled<br><span>3</span></div>
        <div class="card">Total Committees<br><span>25</span></div>
        <div class="card">Merit Application<br><span>5</span></div>
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
            <h4>Upcoming Events</h4>
            <table>
              <thead>
                <tr>
                  <th>Event Name</th><th>Date</th><th>Time</th><th>Location</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>FK Career Talk</td><td>10/05/2025</td><td>10:00 AM</td><td>DK1</td></tr>
                <tr><td>Hackathon 2025</td><td>12/05/2025</td><td>09:00 AM</td><td>Astaka FK</td></tr>
              </tbody>
            </table>

            <h4>Committee List</h4>
            <table>
              <thead>
                <tr>
                  <th>Student ID</th><th>Name</th><th>Position</th><th>Update</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>CB23048</td><td>Hatin Nazirah</td><td>Chairperson</td><td><a href="#">edit</a></td></tr>
                <tr><td>CB23088</td><td>Nur Adilah</td><td>Secretary</td><td><a href="#">edit</a></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
