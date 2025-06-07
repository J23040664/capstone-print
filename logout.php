<?php 
session_start();
session_unset();
session_destroy();

echo "You have successfully logged out!<br>";
echo "<a href='login.php'>Click here to login again</a>";

?>