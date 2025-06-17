<?php
session_start();
session_unset();
session_destroy();

$logoutMessage = "You have successfully logged out!<br>";
header("refresh:2; url=login.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>

    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f2f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <h3 class="text-center mb-3">Logout</h3>
        <p class="text-center"><?php echo $logoutMessage; ?></p>
    </div>

</body>

</html>