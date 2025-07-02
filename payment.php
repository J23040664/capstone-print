<?php
session_start();
include 'dbms.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['id']) && $_SESSION['id'] == $_GET['id']) {
    $user_id = $_GET['id'];
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_payment'])) {
        echo "<script>alert('Payment was not completed. You can complete your payment from the order list page.'); window.location.href = 'customerOrderlist.php?id=" . $_POST['user_id'] . "';</script>";
        exit;
    }

    $order_id = $_POST['order_id'];
    $payment_type = $_POST['payment_method'] === 'Online' ? 'Online' : 'Walk-in';

    // Update payment table
    $sql_update_payment = "UPDATE `payment` SET payment_status = 'Paid', payment_type = ?, payment_date = NOW() WHERE order_id = ?";
    $stmt_update_payment = $conn->prepare($sql_update_payment);
    $stmt_update_payment->bind_param("ss", $payment_type, $order_id);
    $stmt_update_payment->execute();

    // Update order table
    $sql_update_order = "UPDATE `order` SET payment_status = 'Paid' WHERE order_id = ?";
    $stmt_update_order = $conn->prepare($sql_update_order);
    $stmt_update_order->bind_param("s", $order_id);
    $stmt_update_order->execute();

    if ($stmt_update_payment->affected_rows > 0 && $stmt_update_order->affected_rows > 0) {
        if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") {
            echo "<script>alert('Payment successful!'); window.location.href = 'adminOrderlist.php?id=" . $user_id . "';</script>";
            exit;
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] == "Staff") {
            echo "<script>alert('Payment successful!'); window.location.href = 'adminOrderlist.php?id=" . $user_id . "';</script>";
            exit;
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") {
            echo "<script>alert('Payment successful!'); window.location.href = 'customerOrderlist.php?id=" . $user_id . "';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Payment update failed.');</script>";
    }
}

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}
$order_id = $_GET['order_id'];

$sql_order = "SELECT * FROM `order` WHERE order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("s", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
if (!$order) {
    die("Order not found.");
}

$sql_detail = "SELECT service_desc, service_total_price, pages, copies, 
                      finishing_desc1, finishing_price1,
                      finishing_desc2, finishing_price2,
                      finishing_desc3, finishing_price3
               FROM `order_detail` WHERE order_id = ?";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("s", $order_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment</title>
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
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                    <li class="nav-item">
                        <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> Manage Orders</a>
                    </li>
                    <li class="nav-item">
                        <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> Manage Quotations</a>
                    </li>
                <?php } ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") { ?>
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
                        <a href="createQuotation.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> Ask Quotations</a>
                    </li>
                <?php } ?>
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
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                <li class="nav-item">
                    <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i> <span>Manage Orders</span></a>
                </li>
                <li class="nav-item">
                    <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> <span>Manage Quotations</span></a>
                </li>
            <?php } ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") { ?>
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
                    <a href="createQuotation.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i> <span>Ask Quotations</span></a>
                </li>
            <?php } ?>
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
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                            <li><a class="dropdown-item" href="adminSettings.php?id=<?php echo $user_id; ?>">Settings</a></li>
                        <?php } ?>
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
            <div class="mt-4 fw-bold">
                <span class="fs-3">Payment</span>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="content-box">
                        <h5>🧾 Order Summary</h5>
                        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <hr>
                        <?php while ($row = $result_detail->fetch_assoc()): ?>
                            <div class="mb-3">
                                <p><strong><?= $row['service_desc'] ?> - RM<?= number_format($row['service_total_price'], 2) ?></strong></p>
                                <p>Pages: <?= $row['pages'] ?> | Copies: <?= $row['copies'] ?></p>
                                <?php
                                for ($i = 1; $i <= 3; $i++) {
                                    $desc = $row["finishing_desc$i"];
                                    $price = $row["finishing_price$i"];
                                    if (!empty($desc) && $price > 0) {
                                        echo "<p>Finishing: $desc - RM" . number_format($price, 2) . "</p>";
                                    }
                                }
                                ?>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                        <h6>Total Price: <strong>RM<?= number_format($order['total_price'], 2) ?></strong></h6>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content-box">
                        <!-- Choose Payment Method Form Section -->
                        <h5>💳 Choose Payment Method</h5>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($order['customer_id']) ?>">

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="walkin" value="Walk-in" checked>
                                <label class="form-check-label" for="walkin">
                                    Walk-in Payment (Pay at Counter)
                                    <button type="button" tabindex="0" class="btn btn-sm btn-link p-0"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="focus"
                                        title="How Walk-in Payment Works"
                                        data-bs-content="Walk-in orders are not processed or printed until payment is made at the store. This helps avoid uncollected or fake orders. Pay at counter using order ID.">
                                        <i class="bi bi-question-circle"></i>
                                    </button>

                                </label>
                            </div>
                            <div class="alert alert-warning mt-2" id="walkinNotice" style="display: block;">
                                Walk-in orders will only be printed once payment is made at the store.
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="online" value="Online">
                                <label class="form-check-label" for="online">Online Payment</label>
                            </div>

                            <div class="online-portals" id="portalOptions">
                                <label class="form-label">Select Payment Portal</label>
                                <select class="form-select" name="portal">
                                    <option selected disabled>-- Choose Portal --</option>
                                    <option value="TnG">Touch 'n Go</option>
                                    <option value="FPX">FPX</option>
                                    <option value="GrabPay">GrabPay</option>
                                </select>
                            </div>

                            <div id="onlineAgreement" class="form-text text-danger mt-3" style="display: none;">
                                <strong>Note:</strong> Once online payment is made, your order will be processed immediately. <br>
                                Orders paid online are <strong>non-refundable and cannot be canceled</strong>. <br>
                                Please collect using your <strong>Order ID</strong> at the store.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-success w-50 me-2">Proceed to Pay</button>
                                <button type="submit" name="cancel_payment" class="btn btn-outline-danger w-50">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enable Popover & Bootstrap Functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
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

        const walkinRadio = document.getElementById('walkin');
        const onlineRadio = document.getElementById('online');
        const portalOptions = document.getElementById('portalOptions');

        function togglePortal() {
            portalOptions.style.display = onlineRadio.checked ? 'block' : 'none';
            document.getElementById('onlineAgreement').style.display = onlineRadio.checked ? 'block' : 'none';
            document.getElementById('walkinNotice').style.display = walkinRadio.checked ? 'block' : 'none';
        }

        walkinRadio.addEventListener('change', togglePortal);
        onlineRadio.addEventListener('change', togglePortal);

        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverTriggerList.forEach(el => new bootstrap.Popover(el));
    </script>

</body>

</html>