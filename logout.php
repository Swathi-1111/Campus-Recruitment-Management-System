<?php
session_start();
// Destroy the session
$_SESSION = array();
session_destroy();
// Redirect to index page
header("Location: index.php");
exit;
?>