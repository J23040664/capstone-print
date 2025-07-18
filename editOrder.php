<?php
session_start();
include('dbms.php');

$email = $_SESSION['email'] ?? null;
$order_id = $_SESSION['order_id'] ?? null;
$customer_name = $_SESSION['customer_name'] ?? null;
$order_status = $_SESSION['order_status'] ?? null;

// Clear session variables after reading (optional)
unset($_SESSION['email'], $_SESSION['order_id'], $_SESSION['customer_name'], $_SESSION['order_status']);

if (isset($_SESSION['role']) && ($_SESSION['role'] == "Admin" || $_SESSION['role'] == "Staff") && $_SESSION['id'] == $_GET['id']) {

    $user_id = $_GET['id'];
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'];
$showOrderDetails = "SELECT a.*, b.*, c.*
FROM `order` a 
LEFT JOIN order_detail b 
ON a.order_id = b.order_id
LEFT JOIN `file` c
ON b.file_id = c.file_id
WHERE a.order_id = '$order_id'";
$queryShowOrderDetails = mysqli_query($conn, $showOrderDetails) or die(mysqli_error($conn));

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['viewFileBtn'])) {
    $file_id = $_POST['viewFileId'];
    $query = "SELECT file_name, file_type, file_path FROM `file` WHERE file_id = '$file_id'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        header("Content-Type: application/{$row['file_type']}");
        header("Content-Disposition: inline; filename=\"" . $row['file_name'] . "\"");
        echo $row['file_path'];
        exit;
    } else {
        echo "File not found.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editOrderBtn'])) {
    $orderStatus = $_POST['orderStatus'];
    if ($orderStatus === 'Completed') {
        $updateOrderStatus = "UPDATE `order` 
        SET order_status = '$orderStatus', completed_date = NOW()
        WHERE order_id = '$order_id'";
    } else {
        $updateOrderStatus = "UPDATE `order` 
        SET order_status = '$orderStatus'
        WHERE order_id = '$order_id'";
    }

    if (mysqli_query($conn, $updateOrderStatus)) {
        // find customer email
        $sqlCustomerInfo = "SELECT a.customer_id, a.customer_name, a.order_id, a.order_status, b.email
        FROM `order` a
        LEFT JOIN user b 
        ON a.customer_id = b.user_id
        WHERE a.order_id = '$order_id'";
        $queryCustomerInfo = mysqli_query($conn, $sqlCustomerInfo) or die(mysqli_error($conn));
        $rowCustomerInfo = mysqli_fetch_assoc($queryCustomerInfo);

        $cus_email = $rowCustomerInfo['email'];
        $cus_order_id = $rowCustomerInfo['order_id'];
        $cus_name = $rowCustomerInfo['customer_name'];
        $cus_order_status = $rowCustomerInfo['order_status'];

        $_SESSION['email'] = $cus_email;
        $_SESSION['order_id'] = $cus_order_id;
        $_SESSION['customer_name'] = $cus_name;
        $_SESSION['order_status'] = $cus_order_status;

        echo "<script>
            alert('Update Order Status Successfully!');
            window.location.href = 'editOrder.php?order_id=$order_id&id=$user_id';
        </script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Order</title>

    <!-- Bootstrap and Bootstrap Icons CSS -->
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

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <div class="container-fluid">
            <div class="mt-4 fw-bold">
                <span>Order Details</span>
            </div>

            <!-- Order Form -->
            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="max-height: 80vh; overflow-y: auto;">
                <?php while ($rowShowOrderDetails = mysqli_fetch_assoc($queryShowOrderDetails)) { ?>

                    <!-- Customer Name -->
                    <div class="mb-4">
                        <label class="form-label">Name:</label>
                        <?php
                        $customerInfoId = $rowShowOrderDetails['customer_id'];
                        $customerInfo = "SELECT user_id, name, email, phone_number FROM user
                        WHERE user_id = '$customerInfoId'";
                        $queryCustomerInfo = mysqli_query($conn, $customerInfo) or die(mysqli_error($conn));
                        $rowCustomerInfo = mysqli_fetch_assoc($queryCustomerInfo); ?>

                        <button type="button" name="customerInfo" class="btn change-email-btn form-control text-start" data-bs-toggle="modal" data-bs-target="#customerInfoModal">
                            <span><?php echo htmlspecialchars($rowCustomerInfo['name']); ?></span>
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
                    </div>

                    <!-- upload file -->
                    <div class="mb-4">
                        <label class="form-label">Upload Reference File (optional)</label>
                        <?php if (!empty($rowShowOrderDetails['file_path'])): ?>
                            <form method="post" target="_blank">
                                <input type="hidden" class="form-control" name="viewFileId" value="<?php echo htmlspecialchars($rowShowOrderDetails['file_id']); ?>">
                                <button type="submit" class="btn login-btn w-20" name="viewFileBtn">View File</button>
                            </form>
                        <?php else: ?>
                            <input type="text" class="form-control" value="No file uploaded" disabled>
                        <?php endif; ?>
                    </div>

                    <!-- Type of Services -->
                    <div class="mb-4">
                        <label class="form-label">Type Of Services:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($rowShowOrderDetails['service_desc']); ?>" disabled>
                    </div>

                    <!-- Paper Size -->
                    <div class="mb-4">
                        <label class="form-label">Paper Size:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($rowShowOrderDetails['size']); ?>" disabled>
                    </div>

                    <!-- Print Colour -->
                    <div class="mb-4">
                        <label class="form-label">Print Colour:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($rowShowOrderDetails['colour']); ?>" disabled>
                    </div>

                    <!-- Number of Copies -->
                    <div class="mb-4">
                        <label class="form-label">Number of Copies:</label>
                        <input class="form-control" type="number" value="<?php echo htmlspecialchars($rowShowOrderDetails['copies']); ?>" disabled>
                    </div>

                    <!-- Number of Pages -->
                    <div class="mb-4">
                        <label class="form-label">Number of Pages:</label>
                        <input class="form-control" type="number" value="<?php echo htmlspecialchars($rowShowOrderDetails['pages']); ?>" disabled>
                    </div>

                    <!-- Service Cost -->
                    <div class="mb-4">
                        <label class="form-label">Service Cost:</label>
                        <input class="form-control" type="text" value="RM <?php echo number_format($rowShowOrderDetails['service_total_price'], 2); ?>" disabled>
                    </div>

                    <!-- Finishing 1/2/3 -->
                    <div class="mb-4">
                        <label class="form-label">Finishing 1:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($rowShowOrderDetails['finishing_desc1']); ?>" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Finishing 2:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($rowShowOrderDetails['finishing_desc2']); ?>" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Finishing 3:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($rowShowOrderDetails['finishing_desc3']); ?>" disabled>
                    </div>

                    <!-- Total Cost -->
                    <div class="mb-4">
                        <label class="form-label">Total Cost:</label>
                        <input class="form-control" type="text" value="RM <?php echo number_format($rowShowOrderDetails['total_price'], 2); ?>" disabled>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-5">
                        <label class="form-label">Remarks:</label>
                        <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($rowShowOrderDetails['remarks']); ?></textarea>
                    </div>

                    <hr>

                    <!-- update the order status -->
                    <form method="post">
                        <div class="mb-5 mt-2">
                            <label for="orderStatus" class="form-label">Order Status:</label>
                            <select class="form-select" name="orderStatus" id="orderStatus">
                                <?php
                                $statuses = ["Pending", "In Progress", "Ready To Collect", "Completed", "Cancelled"];
                                $currentStatus = $rowShowOrderDetails['order_status'] ?? '';

                                foreach ($statuses as $status) {
                                    $selected = ($status == $currentStatus) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>$status</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="btn btn-light">Back</a>
                            <button type="submit" class="btn login-btn" name="editOrderBtn">Save & Changes</button>
                        </div>

                    </form>
                <?php } ?>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script src="./assets/js/email.js"></script>

    <!-- run the function to send email to customer -->
    <?php if ($email && $order_id && $customer_name && $order_status): ?>
        <script>
            window.onload = function() {
                sendOrderStatusEmail(
                    <?php echo json_encode($email); ?>,
                    <?php echo json_encode($order_id); ?>,
                    <?php echo json_encode($customer_name); ?>,
                    <?php echo json_encode($order_status); ?>
                );
            };
        </script>
    <?php endif; ?>

    <script>
        // Sidebar toggle logic
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const topNavbar = document.getElementById('topNavbar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            // Toggle 'collapsed' class on each section to show/hide sidebar
            sidebar.classList.toggle('collapsed');
            topNavbar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    </script>
</body>

</html>