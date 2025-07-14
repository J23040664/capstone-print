<?php
session_start();
include 'dbms.php';

if (isset($_SESSION['role']) && $_SESSION['id'] == $_GET['id']) {
    $user_id = $_GET['id'];
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

// list all the image
$showImgList = "SELECT * FROM profile_images";
$queryShowImgList = mysqli_query($conn, $showImgList) or die(mysqli_error($conn));

$showUpdateToast = false;
if (isset($_SESSION['update_success']) && $_SESSION['update_success'] === true) {
    $showUpdateToast = true;
    unset($_SESSION['update_success']); // Show only once
}

$showUpdateErrorToast = false;
if (isset($_SESSION['update_error']) && $_SESSION['update_error'] === true) {
    $showUpdateErrorToast = true;
    unset($_SESSION['update_error']); // Show only once
}

// Update profile picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProfileBtn'])) {
    $newImgid = $_POST['updatePic'];

    $updateImg = "UPDATE user SET img_id = '$newImgid' WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $updateImg)) {

        $_SESSION['update_success'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Update info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateInfoBtn'])) {
    $newName = trim($_POST['updateName']);
    $newPhoneNumber = trim($_POST['updatePhoneNumber']);

    // Check if either field is empty
    if (empty($newName) || empty($newPhoneNumber)) {
        $_SESSION['update_error'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    }

    $updateInfo = "UPDATE user SET name = '$newName', phone_number = '$newPhoneNumber'  WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $updateInfo)) {
        $_SESSION['update_success'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    } else {
        $_SESSION['update_error'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    }
}

// update email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateEmailBtn'])) {
    $newEmail = trim($_POST['updateEmail']);

    // Fetch current email from database
    $currentEmailQuery = "SELECT email FROM user WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $currentEmailQuery);
    $row = mysqli_fetch_assoc($result);

    if ($row['email'] === $newEmail) {
        // New email is the same as current one
        $_SESSION['update_error'] = true;
        echo "<script>
        alert('New Email can't same with Current Email.');
        window.location.href = 'profile.php?id={$user_id}';
        </script>";
        exit;
    }

    // Check if new email is already used by another user
    $checkEmailQuery = "SELECT * FROM user WHERE email = '$newEmail' AND user_id != '$user_id'";
    $checkResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Email already in use
        $_SESSION['update_error'] = true;
        echo "<script>
        alert('New Email have already exist, please try another email!');
        window.location.href = 'profile.php?id={$user_id}';
        </script>";
        exit;
    }

    // Passed all checks, proceed to update
    $updateEmail = "UPDATE user SET email = '$newEmail' WHERE user_id = '$user_id'";
    if (mysqli_query($conn, $updateEmail)) {
        $_SESSION['update_success'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    } else {
        $_SESSION['update_error'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    }
}


// Update Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePasswordBtn'])) {
    $old_password = $_POST['oldPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];

    // Get old password and compare
    $oldPassword = "SELECT password FROM user WHERE user_id = $user_id";
    $queryOldPassword = mysqli_query($conn, $oldPassword);
    if (!$queryOldPassword || mysqli_num_rows($queryOldPassword) == 0) {
        echo "<script>
        alert('Error: User not found');
        window.location.href = 'profile.php?id={$user_id}';
        </script>";
        exit;
    }

    $rowOldPassword = mysqli_fetch_assoc($queryOldPassword);
    $hashedOldPassword = $rowOldPassword['password'];
    if (!password_verify($old_password, $hashedOldPassword)) {
        $_SESSION['update_error'] = true;
        echo "<script>
        alert('Old password is not match.');
        window.location.href = 'profile.php?id={$user_id}';
        </script>";
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['update_error'] = true;
        echo "<script>
        alert('New Password and Confirm Password is not match.');
        window.location.href = 'profile.php?id={$user_id}';
        </script>";
        exit;
    }

    // 4. Hash new password and update DB
    $hashedNewPassword = password_hash($new_password, PASSWORD_DEFAULT);

    $updatePassword = "UPDATE user SET password = '$hashedNewPassword' WHERE user_id = $user_id";
    if (mysqli_query($conn, $updatePassword)) {

        $_SESSION['update_success'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    } else {
        $_SESSION['update_error'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile Settings</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets//css/systemStyle.css">

</head>

<body class="adminDash-body">

    <!-- Offcanvas Sidebar (mobile only) -->
    <div class="offcanvas offcanvas-start d-md-none text-bg-dark" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">Art & Print</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            <ul class="nav nav-pills flex-column">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                    <li class="nav-item">
                        <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> Manage Orders</a>
                    </li>
                    <li class="nav-item">
                        <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> Manage Quotations</a>
                    </li>
                <?php } ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") { ?>
                    <li class="nav-item">
                        <a href="customerDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="createOrder.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> Place Orders</a>
                    </li>
                    <li class="nav-item">
                        <a href="customerOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-clock-history"></i> History Orders</a>
                    </li>
                    <li class="nav-item">
                        <a href="createQuotation.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> Ask Quotations</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <!-- Static Sidebar (visible on md and up) -->
    <div id="sidebar" class="d-none d-md-flex flex-column p-3 sidebar">
        <div class="s_logo fs-5">
            <span>Art & Print</span>
        </div>
        <hr style="height: 2px; background-color: #FAFAFA; border: none;">
        <ul class="nav nav-pills flex-column">
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == "Admin" || $_SESSION['role'] == "Staff")) { ?>
                <li class="nav-item">
                    <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> <span>Manage Orders</span></a>
                </li>
                <li class="nav-item">
                    <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> <span>Manage Quotations</span></a>
                </li>
            <?php } ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") { ?>
                <li class="nav-item">
                    <a href="customerDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="createOrder.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> <span>Place Orders</span></a>
                </li>
                <li class="nav-item">
                    <a href="customerOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-clock-history"></i> <span>History Orders</span></a>
                </li>
                <li class="nav-item">
                    <a href="createQuotation.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> <span>Ask Quotation</span></a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light shadow-sm px-3 top-navbar fixed-top">
        <div class="container-fluid">

            <!-- mobile toggle btn -->
            <button class="btn toggle-btn d-block d-sm-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>

            <!-- desktop toggle btn -->
            <button class="btn toggle-btn d-none d-md-block" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <!-- User dropdown on the right -->
            <div class="d-flex align-items-center ms-auto">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                        <img src="data:<?php echo $rowShowUserInfo['img_type']; ?>;base64,<?php echo base64_encode($rowShowUserInfo['img_data']); ?>"
                            class="rounded-circle" width="30" height="30" alt="profile" />
                        <span style="color: #FAFAFA;"><?php echo $rowShowUserInfo['name']; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php?id=<?php echo $user_id; ?>">My Profile</a></li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                        <li><a class="dropdown-item" href="adminSettings.php?id=<?php echo $user_id; ?>">Settings</a></li>
                        <?php } ?>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Log out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <?php if ($showUpdateToast): ?>
            <!-- Update Successful Toast -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                <div id="updateToast" class="toast text-white bg-success border-0">
                    <div class="d-flex">
                        <div class="toast-body">
                            ✅ Record updated successfully!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($showUpdateErrorToast): ?>
            <!-- show Toast when error -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                <div id="updateErrorToast" class="toast text-white bg-danger border-0">
                    <div class="d-flex">
                        <div class="toast-body">
                            Error updating record
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4 fw-bold">
            <span>My Profile</span>
        </div>

        <div class="container mt-4 bg-white p-4 rounded shadow-sm">
            <img src="data:<?php echo $rowShowUserInfo['img_type']; ?>;base64,<?php echo base64_encode($rowShowUserInfo['img_data']); ?>" class=" rounded-circle" width="120" height="120" alt="profile" />
            <br>
            <button class="btn login-btn mt-3" data-bs-toggle="modal" data-bs-target="#changeProfileModal">Edit Picture</button>

            <!-- change profile picture modal -->
            <div class=" modal fade" id="changeProfileModal">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="changeProfileModalLabel">Change Profile Picture</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <form method="post">
                                <?php while ($rowShowImgList = mysqli_fetch_assoc($queryShowImgList)) { ?>
                                    <div class="mb-3">
                                        <input type="radio" name="updatePic" value="<?php echo $rowShowImgList['img_id']; ?>">
                                        <label for="<?php echo $rowShowImgList['img_name']; ?>"><?php echo $rowShowImgList['img_name']; ?></label>
                                        <img src="data:<?php echo $rowShowImgList['img_type']; ?>;base64,<?php echo base64_encode($rowShowImgList['img_data']); ?>"
                                            class="rounded-circle me-2" width="120" height="120" alt="profile" />
                                    </div>
                                <?php } ?>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn login-btn" name="updateProfileBtn">Save & Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <form method="post">
                <div class="mb-3">
                    <label for="updateName">Name:</label>
                    <input type="text" class="form-control" name="updateName" id="updateName" value="<?php echo $rowShowUserInfo['name']; ?>">
                </div>

                <div class="mb-3">
                    <label for="emailBtn">Email:</label><br>
                    <button type="button" name="emailBtn" class="btn change-email-btn form-control text-start" data-bs-toggle="modal" data-bs-target="#changeEmailModal">
                        <span><?php echo htmlspecialchars($rowShowUserInfo['email']); ?></span>
                    </button>
                </div>

                <div class="mb-3">
                    <label for="updatePhoneNumber">Mobile Number:</label>
                    <input type="text" class="form-control" name="updatePhoneNumber" id="updatePhoneNumber" value="<?php echo $rowShowUserInfo['phone_number']; ?>">
                </div>

                <button type="submit" class="btn login-btn mt-3" name="updateInfoBtn" id="updateInfoBtn" style="display: none;">Save & Changes</button>
            </form>

            <!-- Change Email Modal -->
            <div class="modal fade" id="changeEmailModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeEmailModalLabel">Change Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form method="post" id="changeEmailForm">
                            <div class="modal-body">
                                <label for="updateEmail">New Email Address:</label>
                                <input type="email" class="form-control mb-3" name="updateEmail" id="updateEmail" required>

                                <label for="code">Verification Code:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="code" name="code" required />
                                    <button type="button" class="btn btn-sm sendcode-btn" onclick="verifyCode(document.getElementById('updateEmail').value)" id="send_code_btn" disabled>
                                        Send code
                                    </button>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="updateEmailBtn" class="btn login-btn">Save Changes</button>
                            </div>
                        </form>

                        <script>
                            const emailInput = document.getElementById('updateEmail');
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
                            document.getElementById('changeEmailForm').addEventListener('submit', function(e) {
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
                    </div>
                </div>
            </div>

            <hr>

            <h6><strong>Password</strong></h6>
            <button class="btn login-btn" data-bs-toggle="modal" data-bs-target="#changePassModal">Change Password</button>

            <!-- change password modal -->
            <div class="modal fade" id="changePassModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="changePassModalLabel">Change Password</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <form action="" method="post">
                                <label for="oldPassword">Old Password:</label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
                                    <button class="btn bg-white text-muted" type="button" id="toggleOldPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>

                                <label for="newPassword">New Password:</label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                    <button class="btn bg-white text-muted" type="button" id="toggleNewPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>

                                <label for="confirmPassword">Confirm New Password</label>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                    <button class="btn bg-white text-muted" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn login-btn" name="updatePasswordBtn">Save & Changes</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script src="./assets/js/email.js"></script>

    <?php if ($showUpdateToast): ?>
        <script>
            const toast = new bootstrap.Toast(document.getElementById('updateToast'));
            toast.show();
        </script>
    <?php endif; ?>

    <?php if ($showUpdateErrorToast): ?>
        <script>
            const toast1 = new bootstrap.Toast(document.getElementById('updateErrorToast'));
            toast1.show();
        </script>
    <?php endif; ?>

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const topNavbar = document.getElementById('topNavbar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            topNavbar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    </script>

    <script>
        // Get references to input fields and button
        const nameInput = document.getElementById('updateName');
        const phoneInput = document.getElementById('updatePhoneNumber');
        const updateInfoBtn = document.getElementById('updateInfoBtn');

        // Group inputs into an array
        const inputs = [nameInput, phoneInput];

        // Store original values
        const originalValues = {};
        inputs.forEach(input => {
            originalValues[input.id] = input.value;
        });

        // Add event listener to check if any field changed
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                let changed = false;
                inputs.forEach(inp => {
                    if (inp.value !== originalValues[inp.id]) {
                        changed = true;
                    }
                });
                updateInfoBtn.style.display = changed ? 'inline-block' : 'none';
            });
        });
    </script>

    <script>
        // Toggle for old password field
        const toggleOldPasswordBtn = document.getElementById('toggleOldPassword');
        const oldPassword = document.getElementById('oldPassword');
        const oldIcon = toggleOldPasswordBtn.querySelector('i');

        toggleOldPasswordBtn.addEventListener('click', function() {
            const type = oldPassword.type === 'password' ? 'text' : 'password';
            oldPassword.type = type;
            oldIcon.classList.toggle('bi-eye');
            oldIcon.classList.toggle('bi-eye-slash');
        });

        // Toggle for new password field
        const toggleNewPasswordBtn = document.getElementById('toggleNewPassword');
        const newPassword = document.getElementById('newPassword');
        const newIcon = toggleNewPasswordBtn.querySelector('i');

        toggleNewPasswordBtn.addEventListener('click', function() {
            const type = newPassword.type === 'password' ? 'text' : 'password';
            newPassword.type = type;
            newIcon.classList.toggle('bi-eye');
            newIcon.classList.toggle('bi-eye-slash');
        });

        // Toggle for confirm password field
        const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
        const confirmpassword = document.getElementById('confirmPassword');
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