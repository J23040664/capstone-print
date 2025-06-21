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

// Update name
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateNameBtn'])) {
    $newName = $_POST['updateName'];

    $updateName = "UPDATE user SET name = '$newName' WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $updateName)) {

        $_SESSION['update_success'] = true;
        header("Location: profile.php?id={$user_id}");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
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
        echo "<script>
        alert('Old password is not match.');
        window.location.href = 'profile.php';
        </script>";
        exit;
    }

    if ($new_password !== $confirm_password) {
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
        echo "Error updating password: " . mysqli_error($conn);
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

    <link rel="stylesheet" href="./adminStyle.css">

</head>

<body class="adminDash-body">

    <!-- Sidebar Navigation -->
    <div id="sidebar" class="d-flex flex-column p-3 sidebar">
        <div class="s_logo fs-5">
            <span>Art & Print</span>
        </div>
        <hr style="height: 4px; background-color: #FAFAFA; border: none;">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i><span>Manage Orders</span></a>
            </li>
            <li class="nav-item">
                <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i><span>Manage Quotations</span></a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light shadow-sm px-3 top-navbar">
        <div class="container-fluid">
            <button class="btn toggle-btn" id="toggleSidebar">
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
                        <li><a class="dropdown-item" href="adminSettings.php?id=<?php echo $user_id; ?>">Settings</a></li>
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
                <label for="updateName">Name:</label>
                <input type="text" class="form-control" name="updateName" value="<?php echo $rowShowUserInfo['name']; ?>">
                <button type="submit" class="btn login-btn mt-3" name="updateNameBtn">Save & Changes</button>
            </form>

            <hr>

            <h6>Password</h6>
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

    <?php if ($showUpdateToast): ?>
        <script>
            const toast = new bootstrap.Toast(document.getElementById('updateToast'));
            toast.show();
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