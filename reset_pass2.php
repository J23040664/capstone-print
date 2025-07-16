<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $_GET['id']) {
    header("Location: login.php");
    exit; // stop further execution after redirect
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitCode'])) {
    header("Location: reset_pass3.php?id=" . $_SESSION['user_id']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/systemStyle.css">

</head>

<body class="login-body">

    <div class="login-box">

        <h3 class="text-center mb-3">Reset Password</h3>

        <p class="text-center me-2 mt-3 mb-3">Please enter your verification code to reset your password.</p>

        <form method="POST" id="verificationCode">
            <label for="code">Verification Code:</label>
            <div class="input-group">
                <input type="text" class="form-control" id="code" name="code" required />
                <button type="button" class="btn btn-sm sendcode-btn" onclick="verifyCode('<?php echo $_SESSION['email']; ?>')" id="send_code_btn">Send code</button>
            </div>

            <div class="d-grid mb-3 mt-3">
                <button type="submit" class="btn login-btn" name="submitCode">Submit Code</button>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript" src="./assets/js/email.js"></script>

    <script>
        document.getElementById('verificationCode').addEventListener('submit', function(e) {
            const inputCode = document.getElementById('code').value;
            const storedCode = sessionStorage.getItem('verificationCode');
            if (inputCode !== storedCode) {
                e.preventDefault(); // stop submit
                alert("Invalid verification code.");
            } else {
                // Code is correct, so remove it from sessionStorage
                sessionStorage.removeItem('verificationCode');
            }
        });
    </script>

</body>

</html>