<?php
session_start();
include('dbms.php');

if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin" && $_SESSION['user_id'] == $_GET['user_id']) {
    $user_id = $_GET['user_id'];
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

$showOrderList = "SELECT * FROM `order` ORDER BY created_at desc";
$queryShowOrderList = mysqli_query($conn, $showOrderList) or die(mysqli_error($conn));

if (!$queryShowOrderList) {
    die("Query Failed: " . mysqli_error($conn));
}
if (mysqli_num_rows($queryShowOrderList) === 0) {
    echo "<tr><td colspan='7'>No orders found.</td></tr>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Responsive Sidebar Layout</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script src="./assets/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css">

    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>

    <style>
        body {
            overflow-x: hidden;
            background-color: #ffff;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: #343a40;
            color: white;
            transition: all 0.3s ease;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1030;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .s_logo {
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px 0;
        }

        .sidebar .nav-link {
            color: #ccc;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
            color: white;
            text-decoration: none;
        }

        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .s_logo span {
            display: none;
        }

        /* top navbar */
        .top-navbar {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
        }

        .top-navbar.collapsed {
            margin-left: 80px;
        }

        /* Main content */
        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
            padding: 1rem;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        /* mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(0);
                left: 0;
                transition: transform 0.3s ease;
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .top-navbar,
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Navigation -->
    <div id="sidebar" class="sidebar d-flex flex-column p-3">
        <div class="s_logo fs-5">
            <span>System Name</span>
        </div>
        <hr />
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="adminDashboard.php?user_id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a href="orderlist.html" class="nav-link"><i class="bi bi-card-list"></i><span>Manage Orders</span></a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3 top-navbar">
        <div class="container-fluid">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <!-- User dropdown on the right -->
            <div class="d-flex align-items-center ms-auto">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                        <img src="data:<?php echo $rowShowUserInfo['img_type']; ?>;base64,<?php echo base64_encode($rowShowUserInfo['img_data']); ?>"
                            class="rounded-circle" width="30" height="30" alt="profile" />
                        <span><?php echo $rowShowUserInfo['name']; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php?user_id=<?php echo $user_id; ?>">My Profile</a></li>
                        <li><a class="dropdown-item" href="adminSettings.php?user_id=<?php echo $user_id; ?>">Settings</a></li>
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
        <div class="container-fluid">
            <div class="mt-3 fw-bold">
                <span>Order List</span>
            </div>

            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="min-height: 80vh;">
                <br>
                <table id="orderList" class="table table-hover mt-3 mb-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Total Price</th>
                            <th>Customer Name</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rowShowOrderList = mysqli_fetch_assoc($queryShowOrderList)) { ?>
                            <tr>
                                <td>
                                    <a href="editOrder.php?order_id=<?php echo urlencode($rowShowOrderList['order_id']); ?>&user_id=<?php echo urlencode($user_id); ?>">
                                        <?php echo htmlspecialchars($rowShowOrderList['order_id']); ?>
                                    </a>
                                </td>
                                <td><?php echo $rowShowOrderList['total_price']; ?></td>
                                <td><?php echo $rowShowOrderList['customer_name']; ?></td>
                                <td>
                                    <?php if ($rowShowOrderList['order_status'] == "Pending") { ?>
                                        <span class="badge bg-warning text-white"><?php echo $rowShowOrderList['order_status']; ?></span>
                                    <?php } else if ($rowShowOrderList['order_status'] == "Completed") { ?>
                                        <span class="badge bg-success text-white"><?php echo $rowShowOrderList['order_status']; ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($rowShowOrderList['payment_status'] == "Pending") { ?>
                                        <span class="badge bg-warning text-white"><?php echo $rowShowOrderList['payment_status']; ?></span>
                                    <?php } else if ($rowShowOrderList['payment_status'] == "Paid") { ?>
                                        <span class="badge bg-success text-white"><?php echo $rowShowOrderList['payment_status']; ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php
                                    $date = new DateTime($rowShowOrderList['created_at']);
                                    echo $date->format('Y-m-d');
                                    ?>
                                </td>
                                <td>
                                    <a href="editOrder.php?order_id=<?php echo $rowShowOrderList['order_id']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-primary btn-sm me-2">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </main>

    <!-- JS Toggle Logic -->
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

        $('#orderList').DataTable({
            lengthChange: false,
            pageLength: 10, // Show 10 entries per page
            scrollY: '400px', // Set vertical scroll height
            scrollCollapse: true, // Collapse table height when fewer rows
            paging: true, // Enable pagination
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }],
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Add New Order',
                        className: 'btn btn-primary btn-sm',
                        action: function(e, dt, node, config) {
                            window.location.href = 'createorder.php?id=<?php echo $user_id; ?>';
                        }
                    }]
                }
            }
        });
    </script>
</body>

</html>