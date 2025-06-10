<?php
include('dbms.php');
session_start();

if (isset($_SESSION['user_id']) && $_GET['id'] == $_SESSION['user_id']) {
    $user_id = $_SESSION['user_id'];

} else {
    header("Location: login.php");
    exit;
}

// show the image
$showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
$queryShowUserInfor = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
$rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfor);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $pageTitle ?? 'Art & Print'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <!-- sidebar -->
            <div class="col-md-2 d-none d-md-block bg-dark text-white vh-100 p-3">
                <ul class="nav nav-pills flex-column">

                    <hr>

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link text-white d-flex align-items-center">
                            <i class="bi bi-house me-2"></i><span>Dashboard</span>
                        </a>
                    </li>

                    <hr>

                    <li class="nav-item">
                        <a href="orderlist.html" class="nav-link text-white d-flex align-items-center">
                            <i class="bi bi-card-list me-2"></i><span>Manage Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="quotationlist.html" class="nav-link text-white d-flex align-items-center">
                            <i class="bi bi-question-circle me-2"></i><span>Manage Quotation</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sidebar for small screens -->
            <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="sidebar" style="width: 300px;">
                <div class="offcanvas-header">
                    <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-2">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="dashboard.html" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-house me-2"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="orderlist.html" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-card-list me-2"></i><span>Manage Orders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quotationlist.html" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-question-circle me-2"></i><span>Manage Quotation</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-10 col-12">

                <!-- Topbar -->
                <nav class="navbar navbar-expand-lg bg-white shadow-sm px-3 sticky-top">
                    <div class="container-fluid">
                        <button class="btn btn-outline-secondary d-md-none" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebar" aria-label="Toggle sidebar" title="Toggle menu">
                            <i class="bi bi-list"></i>
                        </button>

                        <div class="dropdown ms-auto">
                            <button class="btn dropdown-toggle d-flex align-items-center gap-2"
                                data-bs-toggle="dropdown">
                                <img src="data:<?php echo $rowShowUserInfo['img_type']; ?>;base64,<?php echo base64_encode($rowShowUserInfo['img_data']); ?>"
                                    class="rounded-circle" width="30" height="30" alt="profile" />
                                <span><?php echo $rowShowUserInfo['name']; ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <p class="text-center justify-content-center">User ID: <strong><?php echo $rowShowUserInfo['user_id']; ?></strong></p>
                                <hr>
                                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                <li><a class="dropdown-item" href="settings.html">Settings</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Log out</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="py-4 px-3">
                    <!-- continue content at another file, can refer dashboard -->