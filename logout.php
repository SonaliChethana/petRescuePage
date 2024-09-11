<?php
// logout.php

session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();

// Redirect the user to the homepage or login page after logout
header("Location: login.php"); // Change this to your desired page
exit();
?>
