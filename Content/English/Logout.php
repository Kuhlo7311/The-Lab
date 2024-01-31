<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: ./home.php");
exit();

// Display success message
echo "<article>Logged out successfully.</article>";
?>
