
<?php
include 'Databased/db_connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard – Awarded Merits</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="./dashbord.css">

  
</head>
<body>

  
  <div class="layout-container">
    <!-- Top Navigation -->
    <div class="main-header">
      <div class="logo-header">
        <a href="../templet.html" class="logo">
          <img src="../templet/logo-emblem__329x482.png" alt="MyPetakom Logo" />
        </a>
       <a href="../templet/images.png" class="logo">
            <img src="../templet/images.png" alt="MyPetakom Logo">
        </a>
      </div>
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
            <h4 class="section-title">Student Profile</h4>
            <div class="profile-image-container">
              <img src="../templet/user-icon-on-transparent-background-free-png.webp" alt="Profile Image" class="profile-image">
            </div>
            <ul class="sidebar-menu">
              <li class="menu-item active">
                <a href="#"><i class="fas fa-th-large"></i> Dashboard</a>
              </li>
              <li class="menu-item">
                <a href="#"><i class="fas fa-user-edit"></i> Manage User Profile</a>
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

      <!-- Main Content -->
      <div class="main-content">
        <div class="page-inner">
          <h2>Student Dashboard</h2>
          <p><b>Welcome to the dashboard!</b><br>Track Your Awarded Merits and Activities</p>
          
          <div class="header-actions">
            <button class="btn-info">
              <i class="fas fa-user-edit"></i> Manage Profile
            </button>
            <button class="btn-primary">
              <i class="fas fa-plus"></i> Add Merit Claim
            </button>
          </div>
        </div>

        <div class="widget-row">
          <!-- Total Merit Widget -->
          <div class="card">
            <div class="icon-box">
              <i class="fas fa-award"></i>
            </div>
            <p>Total Merits Collected</p>
            <div class="card-title">1,345</div>
          </div>

          <!-- QR Code Widget -->
          <div class="card">
            <div class="qr-box">
              <img src="../templet/download.png" alt="QR Code">
            </div>
            <a href="../templet.html" download class="btn-sm">
              <i class="fas fa-download"></i> Download QR
            </a>
          </div>
        </div>

        <div class="stats-row">
          <!-- Merit Growth Chart -->
          <div class="chart-card">
            <h3>Merit Growth Over Time</h3>
            <canvas id="statisticsChart" width="400" height="200"></canvas>
            <div class="chart-buttons">
              <button class="btn-export">
                <i class="fas fa-file-export"></i> Export
              </button>
              <button class="btn-print">
                <i class="fas fa-print"></i> Print
              </button>
            </div>
          </div>

          <!-- Recent Awarded Merits Table -->
          <div class="merits-table">
            <h3>Recent Events and Merits</h3>
            <table>
              <thead>
                <tr>
                  <th>Event</th>
                  <th>Role</th>
                  <th>Points</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Hackathon 2025</td>
                  <td><span class="badge badge-committee">Committee</span></td>
                  <td>70</td>
                  <td>Mar 2025</td>
                </tr>
                <tr>
                  <td>Workshop Series</td>
                  <td><span class="badge badge-committee">Facilitator</span></td>
                  <td>55</td>
                  <td>Apr 2025</td>
                </tr>
                <tr>
                  <td>Coding Competition</td>
                  <td><span class="badge badge-committee">Participant</span></td>
                  <td>40</td>
                  <td>Apr 2025</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <footer class="footer">
      <p>&copy; 2025 MyPetakom System. All rights reserved. | UMP Student Dashboard</p>
    </footer>
  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="script.js" defer></script>

</body>
</html>