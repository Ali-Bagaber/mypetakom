<?php
session_start();
include('C:/xampp/htdocs/mypetakom-main/mypetakom-main/Databased/db_connect.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $id = $_POST['id'];
        $attendance_name = $_POST['attendance_name'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $venue = $_POST['venue'];

        $stmt = $conn->prepare("UPDATE attendance_forms SET attendance_name=?, description=?, date=?, start_time=?, end_time=?, venue=? WHERE id=?");
        $stmt->bind_param("ssssssi", $attendance_name, $description, $date, $start_time, $end_time, $venue, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM attendance_forms WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch attendance records
$sql = "SELECT * FROM attendance_forms ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Attendance</title>
  <link rel="stylesheet" href="manageatd1.css">
</head>
<body>

<div class="layout-container">
  <!-- Header -->
  <div class="main-header">
    <div class="logo-header">
      <img src="./image/logo-emblem__329x482.png" alt="UMPSA" />
      <img src="./image/images.png" alt="MyPetakom Logo">
    </div>
    <h1 class="main-title">MyPetakom System</h1>
    <div class="nav-actions">
      <a class="btn-logout" href="../../logout.php">Logout</a>
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

    <div class="main-content1">
      <h2 class="attendance-title">Manage Attendance</h2>
      <form method="POST">
        <table class="attendance-table">
          <thead>
            <tr>
              <th>Attendance Name</th>
              <th>Description</th>
              <th>Date</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Venue</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                <form method="POST">
                    <td><input type="text" name="attendance_name" value="<?= htmlspecialchars($row['attendance_name']) ?>"></td>
                    <td><input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>"></td>
                    <td><input type="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>"></td>
                    <td><input type="time" name="start_time" value="<?= htmlspecialchars($row['start_time']) ?>"></td>
                    <td><input type="time" name="end_time" value="<?= htmlspecialchars($row['end_time']) ?>"></td>
                    <td><input type="text" name="venue" value="<?= htmlspecialchars($row['venue']) ?>"></td>
                    <td class="update-buttons">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="save" class="save-btn">Save</button>
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                    </td>
                </form>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
      </form>
    </div>
  </div>
</div>

</body>
</html>
