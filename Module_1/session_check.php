<?php
// session_check.php - Include this file at the top of all protected pages

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['username']) && 
           isset($_SESSION['user_role']);
}

// Function to check user role
function hasRole($required_role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $required_role;
}

// Function to check multiple roles
function hasAnyRole($required_roles) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    return in_array($_SESSION['user_role'], $required_roles);
}

// Function to get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'user_role' => $_SESSION['user_role'],
        'login_time' => isset($_SESSION['login_time']) ? $_SESSION['login_time'] : null
    ];
}

// Function to redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

// Function to require specific role
function requireRole($required_role) {
    requireLogin();
    
    if (!hasRole($required_role)) {
        // Redirect to appropriate dashboard or show access denied
        switch ($_SESSION['user_role']) {
            case 'admin':
                header("Location: Admin-Dashboard.php");
                break;
            case 'advisor':
                header("Location: dashboard_advisor.php");
                break;
            case 'student':
                header("Location: dashbord(student).php");
                break;
            default:
                header("Location: ../login.php");
        }
        exit();
    }
}

// Function to require any of multiple roles
function requireAnyRole($required_roles) {
    requireLogin();
    
    if (!hasAnyRole($required_roles)) {
        // Redirect to appropriate dashboard
        switch ($_SESSION['user_role']) {
            case 'admin':
                header("Location: Admin-Dashboard.php");
                break;
            case 'advisor':
                header("Location: dashboard_advisor.php");
                break;
            case 'student':
                header("Location: dashbord(student).php");
                break;
            default:
                header("Location: ../login.php");
        }
        exit();
    }
}

// Function to update last activity
function updateLastActivity() {
    if (isLoggedIn()) {
        $_SESSION['last_activity'] = time();
        
        // Optional: Update in database
        require_once '../Databased/db_connect.php';
        $stmt = $conn->prepare("UPDATE users SET last_activity = NOW() WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

// Session timeout check (30 minutes default)
function checkSessionTimeout($timeout = 1800) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Session has expired
        session_unset();
        session_destroy();
        header("Location: ../login.php?timeout=1");
        exit();
    }
    updateLastActivity();
}

// Auto-logout after inactivity
function autoLogout($timeout = 1800) {
    checkSessionTimeout($timeout);
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Display user welcome message
function displayWelcome() {
    if (isLoggedIn()) {
        $user = getCurrentUser();
        echo "<div class='welcome-message'>";
        echo "<p>Welcome, <strong>" . htmlspecialchars($user['username']) . "</strong>";
        echo " (" . htmlspecialchars(ucfirst($user['user_role'])) . ")";
        echo " | <a href='../logout.php'>Logout</a></p>";
        echo "</div>";
    }
}

// Get logout link
function getLogoutLink() {
    return "<a href='../logout.php' onclick='return confirm(\"Are you sure you want to logout?\")'>Logout</a>";
}
?>