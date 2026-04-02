<?php
// Initialize the session
session_start();

// Include bootstrap file with autoloader
require_once "includes/bootstrap.php";

// Create User object and call logout method
$userObj = new User();
$userObj->logout();

// Redirect to login page
header("location: view/login.php");
exit;
?>