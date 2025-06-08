<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['id'] != $_GET['id']) {
    header("Location: login.php");
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

        <p class="text-center me-2 mt-3 mb-3">Please enter your verification code to reset your password.</p>

        <form id="verification_form">
            <label for="code">Verification Code:</label>
            <div class="input-group">
                <input type="text" class="form-control" id="code" name="code" required />
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="verifyCode('<?php echo $_SESSION['email']; ?>')" id="send_code_btn">Send code</button>
            </div>

            <div class="d-grid mb-3 mt-3">
                <button type="submit" class="btn btn-primary">Submit Code</button>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script> -->
    <!-- <script src="email.js"></script> -->

</body>

</html>