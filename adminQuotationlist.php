<?php
session_start();
include('dbms.php');

if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin" && $_SESSION['id'] == $_GET['id']) {
    $user_id = $_GET['id'];
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

$showQuotation = "SELECT * FROM quotation";
$queryShowQuotation = mysqli_query($conn, $showQuotation) or die(mysqli_error($conn));

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script src="./assets/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css">

    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>

    <link rel="stylesheet" href="./adminStyle.css">
</head>

<body>

    <!-- Sidebar Navigation -->
    <div id="sidebar" class="d-flex flex-column p-3 sidebar">
        <div class="s_logo fs-5">
            <span>Art & Print</span>
        </div>
        <hr style="height: 4px; background-color: #FAFAFA; border: none;">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-house"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a href="adminOrderlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-card-list"></i><span>Manage Orders</span></a>
            </li>
            <li class="nav-item">
                <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="nav-link"><i class="bi bi-patch-question"></i><span>Manage Quotations</span></a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light shadow-sm px-3 top-navbar fixed-top">
        <div class="container-fluid">
            <button class="btn toggle-btn" id="toggleSidebar">
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
            <div class="mt-3 fw-bold">
                <span>Quotation List</span>
            </div>

            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="min-height: 80vh;">
                <br>
                <table id="quotationList" class="table table-hover mt-3 mb-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Quotation ID</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Customer Phone Number</th>
                            <th>Status</th>
                            <th>Received Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rowShowQuotation = mysqli_fetch_assoc($queryShowQuotation)) { ?>
                            <tr>
                                <td><a class="text-decoration-none link" href="quotationdetails.php?id=<?php echo $user_id; ?>&quotation_id=<?php echo $rowShowQuotation['quotation_id']; ?>"><?php echo $rowShowQuotation['quotation_id']; ?></a></td>
                                <td><?php echo $rowShowQuotation['requester_name']; ?></td>
                                <td><?php echo $rowShowQuotation['requester_email']; ?></td>
                                <td><?php echo $rowShowQuotation['requester_phone_number']; ?></td>
                                <td>
                                    <?php if ($rowShowQuotation['quotation_status'] == "Pending") { ?>
                                        <span class="badge bg-warning text-black"><?php echo $rowShowQuotation['quotation_status']; ?></span>
                                    <?php } else if ($rowShowQuotation['quotation_status'] == "Done") { ?>
                                        <span class="badge bg-success text-white"><?php echo $rowShowQuotation['quotation_status']; ?></span>
                                    <?php } ?>
                                </td>
                                <td><?php echo $rowShowQuotation['create_date']; ?></td>
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

        $('#quotationList').DataTable({
            lengthChange: false,
            pageLength: 10, // Show 10 entries per page
            scrollY: '400px', // Set vertical scroll height
            scrollCollapse: true, // Collapse table height when fewer rows
            paging: true, // Enable pagination
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }],
        });
    </script>
</body>

</html>