<?php
session_start();
// Include your database connection
include 'dbms.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer" && $_SESSION['id'] == $_GET['id']) {
    $order_id = $_GET['order_id'];
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

// Fetch order info
$sql_order = "SELECT * FROM `order` WHERE order_id = ? AND customer_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("si", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order = $result_order->fetch_assoc();

// Fetch order_detail info
$sql_detail = "SELECT * FROM `order_detail` WHERE order_id = ?";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("s", $order_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$order_detail = $result_detail->fetch_assoc();

// Fetch file info
$sql_file = "SELECT * FROM `file` WHERE order_id = ?";
$stmt_file = $conn->prepare($sql_file);
$stmt_file->bind_param("s", $order_id);
$stmt_file->execute();
$result_file = $stmt_file->get_result();
$file = $result_file->fetch_assoc();

// Preload service list
$service_options = [];
$sql_service = "SELECT service_id, service_desc FROM service_list WHERE service_status = 'Available' AND service_type = 'print'";
$result_service = mysqli_query($conn, $sql_service);
while ($row = mysqli_fetch_assoc($result_service)) {
    $service_options[$row['service_id']] = $row['service_desc'];
}

// Preload paper size list
$size_options = [];
$sql_size = "SELECT service_id, service_desc FROM service_list WHERE service_status = 'Available' AND service_type = 'size'";
$result_size = mysqli_query($conn, $sql_size);
while ($row = mysqli_fetch_assoc($result_size)) {
    $size_options[$row['service_id']] = $row['service_desc'];
}

// Preload finishing list
$finishing_options = [];
$sql_finishing = "SELECT finishing_id, finishing_desc FROM finishing_list WHERE finishing_status = 'Available'";
$result_finishing = mysqli_query($conn, $sql_finishing);
while ($row = mysqli_fetch_assoc($result_finishing)) {
    $finishing_options[$row['finishing_id']] = $row['finishing_desc'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Same head as your createorder.php -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
            <div class="mt-4 fw-bold">
                <span>Order Details</span>
            </div>

            <!-- Order Form (readonly) -->
            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="max-height: 80vh; overflow-y: auto;">
                <!-- Customer Name -->
                <div class="mb-4">
                    <label class="form-label">Name:</label>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin" && $_SESSION['id'] == $_GET['id']) { ?>
                        <?php
                        $customerInfoId = $order['customer_id'];
                        $customerInfo = "SELECT user_id, name, email, phone_number FROM user
                        WHERE user_id = '$customerInfoId'";
                        $queryCustomerInfo = mysqli_query($conn, $customerInfo) or die(mysqli_error($conn));
                        $rowCustomerInfo = mysqli_fetch_assoc($queryCustomerInfo); ?>

                        <button type="button" name="customerInfo" class="btn change-email-btn form-control text-start" data-bs-toggle="modal" data-bs-target="#customerInfoModal">
                            <span><?php echo htmlspecialchars($rowShowUserInfo['name']); ?></span>
                        </button>

                        <!-- Customer Info Modal -->
                        <div class="modal fade" id="customerInfoModal">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Customer Info</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="customerinfoid">User ID:</label>
                                            <input type="text" class="form-control" value="<?php echo $rowCustomerInfo['user_id'] ?>" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label for="customerinfoname">Name:</label>
                                            <input type="text" class="form-control" value="<?php echo $rowCustomerInfo['name'] ?>" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label for="customerinfoemail">Email:</label>
                                            <input type="text" class="form-control" value="<?php echo $rowCustomerInfo['email'] ?>" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label for="customerinfophonenumber">Phone Number:</label>
                                            <input type="text" class="form-control" value="<?php echo $rowCustomerInfo['phone_number'] ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($order['customer_name']); ?>" disabled>
                    <?php } ?>
                </div>

                <!-- Uploaded File -->
                <div class="mb-4">
                    <label class="form-label">Uploaded File:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($file['file_name']); ?>" disabled>
                </div>

                <!-- Type of Services -->
                <div class="mb-4">
                    <label class="form-label">Type Of Services:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($order_detail['service_desc']); ?>" disabled>
                </div>

                <!-- Paper Size -->
                <div class="mb-4">
                    <label class="form-label">Paper Size:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($order_detail['size']); ?>" disabled>
                </div>

                <!-- Print Colour -->
                <div class="mb-4">
                    <label class="form-label">Print Colour:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($order_detail['colour']); ?>" disabled>
                </div>

                <!-- Number of Copies -->
                <div class="mb-4">
                    <label class="form-label">Number of Copies:</label>
                    <input class="form-control" type="number" value="<?php echo htmlspecialchars($order_detail['copies']); ?>" disabled>
                </div>

                <!-- Number of Pages -->
                <div class="mb-4">
                    <label class="form-label">Number of Pages:</label>
                    <input class="form-control" type="number" value="<?php echo htmlspecialchars($order_detail['pages']); ?>" disabled>
                </div>

                <!-- Service Cost -->
                <div class="mb-4">
                    <label class="form-label">Service Cost:</label>
                    <input class="form-control" type="text" value="RM <?php echo number_format($order_detail['service_total_price'], 2); ?>" disabled>
                </div>

                <!-- Finishing 1/2/3 -->
                <div class="mb-4">
                    <label class="form-label">Finishing 1:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($order_detail['finishing_desc1']); ?>" disabled>
                </div>

                <div class="mb-4">
                    <label class="form-label">Finishing 2:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($order_detail['finishing_desc2']); ?>" disabled>
                </div>

                <div class="mb-4">
                    <label class="form-label">Finishing 3:</label>
                    <input class="form-control" type="text" value="<?php echo htmlspecialchars($order_detail['finishing_desc3']); ?>" disabled>
                </div>

                <!-- Total Cost -->
                <div class="mb-4">
                    <label class="form-label">Total Cost:</label>
                    <input class="form-control" type="text" value="RM <?php echo number_format($order['total_price'], 2); ?>" disabled>
                </div>

                <!-- Remarks -->
                <div class="mb-5">
                    <label class="form-label">Remarks:</label>
                    <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($order_detail['remarks']); ?></textarea>
                </div>

                <!-- Back button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="customerOrderlist.php?id=<?php echo urlencode($user_id); ?>" class="btn login-btn">Back</a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar toggle logic -->
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
</body>

</html>