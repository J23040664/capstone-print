<?php
include("./dbms.php");
session_start();
$errorMessage = "";

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
        header("Location: reset_pass2.php?id=" . $row['user_id']);
        exit();
    } else {
        $errorMessage = '<div class="alert alert-danger" role="alert">
                                Account does not exist
                            </div>';
    }


    $checkEmail->close();
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
        <p class="text-center me-2 mt-3 mb-3">Enter your email and we'll send you a verification code to reset your password.</p>
        <?php echo $errorMessage; ?>
        <form method="POST">
            <label for="username" class="form-label">Email: </label>
            <div class="text-center mb-3">
                <input type="email" class="form-control" id="email" name="email" required>
                <button type="submit" class="btn w-100 mt-3 mb-3 login-btn" name="checkAccount" id="checkAccount">Next</button>
                <a class="text-black text-decoration-none" href="login.php"><i class="bi bi-arrow-left me-2"></i>Back to login</a>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    const emailInput = document.getElementById('email');
    const nextBtn = document.getElementById('checkAccount');

    // Simple email validation regex
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Enable or disable button based on email validity
    emailInput.addEventListener('input', () => {
        if (isValidEmail(emailInput.value)) {
            nextBtn.disabled = false;
        } else {
            nextBtn.disabled = true;
        }
    });
</script>