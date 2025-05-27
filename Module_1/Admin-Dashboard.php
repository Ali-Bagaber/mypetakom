<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Petakom Coordinator (Administrator)</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="header">
    <div class="logo-section">
      <img src="Logo1.png" alt="UMP Logo" class="logo">
      <img src="Logo2.png" alt="Petakom Logo" class="logo">
    </div>
	<h1 class="white-text" style="color: white;">Petakom Coordinator (Administrator)</h1>
    <a href="#" id="logoutButton" class="logout-button">Log Out</a>
  </div>

  <div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar" style="border: 1px solid #000; padding: 10px; border-radius: 0;">
      <!-- Admin profile section -->
      <div class="profile">
        <h3>Admin Profile</h3>
        <img src="profileIcon.png" alt="Admin Profile" class="profile-img" />
      </div>

      <!-- Divider line -->
      <hr style="margin: 10px 0; border: 0; border-top: 1px solid #000;">

      <!-- Sidebar menu -->
      <ul class="menu" style="list-style-type: none; padding: 0; margin: 0;">
        <li class="active">Dashboard</li>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #000;">
        <li><a href="Admin-CreateUserAccount.php">Create User Account</a></li>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #000;">
        <li><a href="Admin-ManageUserProfiles.php">Manage User Profiles</a></li>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #000;">
        <li><a href="Admin-ManageMembership.php">Manage Membership</a></li>
      </ul>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
      <div class="stats">
        <!-- Students Card -->
        <div class="card">
          <p>Number of Computer Science Students</p>
          <h2>15,000</h2>
        </div>

        <!-- Lecturers Card -->
        <div class="card">
          <p>Number of Computer Science Lecturers</p>
          <h2>3,000</h2>
        </div>

        <!-- Line Chart -->
        <div class="card chart">
          <p>Student per Year</p>
          <canvas id="studentLineChart"></canvas>
        </div>

        <!-- Bar Chart -->
        <div class="card chart">
          <p>Attendance Rate (%)</p>
          <canvas id="attendanceBarChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart JS -->
  <script>
    // Line Chart: Student per Year
    const ctx1 = document.getElementById('studentLineChart').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: ['2021', '2022', '2023', '2024', '2025'],
        datasets: [{
          label: 'Number of Students',
          data: [2500, 3000, 3200, 4000, 4300],
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    // Bar Chart: Attendance Rate
    const ctx2 = document.getElementById('attendanceBarChart').getContext('2d');
    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [{
          label: 'Attendance Rate (%)',
          data: [85, 90, 88, 92, 87],
          backgroundColor: 'rgba(153, 102, 255, 0.6)',
          borderColor: 'rgba(153, 102, 255, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100
          }
        }
      }
    });

    // Logout Button Function
    document.getElementById('logoutButton').addEventListener('click', function(event) {
      event.preventDefault();
      const confirmLogout = confirm("Are you sure you want to log out?");
      if (confirmLogout) {
        sessionStorage.setItem('logoutSuccess', 'true');
        window.location.href = 'login.php'; // Replace with your login page
      }
    });
  </script>
</body>
</html>
