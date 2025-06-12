<?php
include('dbms.php');

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

// === Query Order Count ===
$sqlCount = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
             FROM `order`
             WHERE created_at BETWEEN '$startDate' AND '$endDate'
             GROUP BY month
             ORDER BY month ASC";

$resultCount = $conn->query($sqlCount);
if ($resultCount) {
    while ($row = $resultCount->fetch_assoc()) {
        $month = $row['month'];
        if (isset($orderCounts[$month])) {
            $orderCounts[$month] = (int)$row['total'];
        }
    }
}

// === Query Order Cost ===
$sqlCost = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_price) AS total_cost
            FROM `order`
            WHERE created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY month
            ORDER BY month ASC";

$resultCost = $conn->query($sqlCost);
if ($resultCost) {
    while ($row = $resultCost->fetch_assoc()) {
        $month = $row['month'];
        if (isset($orderCosts[$month])) {
            $orderCosts[$month] = (float)$row['total_cost'];
        }
    }
}

// === Query New Users Per Month (Fix: Full Year) ===
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

    <style>
        /* Your CSS unchanged */
        body {
            overflow-x: hidden;
            background-color: #fff;
        }

        /* Sidebar styling */
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

        /* Top navbar positioning */
        .top-navbar {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
        }

        .top-navbar.collapsed {
            margin-left: 80px;
        }

        /* Main content positioning */
        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
            padding: 1rem;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        /* Responsive adjustments */
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
                <a href="dashboard.html" class="nav-link"><i class="bi bi-house"></i><span>Dashboard</span></a>
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
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <img src="./assets/icon/userpicture.png" class="rounded-circle" width="30" height="30"
                            alt="profile_picture" />
                        <span>abc</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">My Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item text-danger" href="login.html">Log out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main id="mainContent" class="main-content">
        <div class="container-fluid">
            <div class="mt-3 fw-bold">
                <span>Dashboard</span>

                <div class="row">
                    <!-- Pending Orders -->
                    <div class="col-md-6 mt-4 d-flex">
                        <div class="card h-100 w-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title fs-6">Pending Order</h5>
                                <p class="card-text fs-2" id="pendingOrders">Loading...</p>
                                <a href="orderlist.html" class="btn btn-primary mt-3">Manage Orders</a>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Orders -->
                    <div class="col-md-6 mt-4 d-flex">
                        <div class="card h-100 w-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title fs-6">Pending Order</h5>
                                <p class="card-text fs-2" id="">Loading...</p>
                                <a href="orderlist.html" class="btn btn-primary mt-3">Manage Orders</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Cost -->
                <div class="col-12 mt-4 d-flex">
                    <div class="card h-100 w-100">
                        <div class="card-body">
                            <h5 class="card-title fs-6">Sales of <?php echo $currentYear; ?></h5>
                            <p class="card-text fs-2"><?php echo $todayCost; ?></p>
                            <canvas id="orderCostChart" height="100" class="mt-4 w-100"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Order Count -->
                    <div class="col-md-6 mt-4 d-flex">
                        <div class="card h-100 w-100">
                            <div class="card-body">
                                <h5 class="card-title fs-6">Order of <?php echo $currentYear; ?></h5>
                                <p class="card-text fs-2"><?php echo $todayCount; ?></p>
                                <canvas id="orderCountChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- New User Count -->
                    <div class="col-md-6 mt-4 d-flex">
                        <div class="card h-100 w-100">
                            <div class="card-body">
                                <h5 class="card-title fs-6">New User of <?php echo date('M'); ?></h5>
                                <p class="card-text fs-2"><?php echo $todayNewUsers; ?></p>
                                <canvas id="newUserChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Service Type Pie Chart -->
                    <div class="col-md-6 mt-4 d-flex">
                        <div class="card h-100 w-100">
                            <div class="card-body">
                                <h5 class="card-title fs-6">Service Type</h5>
                                <canvas id="serviceTypeChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- New User Count -->
                    <div class="col-md-6 mt-4 d-flex">
                        <div class="card h-100 w-100">
                            <div class="card-body">
                                <h5 class="card-title fs-6">New User of <?php echo date('M'); ?></h5>
                                <p class="card-text fs-2"><?php echo $todayNewUsers; ?></p>
                                <canvas id="newUserChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </main>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Sidebar toggle logic
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const topNavbar = document.getElementById('topNavbar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            topNavbar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });

        // Chart data from PHP
        const labels = <?php echo json_encode($labels); ?>;
        const orderCounts = <?php echo json_encode($dataCounts); ?>;
        const orderCosts = <?php echo json_encode($dataCosts); ?>;
        const newUsers = <?php echo json_encode($dataNewUsers); ?>;
        const serviceLabels = <?php echo json_encode($serviceLabels); ?>;
        const serviceCounts = <?php echo json_encode($serviceCounts); ?>;

        // Order Count Chart
        const ctxOrderCount = document.getElementById('orderCountChart').getContext('2d');
        new Chart(ctxOrderCount, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Orders',
                    data: orderCounts,
                    backgroundColor: '#42a5f5',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });

        // Order Cost Chart
        const ctxOrderCost = document.getElementById('orderCostChart').getContext('2d');
        new Chart(ctxOrderCost, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales (RM)',
                    data: orderCosts,
                    fill: false,
                    borderColor: '#66bb6a',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // New User Chart
        const ctxNewUser = document.getElementById('newUserChart').getContext('2d');
        new Chart(ctxNewUser, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'New Users',
                    data: newUsers,
                    backgroundColor: '#ffa726',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });

        // Service Type Pie Chart
        const ctxService = document.getElementById('serviceTypeChart').getContext('2d');
        const colors = [
            '#EF476F', '#FFD166', '#06D6A0', '#118AB2', '#073B4C', '#FF9F1C',
            '#6A4C93', '#8F3985', '#D81159', '#218380'
        ];
        new Chart(ctxService, {
            type: 'pie',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceCounts,
                    backgroundColor: colors.slice(0, serviceLabels.length)
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Update pending orders count every 5 seconds
        async function fetchPendingOrders() {
            try {
                const response = await fetch('checkPendingOrder.php');
                const data = await response.json();
                const pendingOrdersElement = document.getElementById('pendingOrders');
                pendingOrdersElement.textContent = data.pending_orders ?? 0;
            } catch (error) {
                console.error('Failed to fetch pending orders:', error);
            }
        }

        fetchPendingOrders();
        setInterval(fetchPendingOrders, 5000);
    </script>
</body>

</html>