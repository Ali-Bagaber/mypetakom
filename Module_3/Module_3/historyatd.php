<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard – Attendance History</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="./templet.css">
  <link rel="stylesheet" href="./historyatd.css">
</head>
<body>

  <div class="layout-container">
    <!-- Top Navigation -->
    <div class="main-header">
      <div class="logo-header">
        <a href="./page.html" class="logo">
          <img src="./image/logo-emblem__329x482.png" alt=" UMPSA" />
        </a>
        <a href="./page.html" class="logo">
            <img src="./image/images.png" alt="MyPetakom Logo">
        </a>
      </div>
      <h1 class="main-title">MyPetakom System</h1>
      <div class="nav-actions">
        <div class="notification-icon">
          <i class="fas fa-bell"></i>
        </div>
        <a href="#" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>

    <!-- Page Wrapper (Sidebar + Content) -->
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="sidebar-wrapper">
          <div class="sidebar-content">
            <h4 class="section-title">(User) Profile</h4>
            <div class="profile-image-container">
              <img src="./image/user-icon-on-transparent-background-free-png.webp" alt="Profile Image" class="profile-image">
            </div>
            <ul class="sidebar-menu">
              <li class="menu-item ">
                <a href="#"><i class="fas fa-th-large"></i> Dashboard</a>
              </li>
              <li class="menu-item">
                <a href="#"><i class="fas fa-user-edit"></i> Manage User Profile</a>
              </li>
              <li class="menu-item ">
                <a href="#"><i class="fas fa-id-card"></i> Scan Attendance</a>
              </li>
              <li class="menu-item active">
                <a href="#"><i class="fas fa-id-card"></i> Attendance history</a>
              </li>
              <li class="menu-item">
                <a href="#"><i class="fas fa-id-card"></i> Manage Membership</a>
              </li>
              <li class="menu-item">
                <a href="#"><i class="fas fa-medal"></i> View Awarded Merits</a>
              </li>
              <li class="menu-item">
                <a href="#"><i class="fas fa-clipboard-check"></i> Manage Merits Claims</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="content">
        <section class="history-section">
          <div class="main-content">
          <h2 class="history-title">Attendance History</h2>
          <div class="table-wrapper">
            <table class="history-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Event Name</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Attendance Status</th>
                </tr>
              </thead>
              <tbody>
                <tr class="bg-white">
                  <td>1</td>
                  <td>PK Career Talk</td>
                  <td>10/03/2022</td>
                  <td>10/03/2022</td>
                  <td class="status-absent">Absent</td>
                </tr>
                <tr class="bg-gray">
                  <td>2</td>
                  <td>petakom 2022</td>
                  <td>24/06/2022</td>
                  <td>24/06/2022</td>
                  <td class="status-present">Present</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <p>&copy; 2025 MyPetakom System. All rights reserved. | UMP Student Dashboard</p>
    </footer>
  </div>

</body>
</html>
