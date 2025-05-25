<?php
session_start();

// If user is already logged in, redirect…
if (isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: /mypetakom/mypetakom/all_dashbord/Admin-Dashboard.php");
            exit;
        case 'advisor':
            header("Location: /mypetakom/mypetakom/all_dashbord/dashboard_advisor.php");
            exit;
        case 'student':
            header("Location: /mypetakom/mypetakom/all_dashbord/dashbord(student).php");
            exit;
    }
}

include '../../Databased/db_connect.php';

$error = '';

// Handle login form
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['login'])) {
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $user_type = $_POST['user_type'];

    if ($username==='' || $password==='' || $user_type==='') {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare(
          "SELECT user_id, username, password, user_role 
             FROM users 
            WHERE username = ? AND user_role = ? 
            LIMIT 1"
        );
        $stmt->bind_param("ss", $username, $user_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows===1) {
            $user = $result->fetch_assoc();

            // detect hashed vs plain‐text
            if (password_get_info($user['password'])['algo']) {
                $valid = password_verify($password, $user['password']);
            } else {
                $valid = $password === $user['password'];
                if ($valid) {
                    // re-hash for security
                    $hp = password_hash($password, PASSWORD_DEFAULT);
                    $u = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
                    $u->bind_param("si",$hp,$user['user_id']);
                    $u->execute();
                    $u->close();
                }
            }

            if ($valid) {
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['user_role'] = $user['user_role'];

                // **REMOVED** login_logs insert

                // redirect
                switch ($user['user_role']) {
                    case 'admin':
                        header("Location: /mypetakom/mypetakom/all_dashbord/Admin-Dashboard.php");
                        break;
                    case 'advisor':
                        header("Location: /mypetakom/mypetakom/all_dashbord/dashboard_advisor.php");
                        break;
                    case 'student':
                        header("Location: /mypetakom/mypetakom/all_dashbord/dashbord(student).php");
                        break;
                }
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or user type.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PETAKOM Login System</title>
  <link rel="stylesheet" href="../CSS/MODULE_1_css/login.css">
  <!-- your styles here… -->
</head>
<body>
  <?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <input name="username"  placeholder="Username" value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
    <input name="password"  type="password" placeholder="Password">
    <select name="user_type">
      <option value="">-- Select Role --</option>
      <option value="student">Student</option>
      <option value="advisor">Event Advisor</option>
      <option value="admin">PETAKOM Admin</option>
    </select>
    <button name="login">Login</button>
  </form>
</body>
</html>
