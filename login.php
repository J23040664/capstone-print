<?php
include "./dbms.php";
session_start();

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
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];

                echo "<p style='color:green;'>Login successful. Redirecting in 2 seconds...</p>";
                header("Refresh: 2; url=dashboard.php?id=" . urlencode($user['user_id']));
                exit;
            } else {
                echo "<p style='color:red;'>Incorrect password.</p>";
            }
        } else {
            echo "<p style='color:red;'>Incorrect Email.</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>Please fill in all fields.</p>";
    }
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

        <h3 class="text-center mb-3">Sign In</h3>

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
                <a href="reset_pass1.php" class="d-inline-block mt-2">Forgot your password?</a>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary" name="signinbtn">Log in</button>
            </div>

            <div>
                <span>Don't have an account? <a href="signup.php">Sign Up</a></span>
            </div>
        </form>

    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const icon = togglePasswordBtn.querySelector('i');

        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>

</body>

</html>