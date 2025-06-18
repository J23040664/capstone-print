<?php
include('dbms.php'); // your DB connection

// Get customer_id from session
$customer_id = $_GET['id'];

// Fetch orders for this customer
$sql = "SELECT 
            o.order_id,
            o.created_at AS order_date,
            o.order_status,
            o.payment_status,
            s.service_desc
        FROM `order` o
        JOIN `order_detail` od ON o.item_id = od.item_id
        JOIN `service_list` s ON od.service_id = s.service_id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Orders</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery & DataTables -->
    <script src="./assets/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css">

    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>

    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1030;
            transition: all 0.3s ease;
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

        /* Top Navbar */
        .top-navbar {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
        }

        .top-navbar.collapsed {
            margin-left: 80px;
        }

        /* Main Content */
        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
            padding: 1rem;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(0);
                left: 0;
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .top-navbar,
            .main-content {
                margin-left: 0;
            }
        }

        /* Table Enhancements */
        #example th,
        #example td {
            text-align: center;
            vertical-align: middle;
        }

        .badge {
            font-size: 0.85rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn-sm i {
            margin-right: 5px;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar d-flex flex-column p-3">
        <div class="s_logo fs-5">
            <span>PrintEase</span>
        </div>
        <hr />
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="dashboard.html" class="nav-link"><i class="bi bi-house"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a href="orderlist.html" class="nav-link active"><i class="bi bi-card-list"></i><span>My Orders</span></a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3 top-navbar">
        <div class="container-fluid">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center ms-auto">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <img src="./assets/icon/userpicture.png" class="rounded-circle" width="30" height="30" alt="profile_picture" />
                        <span>Customer</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.html">My Profile</a></li>
                        <li><a class="dropdown-item" href="settings.html">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item text-danger" href="login.html">Log out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-4">My Orders</h3>

            <div class="bg-white p-4 rounded shadow-sm">



                <?php if ($result->num_rows > 0): ?>
                    <!-- Order Table -->
                    <div class="table-responsive">
                        <table id="example" class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Service Type</th>
                                    <th>Order Date</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['service_desc']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
                                        <td>
                                            <?php
                                                $order_status = $row['order_status'];
                                                $order_badge_class = ($order_status == 'Completed') ? 'bg-success' : (($order_status == 'Pending') ? 'bg-warning text-dark' : 'bg-info text-dark');
                                            ?>
                                            <span class="badge <?php echo $order_badge_class; ?>"><?php echo htmlspecialchars($order_status); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                                $payment_status = $row['payment_status'];
                                                $payment_badge_class = ($payment_status == 'Paid') ? 'bg-success' : 'bg-warning text-dark';
                                            ?>
                                            <span class="badge <?php echo $payment_badge_class; ?>"><?php echo htmlspecialchars($payment_status); ?></span>
                                        </td>
                                        <td>
                                            <a href="http://localhost/capstone-print/orderdetails.php?order_id=<?php echo urlencode($row['order_id']); ?>&id=<?php echo urlencode($customer_id); ?>" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="bi bi-eye"></i> View
                                            </a>

                                            <?php if ($payment_status == 'Pending'): ?>
                                            <a href="payment.php?order_id=<?php echo urlencode($row['order_id']); ?>&user_id=<?php echo urlencode($customer_id); ?>" class="btn btn-success btn-sm">
                                                    <i class="bi bi-credit-card"></i> Pay Now
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- No order history message -->
                    <div class="text-center py-5">
                        <i class="bi bi-folder-x" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="mt-3 fs-5 text-muted">You have no order history yet.</p>
                        <a href="http://localhost/capstone-print/createOrder.php?id=<?php echo urlencode($customer_id); ?>" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Create New Order
                        </a>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </main>

    <!-- JS Toggle + DataTable Init -->
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

        $('#example').DataTable({
            lengthChange: false,
            ordering: false,
            dom: '<"d-flex justify-content-between align-items-center mb-3"fB>t<"d-flex justify-content-between mt-2"ip>',
            buttons: [
                {
                    text: '<i class="bi bi-plus-circle"></i> Create New Order',
                    className: 'btn btn-primary',
                    action: function () {
                        window.location.href = 'http://localhost/capstone-print/createOrder.php?id=<?php echo urlencode($customer_id); ?>';
                    }
                }
            ]
        });

    </script>

    </body>
</html>