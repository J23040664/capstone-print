<?php
include("dbms.php");
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $_GET['id']) {
    header("Location: login.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resetpassbtn'])) {
    $user_id = $_GET['id'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];

    if ($new_password !== $confirm_password) {
        $passwordNotMatch = true;
    }

    $checkPassword = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
    $checkPassword->bind_param("i", $user_id);
    $checkPassword->execute();
    $result = $checkPassword->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $old_hashed_password = $row['password'];

        // Check if new password matches current one
        if (password_verify($new_password, $old_hashed_password)) {
            $passwordMatchCurrent = true;
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $updatePassword = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
        $updatePassword->bind_param("si", $hashed_password, $user_id);

        if ($passwordNotMatch) {
            $message = '<div class="alert alert-danger" role="alert">
                            Password and Confrim Password is not match
                        </div>';
        } else if ($passwordMatchCurrent) {
            $message = '<div class="alert alert-danger" role="alert">
                            New Password cannot same with current password
                        </div>';
        } else if ($updatePassword->execute()) {
            header("refresh:2; url=login.php");
            session_unset();
            session_destroy();
            exit;
        } else {
            $message = '<div class="alert alert-danger" role="alert">' .
                "Error updating password: " . $updatePassword->error;
            '</div>';
        }

        $updatePassword->close();
    } else {
        $message = '<div class="alert alert-danger" role="alert">
                        User not found
                    </div>';
    }

    $checkPassword->close();
    $conn->close();
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
        <?php echo $message; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">
                    Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn bg-white text-muted" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirmpassword" class="form-label">
                    Confirm Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required>
                    <button class="btn bg-white text-muted" type="button" id="toggleConfirmPassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid mb-3 mt-3">
                <button type="submit" class="btn login-btn" name="resetpassbtn">Continue</button>
            </div>
        </form>

        <div class="text-center">
            <a class="text-black text-decoration-none" href="login.php">Cancel</a>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle for password field
        const togglePasswordBtn = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const passwordIcon = togglePasswordBtn.querySelector('i');

        togglePasswordBtn.addEventListener('click', function() {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            passwordIcon.classList.toggle('bi-eye');
            passwordIcon.classList.toggle('bi-eye-slash');
        });

        // Toggle for confirm password field
        const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
        const confirmpassword = document.getElementById('confirmpassword');
        const confirmIcon = toggleConfirmPasswordBtn.querySelector('i');

        toggleConfirmPasswordBtn.addEventListener('click', function() {
            const type = confirmpassword.type === 'password' ? 'text' : 'password';
            confirmpassword.type = type;
            confirmIcon.classList.toggle('bi-eye');
            confirmIcon.classList.toggle('bi-eye-slash');
        });
    </script>

</body>

</html>