<?php
session_start();
session_unset();
session_destroy();

session_start();
$_SESSION['logout'] = true;
$_SESSION['logoutMessage'] = "You have already successfully logout!";
header("Location: login.php");
exit;

?>