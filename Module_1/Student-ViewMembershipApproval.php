<?php
include('db_connect.php');
session_start();

// Use logged-in user_id from session
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id == 0) {
    // Not logged in - redirect to login page or show error
    header("Location: login.php");
    exit();
}

// Fetch latest membership and student info for this user
$sql = "SELECT s.student_id, s.student_name, s.program, s.semester, s.faculty, s.student_id_card, m.join_date, m.status
        FROM student s 
        JOIN membership m ON s.user_id = m.user_id 
        WHERE s.user_id = ? 
        ORDER BY m.join_date DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Petakom Membership Status</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .preview-img {
      max-height: 200px;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .student-details {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
      margin-top: 20px;
      width: 80%;         /* Wider box */
      max-width: 900px;   /* Prevent too wide on large screens */
      margin-left: auto;
      margin-right: auto;
    }

    .info-item {
      margin-bottom: 10px;
      font-size: 16px;
    }

    .info-item strong {
      width: 180px;
      display: inline-block;
    }

    .header, .sidebar, .dashboard {
      /* Add any necessary styling here */
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="logo-section">
      <img src="Logo1.png" alt="UMP Logo" class="logo" />
      <img src="Logo2.png" alt="Petakom Logo" class="logo" />
    </div>
    <h1 class="white-text" style="color: white;">Student</h1>
	<a href="#" id="logoutButton" class="logout-button">Log Out</a>
  </div>

  <div class="main-container">
    <div class="sidebar">
      <div class="profile">
        <h3>Student Profile</h3>
        <img src="profileIcon.png" alt="Student Profile" class="profile-img" />
      </div>
      <hr />
      <ul class="menu">
        <li><a href="Admin-CreateUserAccount.php">Dashboard</a></li>
        <hr />
        <li><a href="Admin-CreateUserAccount.php">Manage User Profile</a></li>
        <hr />
        <li><a href="Student-MembershipApplication.php">Manage Membership</a></li>
        <hr />
		<li class="active">View Membership</li>
        <hr />
        <li><a href="Admin-ManageUserProfiles.php">View Awarded Merits</a></li>
        <hr />
        <li><a href="Admin-ManageUserProfiles.php">Manage Merits Claims</a></li>
        <hr />
      </ul>
    </div>

    <div class="dashboard">
      <div class="container">
        <h2>Petakom Membership Status</h2>

        <?php if ($student): ?>
          <div class="student-details">
            <h3>Student Details</h3>
            <div class="info-item"><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></div>
            <div class="info-item"><strong>Name:</strong> <?= htmlspecialchars($student['student_name']) ?></div>
            <div class="info-item"><strong>Program:</strong> <?= htmlspecialchars($student['program']) ?></div>
            <div class="info-item"><strong>Semester:</strong> <?= htmlspecialchars($student['semester']) ?></div>
            <div class="info-item"><strong>Faculty:</strong> <?= htmlspecialchars($student['faculty']) ?></div>
            <div class="info-item">
              <strong>Student Card:</strong>
              <?php if ($student['student_id_card']): ?>
                <a href="<?= htmlspecialchars($student['student_id_card']) ?>" target="_blank">View Uploaded Card</a>
              <?php else: ?>
                No card uploaded.
              <?php endif; ?>
            </div>

            <h3 style="margin-top: 30px;">Membership Information</h3>
            <div class="info-item"><strong>Join Date:</strong> <?= htmlspecialchars($student['join_date']) ?></div>
            <div class="info-item"><strong>Status:</strong> <?= htmlspecialchars(ucfirst($student['status'])) ?></div>
          </div>
        <?php else: ?>
          <p>No membership record found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
<script>
	  document.getElementById('logoutButton').addEventListener('click', function(event) {
		event.preventDefault(); // Prevent the default anchor behavior

		const confirmLogout = confirm("Are you sure you want to log out?");
		if (confirmLogout) {
		  // Redirect to login page
		  window.location.href = 'login.php'; // Replace with your actual login page
		}
	  });
  </script>
</body>
</html>
