<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View User - Petakom Coordinator</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="header">
    <div class="logo-section">
      <img src="Logo1.png" alt="UMP Logo" class="logo">
      <img src="Logo2.png" alt="Petakom Logo" class="logo">
    </div>
	<h1 class="white-text" style="color: white;">Petakom Coordinator (Administrator)</h1>
    <a href="logout.php" id="logoutButton" class="logout-button">Log Out</a>
  </div>

  <div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar" style="border: 1px solid #000; padding: 10px;">
      <div class="profile">
        <h3>Admin Profile</h3>
        <img src="profileIcon.png" alt="Admin Profile" class="profile-img" />
      </div>
      <hr>
      <ul class="menu" style="list-style-type: none; padding: 0;">
        <li><a href="Admin-Dashboard.php">Dashboard</a></li>
        <hr>
        <li><a href="Admin-CreateUserAccount.php">Create User Account</a></li>
        <hr>
        <li><a href="Admin-ManageUserProfiles.php">Manage User Profiles</a></li>
        <hr>
        <li><a href="Admin-ManageMembership.php">Manage Membership</a></li>
      </ul>
    </div>

    <!-- Main Content -->
	<div class="content" style="flex-grow: 1; padding: 20px; background-color: #ffffff; border-radius: 8px; margin: 20px;">
		<h2>User Details</h2>
		<?php if ($user): ?>
			<p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
			<p><strong>Full Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
			<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
			<p><strong>Role:</strong> <?= htmlspecialchars($user['user_role']) ?></p>
			<br>
			<a href="Admin-ManageUserProfiles.php" class="btn">Back to User List</a>
		<?php else: ?>
			<p>User not found.</p>
		<?php endif; ?>
	</div>
  </div>
</body>
</html>
