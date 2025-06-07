<?php 
session_start();
session_unset();
session_destroy();

echo "You have successfully logged out!<br>";
header("refresh:2; url=login.php");

?>