<?php
session_start();
include('dbms.php');

if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin" && $_SESSION['id'] == $_GET['id']) {
    $user_id = $_GET['id'];

    $showLoginToast = false;
    if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
        $showLoginToast = true;
        unset($_SESSION['login_success']); // Make sure toast shows only once
    }
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

$currentYear = date("Y");
$startDate = "$currentYear-01-01";
$endDate = "$currentYear-12-31";

// Initialize monthly arrays
$orderCounts = [];
$orderCosts = [];
$newUserCounts = [];

for ($i = 1; $i <= 12; $i++) {
    $monthKey = sprintf("%s-%02d", $currentYear, $i);
    $orderCounts[$monthKey] = 0;
    $orderCosts[$monthKey] = 0.0;
    $newUserCounts[$monthKey] = 0;
}

// === Query Order Count and Order Cost (combined) ===
$sqlSales = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS order_count, SUM(total_price) AS total_cost
            FROM `order`
            WHERE created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY month
            ORDER BY month ASC";

$resultSales = $conn->query($sqlSales);
if ($resultSales) {
    while ($rowSales = $resultSales->fetch_assoc()) {
        $month = $rowSales['month'];
        if (isset($orderCounts[$month])) {
            $orderCounts[$month] = (int)$rowSales['order_count'];
            $orderCosts[$month] = (float)$rowSales['total_cost'];
        }
    }
}

// === Query New Users Per Month ===
$sqlNewUsers = "SELECT DATE_FORMAT(create_date, '%Y-%m') AS month, COUNT(*) AS total
                FROM user
                WHERE create_date BETWEEN '$startDate' AND '$endDate'
                GROUP BY month
                ORDER BY month ASC";

$resultNewUsers = $conn->query($sqlNewUsers);
if ($resultNewUsers) {
    while ($row = $resultNewUsers->fetch_assoc()) {
        $month = $row['month'];
        if (isset($newUserCounts[$month])) {
            $newUserCounts[$month] = (int)$row['total'];
        }
    }
}

// === Query Service Type ===
$sqlServiceType = "SELECT service_desc, COUNT(*) AS count 
                   FROM order_detail
                   GROUP BY service_desc
                   ORDER BY count DESC";

$serviceLabels = [];
$serviceCounts = [];

$resultService = $conn->query($sqlServiceType);
if ($resultService) {
    while ($row = $resultService->fetch_assoc()) {
        $serviceLabels[] = $row['service_desc'];
        $serviceCounts[] = (int)$row['count'];
    }
}

// Prepare labels & data arrays for Chart.js
$labels = [];
$dataCounts = [];
$dataCosts = [];
$dataNewUsers = [];

foreach ($orderCounts as $month => $count) {
    $labels[] = date("M", strtotime($month));
    $dataCounts[] = $count;
    $dataCosts[] = round($orderCosts[$month], 2);
    $dataNewUsers[] = $newUserCounts[$month];
}

// For display (current month)
$currentMonth = date("Y-m");
$todayCount = $orderCounts[$currentMonth] ?? 0;
$todayCost = $orderCosts[$currentMonth] ?? 0.0;
$todayNewUsers = $newUserCounts[$currentMonth] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./adminStyle.css">

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
                <li class="nav-item">
                    <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> Manage Orders</a>
                </li>
                <li class="nav-item">
                    <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> Manage Quotations</a>
                </li>
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
            <li class="nav-item">
                <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> <span>Manage Orders</span></a>
            </li>
            <li class="nav-item">
                <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> <span>Manage Quotations</span></a>
            </li>
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

    <main id="mainContent" class="main-content">
        <div class="container-fluid">

            <?php if ($showLoginToast): ?>
                <!-- Login successful toast container -->
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                    <div id="successToast" class="toast text-white bg-success border-0">
                        <div class="d-flex">
                            <div class="toast-body">
                                <span>Login Successful! <br> Welcome to admin dashboard.</span>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-4 fw-bold">
                <span>Dashboard</span>
            </div>

            <div class="row">

                <!-- Today Sales -->
                <div class="col-md-3 mt-4 d-flex">
                    <div class="card h-100 w-100 custom-card">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <h5 class="card-title fs-6">Today Sales</h5>
                            <p class="card-text fs-2" id="todaySales">Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Today Order -->
                <div class="col-md-3 mt-4 d-flex">
                    <div class="card h-100 w-100 custom-card">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <h5 class="card-title fs-6">Today Orders</h5>
                            <p class="card-text fs-2" id="todayOrders">Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="col-md-3 mt-4 d-flex">
                    <div class="card h-100 w-100 custom-card">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <h5 class="card-title fs-6">Pending Orders</h5>
                            <p class="card-text fs-2" style="color: #FFC107;" id="pendingOrders">Loading...</p>
                            <a href="adminOrderlist.php?id=<?php echo $rowShowUserInfo['user_id']; ?>" class=" btn login-btn mt-3">Manage Orders</a>
                        </div>
                    </div>
                </div>

                <!-- Pending quotations -->
                <div class="col-md-3 mt-4 d-flex">
                    <div class="card h-100 w-100 custom-card">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <h5 class="card-title fs-6">Pending Quotations</h5>
                            <p class="card-text fs-2" style="color: #FFC107;" id="pendingQuotations">Loading...</p>
                            <a href="adminQuotationlist.php?id=<?php echo $rowShowUserInfo['user_id']; ?>" class="btn login-btn mt-3">Manage Quotations</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <!-- Sales and Orders Chart -->
                <div class="col-md-12 mt-4 d-flex">
                    <div class="card h-100 w-100">
                        <div class="card-body">
                            <h5 class="card-title fs-6">Sales & Orders in <?php echo $currentYear; ?></h5>
                            <p class="card-text fs-2">RM <?php echo number_format($todayCost, 2); ?></p>
                            <canvas id="orderCountSales" height="90"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <!-- New User Count Chart -->
                <div class="col-md-6 mt-4 d-flex">
                    <div class="card h-100 w-100">
                        <div class="card-body">
                            <h5 class="card-title fs-6">New Users in <?php echo date('F'); ?></h5>
                            <p class="card-text fs-2"><?php echo $todayNewUsers; ?></p>
                            <canvas id="newUserChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Service Type Pie Chart -->
                <div class="col-md-6 mt-4 d-flex">
                    <div class="card h-100 w-100">
                        <div class="card-body">
                            <h5 class="card-title fs-6">Service Types Distribution</h5>
                            <canvas id="serviceTypeChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($showLoginToast): ?>
        <script>
            const loginSuccessToast = new bootstrap.Toast(document.getElementById('successToast'));
            loginSuccessToast.show();
        </script>
    <?php endif; ?>

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const topbar = document.getElementById('topNavbar');
        const maincontent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            topbar.classList.toggle('collapsed');
            maincontent.classList.toggle('collapsed');
        });

        // Chart data from PHP
        const labels = <?php echo json_encode($labels); ?>;
        const orderCounts = <?php echo json_encode($dataCounts); ?>;
        const orderCosts = <?php echo json_encode($dataCosts); ?>;
        const newUsers = <?php echo json_encode($dataNewUsers); ?>;
        const serviceLabels = <?php echo json_encode($serviceLabels); ?>;
        const serviceCounts = <?php echo json_encode($serviceCounts); ?>;

        // --- Sales & Orders Combined Chart ---
        const ctxSalesOrders = document.getElementById('orderCountSales').getContext('2d');
        new Chart(ctxSalesOrders, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Order Count',
                        data: orderCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Sales (RM)',
                        data: orderCosts,
                        type: 'line',
                        borderColor: 'rgba(255, 99, 132, 0.8)',
                        backgroundColor: 'rgba(255, 99, 132, 0.4)',
                        yAxisID: 'y2',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                scales: {
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Orders'
                        },
                        beginAtZero: true,
                    },
                    y2: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Sales (RM)'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                },
                plugins: {
                    legend: {
                        labels: {
                            boxWidth: 15,
                            padding: 10
                        }
                    }
                }
            }
        });

        // --- New User Chart ---
        const ctxNewUsers = document.getElementById('newUserChart').getContext('2d');
        new Chart(ctxNewUsers, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'New Users',
                    data: newUsers,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Users'
                        }
                    }
                }
            }
        });

        // --- Service Type Pie Chart ---
        const ctxServiceType = document.getElementById('serviceTypeChart').getContext('2d');
        new Chart(ctxServiceType, {
            type: 'pie',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceCounts,
                    backgroundColor: [
                        '#007bff',
                        '#dc3545',
                        '#ffc107',
                        '#28a745',
                        '#6f42c1',
                        '#fd7e14',
                        '#20c997',
                        '#17a2b8'
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        async function fetchPendingCounts() {
            try {
                const response = await fetch('checkPendingData.php');
                const data = await response.json();

                document.getElementById('todaySales').textContent = data.today_sales ?? 0;
                document.getElementById('todayOrders').textContent = data.today_orders ?? 0;
                document.getElementById('pendingOrders').textContent = data.pending_orders ?? 0;
                document.getElementById('pendingQuotations').textContent = data.pending_quotations ?? 0;
            } catch (error) {
                console.error('Failed to fetch pending counts:', error);
            }
        }

        fetchPendingCounts();
        setInterval(fetchPendingCounts, 10000); // 1000 = 1 second
    </script>
</body>

</html>