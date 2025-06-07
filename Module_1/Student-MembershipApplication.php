<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentName = $_POST['studentName'] ?? '';
    $studentID = $_POST['studentID'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $program = $_POST['program'] ?? '';
    $year = $_POST['year'] ?? '';

    if (!$studentName || !$studentID || !$email || !$phone || !$program || !$year) {
        echo "<script>alert('Please fill all fields'); window.history.back();</script>";
        exit;
    }

    // File upload handling
    $uploadDir = "uploads/studentCards/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $studentCardPath = '';
    if (isset($_FILES['studentCard']) && $_FILES['studentCard']['error'] == 0) {
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $fileType = $_FILES['studentCard']['type'];
        $fileSize = $_FILES['studentCard']['size'];
        $fileName = basename($_FILES['studentCard']['name']);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newName = $studentID . "_" . time() . "." . $ext;

        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Invalid file type. Only PDF, JPG, PNG allowed.'); window.history.back();</script>";
            exit;
        }

        if ($fileSize > 5 * 1024 * 1024) {
            echo "<script>alert('File too large. Max 5MB.'); window.history.back();</script>";
            exit;
        }

        $studentCardPath = $uploadDir . $newName;
        move_uploaded_file($_FILES['studentCard']['tmp_name'], $studentCardPath);
    } else {
        echo "<script>alert('Please upload your student card.'); window.history.back();</script>";
        exit;
    }

    // Replace this with session user_id later
    $user_id = 1;  // dummy user id for now
    $faculty = "Faculty of Computing";
    $student_qr = "";

    // Insert into student table
    $stmt1 = $conn->prepare("INSERT INTO student (user_id, student_name, student_id_card, program, semester, faculty, student_qr) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("issssss", $user_id, $studentName, $studentCardPath, $program, $year, $faculty, $student_qr);
    $stmt1->execute();
    $stmt1->close();

    // Insert into membership table with correct columns
    $join_date = date("Y-m-d");
    $status = "pending";  // default status for new applications

    $stmt2 = $conn->prepare("INSERT INTO membership (user_id, join_date, status) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $user_id, $join_date, $status);
    $stmt2->execute();
    $stmt2->close();

    echo "<script>alert('Application submitted successfully.'); window.location='Student-MembershipApplication.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Petakom Membership Application</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .preview-img {
      max-height: 200px;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
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
        <li class="active">Manage Membership</li>
        <hr />
		<li><a href="Student-ViewMembershipApproval.php">View Membership</a></li>
        <hr />
        <li><a href="Admin-ManageUserProfiles.php">View Awarded Merits</a></li>
        <hr />
        <li><a href="Admin-ManageUserProfiles.php">Manage Merits Claims</a></li>
        <hr />
      </ul>
    </div>

    <div class="admin-container">
      <h1>Petakom Membership Registration</h1>

      <form method="POST" enctype="multipart/form-data">
        <label>Full Name:</label>
        <input type="text" name="studentName" required>

        <label>Student ID:</label>
        <input type="text" name="studentID" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Phone:</label>
        <input type="text" name="phone" required>

        <label>Program:</label>
        <select name="program" required>
          <option value="">-- Select --</option>
          <option value="BCS">BCS</option>
          <option value="BCG">BCG</option>
          <option value="BCN">BCN</option>
          <option value="BCY">BCY</option>
          <option value="DRC">DRC</option>
        </select>

        <label>Year:</label>
        <select name="year" required>
          <option value="">-- Select --</option>
          <option value="1">Year 1</option>
          <option value="2">Year 2</option>
          <option value="3">Year 3</option>
          <option value="4">Year 4</option>
        </select>

        <label>Upload Student Card (PDF/JPG/PNG, max 5MB):</label>
        <input type="file" name="studentCard" id="studentCard" accept=".pdf,.jpg,.jpeg,.png" required>
        <div id="previewContainer">
          <img id="previewImg" class="preview-img" style="display:none;" />
          <p id="previewFile" style="font-style: italic;"></p>
        </div>

        <button type="submit">Submit Application</button>
        <button type="button" onclick="resetForm()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    function resetForm() {
      document.querySelector('form').reset();
      document.getElementById('previewImg').style.display = 'none';
      document.getElementById('previewFile').textContent = '';
    }

    document.getElementById('studentCard').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const previewImg = document.getElementById('previewImg');
      const previewFile = document.getElementById('previewFile');

      if (!file) return;

      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(evt) {
          previewImg.src = evt.target.result;
          previewImg.style.display = 'block';
          previewFile.textContent = '';
        };
        reader.readAsDataURL(file);
      } else {
        previewImg.style.display = 'none';
        previewFile.textContent = 'Selected file: ' + file.name;
      }
    });
	
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
