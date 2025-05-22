<?php
// Get student information if not already fetched
if (!isset($student_info)) {
    // Only include db_connect if not already connected
    if (!isset($conn)) {
        include '../../Databased/db_connect.php';
    }
    
    $user_id = 1; // You can make this dynamic based on session
    
    $student_sql = "SELECT s.*, u.username, u.email 
                    FROM student s 
                    JOIN users u ON s.user_id = u.user_id 
                    WHERE s.user_id = ?";
    
    $stmt = $conn->prepare($student_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student_info = $student_result->fetch_assoc();
}

// Function to check if menu item is active
function isActive($page, $dir = '') {
    $current_page = basename($_SERVER['PHP_SELF']);
    $current_dir = basename(dirname($_SERVER['PHP_SELF']));
    
    if ($dir && $current_dir == $dir) return 'active';
    if ($current_page == $page) return 'active';
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
  <link rel="stylesheet" href="../CSS/HST_CSS.css">
</head>
<body>
  <div class="layout-container">
    <!-- Top Navigation -->
    <div class="main-header">
      <div class="logo-header">
        <a href="#" class="logo">
          <img src="../templet/image/logo-emblem__329x482.png" alt="MyPetakom Logo" />
        </a>
        <a href="../templet/images.png" class="logo">
            <img src="../templet/image/images.png" alt="MyPetakom Logo">
        </a>
      </div>
      <div class="nav-actions">
        <div class="notification-icon">
          <i class="fas fa-bell"></i>
        </div>
        <a href="../login/logout.php" class="btn-logout">
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
              <img src="../templet/image/user-icon.webp" alt="Profile Image" class="profile-image">
            </div>
            
            <!-- Student Info Display -->
            <?php if (isset($student_info) && $student_info): ?>
            <div style="text-align: center; margin-bottom: 20px; color: rgba(255,255,255,0.9);">
              <div style="font-weight: 600; margin-bottom: 5px;">
                <?= htmlspecialchars($student_info['student_name']) ?>
              </div>
              <div style="font-size: 0.85rem; opacity: 0.8;">
                <?= htmlspecialchars($student_info['student_id_card']) ?>
              </div>
              <div style="font-size: 0.8rem; opacity: 0.7;">
                <?= htmlspecialchars($student_info['program']) ?> - Sem <?= htmlspecialchars($student_info['semester']) ?>
              </div>
            </div>
            <?php endif; ?>
            
            <ul class="sidebar-menu">
              <li class="menu-item <?= isActive('dashboard.php', 'all_dashbord') ?>">
                <a href="../all_dashbord/dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
              </li>
              <li class="menu-item <?= isActive('manage_profile.php', 'profile') ?>">
                <a href="../profile/manage_profile.php"><i class="fas fa-user-edit"></i> Manage User Profile</a>
              </li>
              <li class="menu-item <?= isActive('manage_membership.php', 'membership') ?>">
                <a href="../membership/manage_membership.php"><i class="fas fa-id-card"></i> Manage Membership</a>
              </li>
              <li class="menu-item <?= isActive('VIEW_AWARDED.PHP', 'MODULE_4') ?>">
                <a href="../MODULE_4/VIEW_AWARDED.PHP"><i class="fas fa-medal"></i> View Awarded Merits</a>
              </li>
              <li class="menu-item <?= isActive('manage_claims.php', 'claims') ?>">
                <a href="../claims/manage_claims.php"><i class="fas fa-clipboard-check"></i> Manage Merits Claims</a>
              </li>
            </ul>
          </div>
        </div>
      </div>