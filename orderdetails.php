<?php
// Include your database connection
include 'dbms.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$order_id = $_GET['order_id'];
$customer_id = $_GET['id'];

// Fetch order info
$sql_order = "SELECT * FROM `order` WHERE order_id = ? AND customer_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("si", $order_id, $customer_id);
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

    <style>
        body {
            overflow-x: hidden;
            background-color: #fff;
        }

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

        .top-navbar {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
        }

        .top-navbar.collapsed {
            margin-left: 80px;
        }

        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
            padding: 1rem;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

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
                <a href="orderlist.html" class="nav-link active"><i class="bi bi-card-list"></i><span>Manage Orders</span></a>
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

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <div class="container-fluid">
            <div class="mt-3 fw-bold">
                <span>Order Details</span>
            </div>

            <!-- Order Form (readonly) -->
            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="max-height: 80vh; overflow-y: auto;">
                <form>
                    <!-- Customer Name -->
                    <div class="mb-4">
                        <label class="form-label">Name:</label>
                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($order['customer_name']); ?>" disabled>
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
                        <a href="customerOrderlist.php?id=<?php echo urlencode($customer_id); ?>" class="btn btn-primary">Back</a>
                    </div>
                </form>
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