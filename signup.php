<?php
include "dbms.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signupbtn'])) {

    $name = htmlspecialchars(trim($_POST["fullname"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $phonenumber = htmlspecialchars(trim($_POST["phonenumber"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $confirmpassword = htmlspecialchars(trim($_POST["confirmpassword"]));
    $role = "Customer";
    $create_date = date("Y-m-d");
    $img_id = 1;

    if (!empty($name) && !empty($email) && !empty($phonenumber) && !empty($password) && !empty($confirmpassword)) {

        if ($password !== $confirmpassword) {
            echo "<p style='color:red;'>Error: Passwords do not match.</p>";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errorMessage = '<div class="alert alert-danger" role="alert">
                                    Account have already exist
                                </div>';
            } else {
                // Hash password and insert user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $insert = $conn->prepare("INSERT INTO user (name, email, phone_number, password, role, create_date, img_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert->bind_param("sssssss", $name, $email, $phonenumber, $hashedPassword, $role, $create_date, $img_id);

                if ($insert->execute()) {
                    echo "<p style='color:green;'>Account created successfully. Redirecting to login...</p>";
                    header("refresh:2; url=login.php");
                    exit;
                } else {
                    echo "<p style='color:red;'>Error: " . htmlspecialchars($insert->error) . "</p>";
                }

                $insert->close();
            }

            $stmt->close();
        }
    } else {
        $errorMessage = '<div class="alert alert-danger" role="alert">
                            Please fill in all the fields
                        </div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - Art & Print</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/systemStyle.css">

</head>

<body class="login-body">

    <div class="login-box">

        <h3 class="text-center mb-3">Create an account</h3>

        <?php echo $errorMessage ?>

        <form method="POST" id="signUpCode">
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="phonenumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phonenumber" name="phonenumber" required>
            </div>

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

            <div class="mb-3">
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

            <div class="mb-4">
                <label for="code">Verification Code:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="code" name="code" required />
                    <button type="button" class="btn btn-sm sendcode-btn" onclick="verifyCode(document.getElementById('email').value)" id="send_code_btn">
                        Send code
                    </button>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn login-btn" name="signupbtn">Sign Up</button>
                <div style="font-size: 11px; margin-top: 10px;">
                    <span>By registering you agree to Art & Print <a class="link" href="#">Terms Of Service</a> and <a class="link" href="#">Privacy and Policy</a></span>
                </div>
            </div>

            <div>
                <span>Already have an account? <a class="text-decoration-none link" href="login.php">Sign In</a></span>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script src="./assets/js/email.js"></script>

    <script>
        const emailInput = document.getElementById('email');
        const sendCodeBtn = document.getElementById('send_code_btn');

        // Simple email validation regex
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Disable button initially
        sendCodeBtn.disabled = true;

        // Enable or disable send button based on email input
        emailInput.addEventListener('input', () => {
            sendCodeBtn.disabled = !isValidEmail(emailInput.value);
        });

        // Disable send button for 60 seconds after click
        sendCodeBtn.addEventListener('click', () => {
            sendCodeBtn.disabled = true;
            sendCodeBtn.textContent = "Please wait...";

            setTimeout(() => {
                // Recheck email validity before enabling again
                sendCodeBtn.disabled = !isValidEmail(emailInput.value);
                sendCodeBtn.textContent = "Send Code";
            }, 60000); // 60 seconds
        });
    </script>

    <script>
        document.getElementById('signUpCode').addEventListener('submit', function(e) {
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