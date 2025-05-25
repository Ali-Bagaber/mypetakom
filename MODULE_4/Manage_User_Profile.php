<?php
session_start();
include '../../Databased/db_connect.php';

// Identify current user
$user_id = $_SESSION['user_id'] ?? 1;

// Initialize messages
$success = '';
$error   = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $username        = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $student_name    = trim($_POST['student_name']);
    // Fixed fields remain submitted via readonly inputs
    $student_id_card = trim($_POST['student_id_card']);
    $program         = trim($_POST['program']);
    $semester        = trim($_POST['semester']);
    $faculty         = trim($_POST['faculty']);

    // Basic validation
    if (!$username || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid username and email.";
    } else {
        // Update users table
        $updUser = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $updUser->bind_param("ssi", $username, $email, $user_id);
        $updUser->execute();
        $updUser->close();

        // Check if student row exists
        $check = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $res = $check->get_result();
        $check->close();

        if ($res->num_rows) {
            // Update existing student record
            $updStu = $conn->prepare(
                "UPDATE student
                   SET student_name    = ?,
                       student_id_card = ?,
                       program         = ?,
                       semester        = ?,
                       faculty         = ?
                 WHERE user_id = ?"
            );
            $updStu->bind_param(
                "sssssi",
                $student_name,
                $student_id_card,
                $program,
                $semester,
                $faculty,
                $user_id
            );
            $ok = $updStu->execute();
            $updStu->close();
        } else {
            // Insert new student record
            $insStu = $conn->prepare(
                "INSERT INTO student
                    (user_id, student_name, student_id_card, program, semester, faculty)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $insStu->bind_param(
                "isssss",
                $user_id,
                $student_name,
                $student_id_card,
                $program,
                $semester,
                $faculty
            );
            $ok = $insStu->execute();
            $insStu->close();
        }

        if ($ok) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to save student details.";
        }
    }
}

// Load current values
$q = $conn->prepare(
    "SELECT u.username, u.email,
            s.student_name, s.student_id_card, s.program, s.semester, s.faculty
       FROM users u
  LEFT JOIN student s USING(user_id)
      WHERE u.user_id = ?"
);
$q->bind_param("i", $user_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
$q->close();

$username         = $row['username']        ?? '';
$email            = $row['email']           ?? '';
$student_name     = $row['student_name']    ?? '';
$student_id_card  = $row['student_id_card'] ?? '';
$program          = $row['program']         ?? '';
$semester         = $row['semester']        ?? '';
$faculty          = $row['faculty']         ?? '';

// Include header and sidebar
include '../HADER_SIDER_FOOTER/HST.PHP';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage User Profile – Student Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/manage_profile.css">
</head>
<body>

  <div class="form-container">
    <h2><i class="fas fa-user"></i> Manage Your Profile</h2>

    <?php if ($success): ?>
      <div class="alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username) ?>">
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
      </div>

      <hr>

      <h3>Student Details</h3>
      <div class="form-group">
        <label for="student_name">Full Name</label>
        <input type="text" id="student_name" name="student_name" value="<?= htmlspecialchars($student_name) ?>">
      </div>
      <div class="form-group">
        <label for="student_id_card">Student ID Card</label>
        <input type="text" id="student_id_card" name="student_id_card" readonly value="<?= htmlspecialchars($student_id_card) ?>">
      </div>
      <div class="form-group">
        <label for="program">Program</label>
        <input type="text" id="program" name="program" readonly value="<?= htmlspecialchars($program) ?>">
      </div>
      <div class="form-group">
        <label for="semester">Semester</label>
        <input type="text" id="semester" name="semester" readonly value="<?= htmlspecialchars($semester) ?>">
      </div>
      <div class="form-group">
        <label for="faculty">Faculty</label>
        <input type="text" id="faculty" name="faculty" readonly value="<?= htmlspecialchars($faculty) ?>">
      </div>

      <button type="submit" class="btn-primary">
        <i class="fas fa-save"></i> Save Changes
      </button>
    </form>
  </div>

</body>
</html>

<?php
$conn->close();
?>
