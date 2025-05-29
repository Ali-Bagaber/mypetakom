<?php
include('db_connect.php');
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event QR Code</title>
  <link rel="stylesheet" href="styleadvisor.css">
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logos">
          <img src="ump logo.png" alt="ump Logo">
          <img src="petakom logo.png" alt="petakom Logo">
        </div>
		<h2>Advisor Profile</h2>
        <div class="profile-pic"></div>
        <ul>
			<li><a href="dashboard_advisor.php">Dashboard</a></li>
			<li><a href="manage_profile_advisor.php">Manage User Profile</a></li>
			<li><a href="create_event.php">Create New Event</a></li>
			<li><a href="create_committee.php">Register Commitee </a></li>
			<li><a href="manage_event.php">Manage Events</a></li>
			<li><a href="manage_committee.php">Manage Committees</a></li>
			<li><a href="merit_approval.php">Merit Application Approval</a></li>
			<li><a href="event_qr.php"class="active">Event QR Code</a></li>
        </ul>
      </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="top-header">
        <h1>MyPetakom System</h1>
        <button class="logout">Log Out</button>
      </div>

      <h2>Event QR Codes</h2>
	<div class="qr-table-wrapper">
	  <table class="qr-table">
		<thead>
		  <tr>
			<th>No</th>
			<th>Event Name</th>
			<th>Date</th>
			<th>QR Code Preview</th>
			<th>Status</th>
			<th>Action</th>
		  </tr>
		</thead>
		<tbody id="event-table-body">
			<?php
				$sql = "SELECT * FROM events";
				$result = mysqli_query($conn, $sql);
				$counter = 1;

				if (mysqli_num_rows($result) > 0) {
				  while($row = mysqli_fetch_assoc($result)) {
					$eventId = $row['event_id'];
					$title = htmlspecialchars($row['title']);
					$date = date('d/m/Y', strtotime($row['start_date']));
					$status = $row['event_status'];
					$host = $_SERVER['HTTP_HOST'];
					$path = dirname($_SERVER['PHP_SELF']);
					if ($_SERVER['HTTP_HOST'] === 'localhost') {
					  $qrLink = "http://{$host}{$path}/view_event.php?event_id={$eventId}";
					} else {
					  $qrLink = "https://mypetakom.ump.edu.my/view_event.php?event_id={$eventId}";
					}



					// Set status color
					$color = 'black';
					if ($status === 'Active') $color = 'lightgreen';
					elseif ($status === 'Postponed') $color = 'gold';
					elseif ($status === 'Cancelled') $color = 'red';
					elseif ($status === 'Completed') $color = 'lightblue';

					echo "
					  <tr>
						<td>{$counter}</td>
						<td>{$title}</td>
						<td>{$date}</td>
						<td class='qr-cell'>
						  <div id='qrcode-{$eventId}' class='qr-container' data-qrtext='{$qrLink}'></div>
						  <button onclick='downloadQR({$eventId})'>⬇</button>
						</td>
						<td style='color: {$color}'>{$status}</td>
						<td>
						  
						  <a href='view_event.php?event_id={$eventId}' target='_blank'><button>View</button></a>

						  <a href='#' onclick=\"copyLink('{$qrLink}'); return false;\"><button>Copy Link</button></a>
						  
						</td>
					  </tr>";

					$counter++;
				  }
				} else {
				  echo "<tr><td colspan='6'>No events found.</td></tr>";
				}
			?>

</tbody>

	  </table>
	</div>


	<script>
  window.onload = function () {
    const qrCells = document.querySelectorAll('.qr-container');
    qrCells.forEach(container => {
      const qrText = container.getAttribute('data-qrtext');
      new QRCode(container, {
        text: qrText,
        width: 64,
        height: 64
      });
    });
  };

  function downloadQR(id) {
    const canvas = document.querySelector(`#qrcode-${id} canvas`);
    if (!canvas) return alert("QR code not loaded yet.");
    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/png');
    link.download = `event_qr_${id}.png`;
    link.click();
  }

  function copyLink(qrText) {
    navigator.clipboard.writeText(qrText).then(() => {
      alert("Event link copied to clipboard!");
    }).catch(err => {
      alert("Failed to copy link: " + err);
    });
  }
	</script>


</body>
</html>
