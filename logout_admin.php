<?php
// Start session
session_start();

// Destroy all session data
session_destroy();

// Clear any remaining session variables
$_SESSION = array();

// Redirect to admin login page
header("Location: login_admin.php");
exit();
?>

