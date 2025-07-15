<?php
session_start();
include('dbms.php'); // your DB connection

if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer" && $_SESSION['id'] == $_GET['id']) {
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

// Fetch orders for this customer
$sql = "SELECT 
            o.order_id,
            o.created_at AS order_date,
            o.order_status,
            o.payment_status,
            o.total_price,
            s.service_desc
        FROM `order` o
        JOIN `order_detail` od ON o.item_id = od.item_id
        JOIN `service_list` s ON od.service_id = s.service_id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Orders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="./assets/css/systemStyle.css">
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
                    <a href="customerDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="createOrder.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> Place Orders</a>
                </li>
                <li class="nav-item">
                    <a href="customerOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-clock-history"></i> History Orders</a>
                </li>
                <li class="nav-item">
                    <a href="createQuotation.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> Ask Quotation</a>
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
            <h3 class="fw-bold mb-4">My Orders</h3>
            <div class="bg-white p-4 rounded shadow-sm">
                <?php if ($result->num_rows > 0): ?>
                    <!-- Order Table -->
                    <div class="table-responsive">
                        <table id="customerOrderlist" class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date Placed</th>
                                    <th>Price</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th class="text-start">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($row['order_id']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
                                        <td>RM <?php echo htmlspecialchars($row['total_price']); ?></td>
                                        <td>
                                            <?php
                                            $order_status = $row['order_status'];
                                            $order_badge_class =
                                                ($order_status == 'Completed') ? 'status-completed text-dark' : 
                                                (($order_status == 'Pending') ? 'status-pending text-dark' : 
                                                (($order_status == 'In Progress') ? 'status-in-progress text-dark' : 
                                                (($order_status == 'Ready To Collect') ? 'status-ready-to-collect text-dark' : 
                                                (($order_status == 'Cancelled') ? 'status-cancelled text-dark' :
                                                                'bg-primary text-white'))));
                                            ?>
                                            <span class="badge <?php echo $order_badge_class; ?>"><?php echo htmlspecialchars($order_status); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $payment_status = $row['payment_status'];
                                            $payment_badge_class = ($payment_status == 'Paid') ? 'status-completed text-dark' : 'status-pending text-dark';
                                            ?>
                                            <span class="badge <?php echo $payment_badge_class; ?>"><?php echo htmlspecialchars($payment_status); ?></span>
                                        </td>
                                        <td class="text-start">
                                            <div class="d-flex gap-2">
                                                <a href="orderDetails.php?order_id=<?php echo urlencode($row['order_id']); ?>&id=<?php echo urlencode($user_id); ?>" class="btn login-btn btn-sm me-2">
                                                    <i class="bi bi-eye"></i> View
                                                </a>

                                                <?php if ($payment_status == 'Pending'): ?>
                                                    <a href="payment.php?order_id=<?php echo urlencode($row['order_id']); ?>&id=<?php echo urlencode($user_id); ?>" class="btn btn-success btn-sm">
                                                        <i class="bi bi-credit-card"></i> Pay Now
                                                    </a>
                                                <?php endif; ?>
                                            </div>
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
                        <a href="createOrder.php?id=<?php echo urlencode($user_id); ?>" class="btn login-btn mt-3">
                            <i class="bi bi-plus-circle"></i> Create New Order
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>

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

        $('#customerOrderlist').DataTable({
            lengthChange: false,
            ordering: false,
            paging: false,
            dom: '<"d-flex justify-content-between align-items-center mb-3"fB>t<"d-flex justify-content-between mt-2"ip>',
            buttons: [{
                text: '<i class="bi bi-plus-circle"></i> Create New Order',
                className: 'btn orderlist-btn btn-sm',
                action: function() {
                    window.location.href = 'createOrder.php?id=<?php echo urlencode($user_id); ?>';
                }
            }]
        });
    </script>

</body>

</html>