<?php

include '../../Databased/db_connect.php';

  include '../HADER_SIDER_FOOTER/HST.PHP';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard – Attendance History</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="./historyatd.css">
</head>
<body>

  <div class="layout-container">
    
    <!-- Page Wrapper (Sidebar + Content) -->
    <div class="wrapper">
   

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
