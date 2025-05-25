<?php
include('db_connect.php');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
  $role = $_POST['role'];

  // Insert into database including full name
  $sql = "INSERT INTO users (name, username, email, password, user_role) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $fullname, $username, $email, $password, $role);

  if ($stmt->execute()) {
    echo "<script>alert('User account created successfully.');</script>";
  } else {
    echo "<script>alert('Error: " . $stmt->error . "');</script>";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Petakom Coordinator (Administrator)</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="header">
    <div class="logo-section">
      <img src="Logo1.png" alt="UMP Logo" class="logo">
      <img src="Logo2.png" alt="Petakom Logo" class="logo">
    </div>
    <h1 class="white-text">Petakom Coordinator (Administrator)</h1>
	<a href="#" id="logoutButton" class="logout-button">Log Out</a>
  </div>

  <div class="main-container">
    <div class="sidebar" style="border: 1px solid #000; padding: 10px; border-radius: 0;">
      <div class="profile">
        <h3>Admin Profile</h3>
        <img src="profileIcon.png" alt="Admin Profile" class="profile-img" />
      </div>
      <hr style="margin: 10px 0; border: 0; border-top: 1px solid #000;">
      <ul class="menu" style="list-style-type: none; padding: 0; margin: 0;">
        <li><a href="Admin-Dashboard.php">Dashboard</a></li>
        <hr>
        <li class="active">Create User Account</li>
        <hr>
        <li><a href="Admin-ManageUserProfiles.php">Manage User Profiles</a></li>
        <hr>
        <li><a href="Admin-ManageMembership.php">Manage Membership</a></li>
      </ul>
    </div>

    <div class="admin-container">
      <h1>Create User Account</h1>
      <form class="user-form" method="post" autocomplete="off">
        <div class="form-group">
          <label for="fullname">Full Name</label>
          <input type="text" id="fullname" name="fullname" required>
        </div>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="new-password" required>
        </div>
        <div class="form-group">
          <label for="role">User Role</label>
          <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Petakom Coordinator (Administrator)</option>
            <option value="advisor">Event Advisor</option>
            <option value="student">Student</option>
          </select>
        </div>
        <div class="form-buttons">
          <button type="submit" class="btn btn-primary">Create Account</button>
          <button type="reset" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
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
