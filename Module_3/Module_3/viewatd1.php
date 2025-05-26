<?php
session_start();
include('C:/xampp/htdocs/mypetakom-main/mypetakom-main/Databased/db_connect.php');

// Fetch all attendance records
$sql = "SELECT id, attendance_name, description, date, start_time, venue FROM attendance_forms ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Attendance</title>
  <link rel="stylesheet" href="viewatd1.css">
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
<div class="main-content">
  <section class="attendance-section">
    <h2 class="attendance-title">Attendance Records</h2>
    <div class="overflow-x-auto">
      <table class="attendance-table">
        <thead>
          <tr class="attendance-row">
            <th class="attendance-header">Name</th>
            <th class="attendance-header">Description</th>
            <th class="attendance-header">Date</th>
            <th class="attendance-header">Start Time</th>
            <th class="attendance-header">Venue</th>
            <th class="attendance-header text-center">QR Code</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="attendance-row">
              <td class="attendance-data"><?php echo htmlspecialchars($row['attendance_name']); ?></td>
              <td class="attendance-data"><?php echo htmlspecialchars($row['description']); ?></td>
              <td class="attendance-data"><?php echo htmlspecialchars($row['date']); ?></td>
              <td class="attendance-data"><?php echo htmlspecialchars($row['start_time']); ?></td>
              <td class="attendance-data"><?php echo htmlspecialchars($row['venue']); ?></td>
              <td class="attendance-data text-center">
                <a href="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode('attendance_id=' . $row['id']); ?>&amp;size=100x100" target="_blank">
                  <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode('attendance_id=' . $row['id']); ?>&amp;size=50x50" alt="QR Code" class="qr-icon">
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

</body>
</html>
