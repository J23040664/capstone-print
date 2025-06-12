<?php
include("./dbms.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkAccount'])) {

    $email = $_POST['email'];

    $checkEmail = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['email'] = $row['email'];
        header("Location: reset_pass2.php?user_id=" . $row['user_id']);
        exit();
    } else {
        echo "This account doesn't exist";
    }


    $checkEmail->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Art & Print</title>

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

        <h3 class="text-center mb-3">Reset Password</h3>

        <p class="text-center me-2 mt-3 mb-3">Enter your email and we'll send you a verification code to reset your password.</p>

        <form method="POST">
            <label for="username" class="form-label">Email: </label>
            <div class="text-center mb-3">
                <input type="email" class="form-control" id="email" name="email" required>
                <button type="submit" class="btn btn-primary w-100 mt-3 mb-3" name="checkAccount">Next</button>
                <a class="text-black text-decoration-none" href="login.php"><i class="bi bi-arrow-left me-2"></i>Back to login</a>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script> -->
    <!-- <script src="email.js"></script> -->

</body>

</html>