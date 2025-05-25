<?php
include('db_connect.php');

// Handle approve/reject action if present
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $status = ucfirst($row['claim_status']);
	$submission_date = date('Y-m-d');
	$query = "INSERT INTO meritapplication (event_id, user_id, claim_status, submission_date, supporting_document) 
			  VALUES (?, ?, 'pending', ?, ?)";

	$stmt = $conn->prepare($query);
	$stmt->bind_param("iiss", $event_id, $user_id, $submission_date, $supporting_document);
	$stmt->execute();




    if ($action === 'approve' || $action === 'reject') {
        $query = "UPDATE meritapplication SET claim_status = ? WHERE application_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $action, $id);
        $stmt->execute();
        $stmt->close();
    }

 
}

// Query to fetch merit applications
$query = "
SELECT 
    ma.application_id,
    e.title AS title,
    u.name AS name,
    ma.submission_date,
    ma.supporting_document,
    ma.claim_status
FROM 
    meritapplication ma
LEFT JOIN 
    events e ON ma.event_id = e.event_id
LEFT JOIN 
    users u ON ma.user_id = u.user_id
ORDER BY 
    ma.application_id DESC";


$result = $conn->query($query);

if (!$result) {
    die("Query error: " . $conn->error); // ❗ Helpful debugging
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Merit Application Approval</title>
  <link rel="stylesheet" href="styleadvisor.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div>
        <div class="logos">
          <img src="ump logo.png" alt="UMP Logo">
          <img src="petakom logo.png" alt="Petakom Logo">
        </div>
        <h2>Advisor Profile</h2>
        <div class="profile-pic"></div>
        <ul>
          <li><a href="dashboard_advisor.php">Dashboard</a></li>
          <li>Manage User Profile</li>
          <li><a href="create_event.php">Create New Event</a></li>
          <li><a href="create_committee.php">Register Committee</a></li>
          <li><a href="manage_event.php">Manage Events</a></li>
          <li><a href="manage_committee.php">Manage Committees</a></li>
          <li><a href="merit_approval.php" class="active">Merit Application Approval</a></li>
          <li><a href="event_qr.php">Event QR Code</a></li>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="top-header">
        <h2>MyPetakom System</h2>
        <button class="logout">Log Out</button>
      </div>

      <div class="charts">
        <h2>Merit Application Approval</h2>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Event Name</th>
                <th>Submitted by</th>
                <th>Submission Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $status = ucfirst($row['claim_status']);
                  $color = $status === 'Approved' ? 'lightgreen' : ($status === 'Rejected' ? 'red' : 'gold');

                  echo "<tr>
                          <td>{$no}</td>
                          <td>{$row['title']}</td>
                          <td>{$row['name']}</td>
                          <td>" . ($row['submission_date'] ?? '-') . "</td>
                          <td style='color: {$color};'>{$status}</td>
                        </tr>";

                  

               
                  $no++;
                }
              } else {
                echo "<tr><td colspan='7'>No merit applications found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
