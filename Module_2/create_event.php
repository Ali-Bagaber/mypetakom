<?php
include '../../Databased/db_connect.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $geolocation = $_POST['geolocation'];
    $location = $_POST['location'];
    $merit = $_POST['merit'];
    $merit_application = ($merit === 'Apply Merit') ? 'applied' : 'not applied';

    $approval_letter = '';
    if (isset($_FILES['approval_letter']) && $_FILES['approval_letter']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES["approval_letter"]["name"]);
        $target_file = $upload_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES["approval_letter"]["tmp_name"], $target_file)) {
            $approval_letter = $target_file;
        }
    }

    $created_by = 1;
    $event_status = isset($_POST['save_draft']) ? 'Draft' : 'Pending Approval';
    $event_level = 'Faculty';
    $qrcode_event = '';

    $sql = "INSERT INTO events 
        (created_by, qrcode_event, title, description, start_date, end_date, event_status, geographic_location, location, approval_letter, event_level, merit_application)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isssssssssss", 
        $created_by, 
        $qrcode_event, 
        $event_name, 
        $description, 
        $start_date, 
        $end_date, 
        $event_status, 
        $geolocation, 
        $location, 
        $approval_letter, 
        $event_level,
        $merit_application
    );

    if ($stmt->execute()) {
        $event_id = $conn->insert_id;

        if ($merit === "Apply Merit") {
            $user_id = $created_by;
            $claim_status = 'pending';

            $sql_merit = "INSERT INTO meritapplication (user_id, event_id, claim_status) VALUES (?, ?, ?)";
            $stmt_merit = $conn->prepare($sql_merit);

            if (!$stmt_merit) {
                die("SQL Error (meritapplication): " . $conn->error);
            }

            $stmt_merit->bind_param("iis", $user_id, $event_id, $claim_status);

            if ($stmt_merit->execute()) {
                echo "<script>alert('Event and Merit Application submitted successfully!'); window.location='create_event.php';</script>";
            } else {
                echo "Merit application error: " . $stmt_merit->error;
            }

            $stmt_merit->close();
        } else {
           $message = ($event_status === 'Draft') 
		  ? 'Event saved as draft.' 
		  : 'Event created successfully without merit.';
		echo "<script>alert('$message'); window.location='create_event.php';</script>";

        }
    } else {
        echo "Event creation error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Event</title>
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
          <li><a href="dashboard_advisor.php">Dashboard</a></li>
          <li>Manage User Profile</li>
          <li><a href="create_event.php" class="active">Create New Event</a></li>
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
        <button class="logout" onclick="window.location.href='logout.php'">Log Out</button>
      </header>

      <section class="dashboard-header">
        <h2>Create New Event Form</h2>
      </section>

      <form method="POST" enctype="multipart/form-data" style="background:#ccc; padding:20px; border-radius:10px;">
        <div style="display:flex; gap:20px;">
          <div style="flex:1;">
            <label>Event Name:</label><br>
            <input type="text" name="event_name" required><br><br>

            <label>Description:</label><br>
            <input type="text" name="description" required><br><br>

            <label>Start Date:</label><br>
            <input type="date" name="start_date" required><br><br>

            <label>End Date:</label><br>
            <input type="date" name="end_date" required><br><br>
          </div>

          <div style="flex:1;">
            <label>Location:</label><br>
            <input type="text" name="location" required><br><br>

            <label>Geolocation:</label><br>
            <input type="text" name="geolocation" placeholder="eg. 3.1234,101.6789"><br><br>

            <label>Approval Letter (PDF):</label><br>
            <input type="file" name="approval_letter" accept="application/pdf"><br><br>

            <label>Merit Application for Event:</label><br>
            <input type="radio" name="merit" value="Apply Merit"> Apply Merit
            <input type="radio" name="merit" value="No" checked> No<br><br>
          </div>
        </div>

        <div style="text-align:right;">
          <button type="submit" name="save_draft" class="logout" style="background-color:orange;">Save as Draft</button>
          <button type="submit" name="submit" class="logout" style="background-color:green;">Submit</button>
          <button type="reset" class="logout" style="background-color:gray;">Cancel</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
