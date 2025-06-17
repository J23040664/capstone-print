<?php
session_start();
session_unset();
session_destroy();

$logoutMessage = "You have successfully logged out!<br>";
// header("refresh:2; url=login.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./adminStyle.css">
</head>

<body class="login-body">

    <div class="logout-box">
        <h2 class="text-center mb-3">Logout</h2>
        <p class="text-center text-danger fs-3"><?php echo $logoutMessage; ?></p>
        <a class="btn login-btn d-block text-center" href=" login.php">Back to login</a>
    </div>

</body>

</html>