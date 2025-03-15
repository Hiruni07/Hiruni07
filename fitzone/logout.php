<?php
session_start();  // Start the session

// Destroy all session data (logout the user)
session_unset();  // Unset session variables
session_destroy();  // Destroy the session

// Redirect to the homepage (index.php)
header("Location: index.php");
exit();
?>
