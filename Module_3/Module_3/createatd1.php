<?php
session_start();
include('C:/xampp/htdocs/mypetakom-main/mypetakom-main/Databased/db_connect.php');

// Feedback message variables
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_name = trim($_POST['attendance_name']);
    $description     = trim($_POST['description']);
    $date            = trim($_POST['date']);
    $start_time      = trim($_POST['start_time']);
    $end_time        = trim($_POST['end_time']);
    $venue           = trim($_POST['venue']);

    // Basic validation
    if (!$attendance_name || !$date || !$start_time || !$end_time || !$venue) {
        $error = "Attendance Name, Date, Start Time, End Time, and Venue are required.";
    } else {
        $sql = "INSERT INTO attendance_forms (attendance_name, description, date, start_time, end_time, venue)
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $attendance_name, $description, $date, $start_time, $end_time, $venue);


        if ($stmt->execute()) {
            $success = "Attendance form created successfully.";
        } else {
            $error = "Failed to create attendance form. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Attendance Form</title>
  <link rel="stylesheet" href="createatd1.css">
</head>
<body>

<div class="layout-container">
  <div class="main-header">
    <div class="logo-header">
      <img src="./image/logo-emblem__329x482.png" alt="UMPSA" />
      <img src="./image/images.png" alt="MyPetakom Logo">
    </div>
    <h1 class="main-title">MyPetakom System</h1>
    <div class="nav-actions">
      <a class="btn-logout" href="../../logout.php">Log Out</a>
    </div>
  </div>

  <div class="wrapper">
    <aside class="sidebar">
      <div class="profile-image-container">
        <img src="./image/user-icon-on-transparent-background-free-png.webp" class="profile-image" alt="Profile Picture">
      </div>
      <div class="section-title">Advisor Profile</div>
      <ul class="sidebar-menu">
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Manage User Profile</a></li>
        <li class="active"><a href="createatd1.php">Create Attendance</a></li>
        <li><a href="manageatd.php">Manage Attendance</a></li>
        <li><a href="viewatd.php">View Attendance</a></li>
      </ul>
    </aside>

    <main class="main-content">
      <div class="form-box">
        <span class="save-draft">Save as Draft</span>
        <h2>Create Attendance Form</h2>

        <?php if ($success): ?>
          <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-row">
            <label for="attendance_name">Attendance Name :</label>
            <input type="text" id="attendance_name" name="attendance_name" required />
          </div>

          <div class="form-row">
            <label for="description">Event Name :</label>
            <input type="text" id="description" name="description" />
          </div>

          <div class="form-row">
            <label for="date">Date :</label>
            <input type="date" id="date" name="date" required />
          </div>

          <div class="form-row">
            <label for="start_time">Start Time :</label>
            <input type="time" id="start_time" name="start_time" required />
        </div>

        <div class="form-row">
            <label for="end_time">End Time :</label>
            <input type="time" id="end_time" name="end_time" required />
        </div>

          <div class="form-row">
            <label for="venue">Venue :</label>
            <input type="text" id="venue" name="venue" required />
          </div>

          <div class="form-buttons">
            <button type="reset" class="btn-cancel">Cancel</button>
            <button type="submit" class="btn-submit">Submit</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</div>

</body>
</html>
