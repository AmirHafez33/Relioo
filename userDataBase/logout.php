<?php
session_start(); // Start the session

// Destroy all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Optional: redirect to login page or homepage
header("Location: login.php");
exit;
