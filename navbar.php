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
                        <a href="dashboard.html" class="nav-link text-white d-flex align-items-center">
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
                                <img src="./assets/icon/userpicture.png" class="rounded-circle"
                                    width="30" height="30" alt="profile" />
                                <span>abc</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.html">My Profile</a></li>
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