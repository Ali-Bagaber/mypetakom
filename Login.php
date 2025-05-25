<?php

session_start();

// adjust this line:
require __DIR__ . '/../Databased/db_connect.php';

// rest of your code…


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['userID']);
    $password = $_POST['password'];
    $userRole = $_POST['userType'];

    // Fetch user record
    $sql  = "SELECT user_id, username, password, user_role 
               FROM users 
              WHERE username = ? 
                AND user_role = ? 
              LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $userRole);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Verify password (hashed)
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID
            session_regenerate_id(true);

            // **Set the same session keys your dashboard expects**
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['login_time']= time();

            // Redirect to the correct absolute path
            switch ($user['user_role']) {
                case 'admin':
                    header("Location: /mypetakom/all_dashbord/Admin-Dashboard.php");
                    break;
                case 'advisor':
                    header("Location: /mypetakom/all_dashbord/dashboard_advisor.php");
                    break;
                case 'student':
                    header("Location: /mypetakom/all_dashbord/dashbord(student).php");
                    break;
                default:
                    // just in case
                    header("Location: /mypetakom/Module_1/Login.php");
            }
            exit;
        } else {
            echo "<script>alert('Invalid password.'); window.location='/mypetakom/Module_1/Login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or role.'); window.location='/mypetakom/Module_1/Login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- ... your form HTML unchanged ... -->
</body>
</html>
