<?php

session_start();

// Get student information if not already fetched
// HST.PHP – pull profile info based on role
if (!isset($profile_info)) {
    if (!isset($conn)) include '../../Databased/db_connect.php';
      $role = $_SESSION['user_role'] 
      ?? $_SESSION['userRole']      // fallback to the key Login.php set
      ?? '';      

    switch ($role) {
      case 'student':
        $sql = "SELECT s.student_name   AS name,
                       s.student_id_card AS id_card,
                       s.program,
                       s.semester,
                       s.faculty,
                       u.email
                  FROM student s
                  JOIN users   u ON s.user_id = u.user_id
                 WHERE s.user_id = ?";
        break;

      case 'advisor':
        $sql = "SELECT ea.advisor_name   AS name,
                       u.email
                  FROM eventadvisor ea
                  JOIN users       u ON ea.user_id = u.user_id
                 WHERE ea.user_id = ?";
        break;

      case 'admin':
        $sql = "SELECT pa.admin_name   AS name,
                       u.email
                  FROM petakomadmin pa
                  JOIN users       u ON pa.user_id = u.user_id
                 WHERE pa.user_id = ?";
        break;

      default:
        // fallback—treat as guest
        $profile_info = ['name'=>'Guest','email'=>''];
        break;
    }

    if (isset($sql)) {
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $uid);
      $stmt->execute();
      $profile_info = $stmt->get_result()->fetch_assoc();
      $stmt->close();
    }
}


// Function to check if menu item is active

// Fixed function to check if menu item is active
function isActive($page, $dir = '') {
    $current_page = basename($_SERVER['PHP_SELF']);
    $current_dir = basename(dirname($_SERVER['PHP_SELF']));
    
    // If both directory and page are specified, check both
    if ($dir && $page) {
        if ($current_dir == $dir && $current_page == $page) {
            return 'active';
        }
    }
    // If only directory is specified, check directory
    elseif ($dir && !$page) {
        if ($current_dir == $dir) {
            return 'active';
        }
    }
    // If only page is specified (no directory), check page
    elseif ($current_page == $page) {
        return 'active';
    }
    
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? $page_title : 'Student Dashboard' ?> – <?= htmlspecialchars($student_info['student_name'] ?? 'Student') ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../HADER_SIDER_FOOTER/HST_css">

  <style>
    /* push the body content below the fixed header and beside the sidebar */
.dashboard {
  flex: 1;
  padding: 30px;
  background-color: var(--bg-light);
  min-height: calc(100vh - 90px);
}
.main-container {
  margin-left: 280px;
  padding: 30px;
  background-color: var(--bg-light);
  min-height: calc(100vh - 90px);
}

  </style>



</head>

<body>



  <div class="layout-container">
    <!-- Top Navigation -->
    <div class="main-header">
      <div class="logo-header">
        <a href="#" class="logo">
          <img src="../templet ( use this to match our overview)/image/logo-emblem__329x482.png" alt="MyPetakom Logo" />
        </a>
        <a href="../templet ( use this to match our overview)/images.png" class="logo">
            <img src="../templet ( use this to match our overview)/image/images.png" alt="MyPetakom Logo">
        </a>
      </div>
      <div class="nav-actions">

      <a href="../Module_1/logout.php" class="btn-logout">
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
              <img src="../templet ( use this to match our overview)/image/user-icon.webp" alt="Profile Image" class="profile-image">
            </div>
            

           
                  <ul class="sidebar-menu">
                    <?php if ($role === 'student'): ?>
                      <!-- STUDENT MENU -->
                      <li class="menu-item <?= isActive('dashboard_student.php','all_dashbord') ?>">
                        <a href="../all_dashbord/dashboard_student.php"><i class="fas fa-th-large"></i> Dashboard</a>
                      </li>
                      <li class="menu-item <?= isActive('Manage_User_Profile.php','MODULE_4') ?>">
                        <a href="../MODULE_4/Manage_User_Profile.php"><i class="fas fa-user-edit"></i> Manage Profile</a>
                      </li>

                         <li class="menu-item <?= isActive('../Module_3/Module_3/view.php', 'module_3') ?>">
                           <a href="../Module_3/Module_3/view.php"><i class="fas fa-camera""></i> scan Attendanc</a>
                         </li>
                           <li class="menu-item <?= isActive('../Module_3/Module_3/historyatd.php', 'module_3') ?>">
                              <a href="../Module_3/Module_3/historyatd.php"><i class="fas fa-qrcode"></i> Attendant History</a>
                           </li>

                       <li class="menu-item <?= isActive('Manage_Merits_Claims.php','MODULE_4') ?>">
                        <a href="../MODULE_4/Manage_Merits_Claims.php"><i class="fas fa-clipboard-check"></i> Manage Merit Claims</a>
                      </li>
                      <li class="menu-item <?= isActive('VIEW_AWARDED.PHP','MODULE_4') ?>">
                        <a href="../MODULE_4/VIEW_AWARDED.PHP"><i class="fas fa-medal"></i> View Awarded Merits</a>
                      </li>

                    <?php elseif ($role === 'advisor'): ?>
                      <!-- ADVISOR MENU -->
                      <li class="menu-item <?= isActive('dashboard_advisor.php','all_dashbord') ?>">
                        <a href="../all_dashbord/dashboard_advisor.php"><i class="fas fa-th-large"></i> Advisor Dashboard</a>
                      </li>
                      <li class="menu-item <?= isActive('manage_event.php','Module_2') ?>">
                        <a href="../Module_2/manage_event.php"><i class="fas fa-calendar-alt"></i> Manage Events</a>
                      </li>
                      <li class="menu-item <?= isActive('scan_attendance.php','Module_3') ?>">
                        <a href="../Module_3/scan_attendance.php"><i class="fas fa-clipboard-check"></i> Scan Attendance</a>
                      </li>
                      <li class="menu-item <?= isActive('manage_claim_advisor.php','MODULE_4') ?>">
                        <a href="../MODULE_4/manage_claim_advisor.php"><i class="fas fa-tasks"></i> Review Merit Claims</a>
                      </li>
                      <?php elseif ($role === 'admin'): ?>
                        <!-- ADMIN GIS‐STYLE SIDEBAR -->
                        <ul class="sidebar-menu">
                          <li class="menu-item <?= isActive('Admin-Dashboard.php','all_dashbord') ?>">
                            <a href="../all_dashbord/Admin-Dashboard.php">
                              <i class="fas fa-th-large"></i> Dashboard
                            </a>
                          </li>
                          <li class="menu-item <?= isActive('Admin-CreateUserAccount.php','Module_1') ?>">
                            <a href="../Module_1/Admin-CreateUserAccount.php">
                              <i class="fas fa-user-plus"></i> Create User Account
                            </a>
                          </li>
                          <li class="menu-item <?= isActive('Admin-ManageUserProfiles.php','Module_1') ?>">
                            <a href="../Module_1/Admin-ManageUserProfiles.php">
                              <i class="fas fa-users"></i> Manage User Profiles
                            </a>
                          </li>
                          <li class="menu-item <?= isActive('Admin-ManageMembership.php','Module_1') ?>">
                            <a href="../Module_1/Admin-ManageMembership.php">
                              <i class="fas fa-id-card"></i> Manage Membership
                            </a>
                          </li>
                        </ul>
                      <?php endif; ?>

                  </ul>


          </div>
        </div>
      </div>


      