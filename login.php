<?php
session_start();
include "dbms.php";

$showLogoutToast = false;
if (isset($_SESSION['logout']) && $_SESSION['logout']) {
    $showLogoutToast = true;
    $toastLogoutMessage = $_SESSION['logoutMessage'];
    unset($_SESSION['logout']);
    unset($_SESSION['logoutMessage']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signinbtn"])) {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Login success: store user info in session
                $_SESSION['id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_success'] = true;

                header("Refresh: 1; url=adminDashboard.php?id=" . urlencode($user['user_id']));
                exit;
            } else {
                $errorMessage = '<div class="alert alert-danger" role="alert">
                                    Password is invalid
                                </div>';
            }
        } else {
            $errorMessage = '<div class="alert alert-danger" role="alert">
                                Account does not exist
                            </div>';
        }

        $stmt->close();
    } else {
        $errorMessage = '<div class="alert alert-danger" role="alert">
                            All fields is required.
                        </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Art & Print</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/systemStyle.css">
</head>

<body class="login-body">
    <?php if ($showLogoutToast): ?>
        <!-- Toast Container -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <div id="logoutToast" class="toast text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $toastLogoutMessage; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="login-box">

        <h3 class="text-center mb-3">Sign In</h3>

        <?php echo $errorMessage; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn bg-white text-muted" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <a href="reset_pass1.php" class="d-inline-block text-decoration-none mt-2 link">Forgot your password?</a>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn login-btn" name="signinbtn">Log in</button>
            </div>

            <div>
                <span>Don't have an account? <a class="text-decoration-none link" href="signup.php">Sign Up</a></span>
            </div>
        </form>
        <hr class="mt-4 mb-4">
        <a href="index.php" class="link d-block text-center">Back To Home Page</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toast = new bootstrap.Toast(document.getElementById('logoutToast'));
        toast.show();
    </script>

    <script>
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const icon = togglePasswordBtn.querySelector('i');

        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>