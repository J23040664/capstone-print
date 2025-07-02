<?php
session_start();
include('dbms.php');

// must include at each page, to prevent change user id from url
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

$showToast = false;
if (isset($_SESSION['success']) && $_SESSION['success']) {
    $showToast = true;
    $showMessage = $_SESSION['toastMessage'];
    unset($_SESSION['success']); // Show only once
    unset($_SESSION['toastMessage']);
}

$showFailedToast = false;
if (isset($_SESSION['failed']) && $_SESSION['failed']) {
    $showFailedToast = true;
    $showFailedMessage = $_SESSION['toastFailedMessage'];
    unset($_SESSION['failed']); // Show only once
    unset($_SESSION['toastFailedMessage']);
}

// function get next service id
function getNextIServiceId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(service_id) AS max_id FROM service_list");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'S' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

// function get next finishing id
function getNextIFinishingId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(finishing_id) AS max_id FROM finishing_list");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'S' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

// add service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addServiceBtn'])) {
    $newServiceId = getNextIServiceId($conn);
    $newServiceType1 = $_POST['newServiceType'];
    $newServiceDesc = $_POST['newServiceDesc'];
    $newServicePrice = $_POST['newServicePrice'];
    $newServiceStatus = $_POST['newServiceStatus'];

    $addService = "INSERT INTO service_list (service_id, service_type, service_desc, service_price, service_status) 
    VALUE ('$newServiceId', '$newServiceType1', '$newServiceDesc', '$newServicePrice', '$newServiceStatus')";

    if (mysqli_query($conn, $addService)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "Service {$newServiceId} added successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error service {$newServiceId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// edit service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editServiceBtn'])) {
    $updateServiceId = $_POST['updateServiceId'];
    $updateServiceType = $_POST['updateServiceType'];
    $updateServiceDesc = $_POST['updateServiceDesc'];
    $updateServicePrice = $_POST['updateServicePrice'];
    $updateServiceStatus = $_POST['updateServiceStatus'];

    $updateService = "UPDATE service_list 
    SET service_type = '$updateServiceType', service_desc = '$updateServiceDesc', service_price = '$updateServicePrice', service_status = '$updateServiceStatus'
    WHERE service_id = '$updateServiceId'";

    if (mysqli_query($conn, $updateService)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "Service {$updateServiceId} edited successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error service {$updateServiceId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// delete service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteServiceBtn'])) {

    $deleteServiceId = $_POST['deleteServiceId'];

    $deleteService = "DELETE FROM service_list WHERE service_id = '$deleteServiceId'";

    if (mysqli_query($conn, $deleteService)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "Service {$deleteServiceId} deleted successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error service {$deleteServiceId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// add finishing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addFinishingBtn'])) {
    $newFinishingId = getNextIFinishingId($conn);
    $newFinishingDesc = $_POST['newFinishingDesc'];
    $newFinishingPrice = $_POST['newFinishingPrice'];
    $newFinishingStatus = $_POST['newFinishingStatus'];

    $addFinishing = "INSERT INTO finishing_list (finishing_id, finishing_desc, finishing_price, finishing_status) 
    VALUE ('$newFinishingId', '$newFinishingDesc', '$newFinishingPrice', '$newFinishingStatus')";

    if (mysqli_query($conn, $addFinishing)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "Finishing {$newFinishingId} added successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error Finishing {$newFinishingId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// edit finishing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editFinishingBtn'])) {
    $updateFinishingId = $_POST['updateFinishingId'];
    $updateFinishingDesc = $_POST['updateFinishingDesc'];
    $updateFinishingPrice = $_POST['updateFinishingPrice'];
    $updateFinishingStatus = $_POST['updateFinishingStatus'];

    $updateFinishing = "UPDATE finishing_list 
    SET finishing_desc = '$updateFinishingDesc', finishing_price = '$updateFinishingPrice', finishing_status = '$updateFinishingStatus'
    WHERE finishing_id = '$updateFinishingId'";

    if (mysqli_query($conn, $updateFinishing)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "Finishing {$updateFinishingId} edited successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error Finishing {$updateFinishingId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// delete finishing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteFinishingBtn'])) {

    $deleteFinishingId = $_POST['deleteFinishingId'];

    $deleteFinishing = "DELETE FROM finishing_list WHERE finishing = '$deleteFinishingId'";

    if (mysqli_query($conn, $deleteFinishing)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "Finishing {$deleteFinishingId} deleted successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error Finishing {$deleteFinishingId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// add user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addUserBtn'])) {
    $newName = htmlspecialchars(trim($_POST["newName"]));
    $newEmail = htmlspecialchars(trim($_POST["newEmail"]));
    $newPhoneNumber = htmlspecialchars(trim($_POST["newPhoneNumber"]));
    $newPassword = htmlspecialchars(trim($_POST["newPassword"]));
    $newRole = htmlspecialchars(trim($_POST["newRole"]));
    $newCreateDate = date("Y-m-d");
    $newImg = 1;

    // Check if email already exists
    $checkEmail = "SELECT * FROM user WHERE email = '$newEmail'";
    $queryCheckEmail = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($queryCheckEmail) > 0) {
        $_SESSION['failed'] = true;
        $_SESSION['toastFailedMessage'] = "Email already exists.";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        // Hash password and insert user
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $addUser = "INSERT INTO user (name, email, phone_number, password, role, create_date, img_id)
        VALUES ('$newName', '$newEmail', '$newPhoneNumber', '$hashedPassword', '$newRole', '$newCreateDate', $newImg)";

        if (mysqli_query($conn, $addUser)) {
            $_SESSION['success'] = true; // Set flag for toast
            $_SESSION['toastMessage'] = "User added successfully!";
            header("Location: adminSettings.php?id=" . $user_id);
            exit;
        } else {
            $_SESSION['failed'] = true; // Set flag for toast
            $_SESSION['toastFailedMessage'] = "Error user add: " . mysqli_error($conn);
            header("Location: adminSettings.php?id=" . $user_id);
            exit;
        }
    }
}

// edit user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editUserBtn'])) {
    $updateUserId = $_POST['updateUserId'];
    $updateName = $_POST['updateName'];
    $updateEmail = $_POST['updateEmail'];
    $updatePhoneNumber = $_POST['updatePhoneNumber'];
    $updateRole = $_POST['updateRole'];


    $updateUser = "UPDATE user
    SET name = '$updateName', email = '$updateEmail', phone_number = '$updatePhoneNumber', role = '$updateRole'
    WHERE user_id = '$updateUserId'";

    if (mysqli_query($conn, $updateUser)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "User {$updateUserId} edited successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error user {$updateUserId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

// delete user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteUserBtn'])) {

    $deleteUserId = $_POST['deleteUserId'];

    $deleteUser = "DELETE FROM user WHERE user_id = '$deleteUserId'";

    if ($deleteUserId == $_SESSION['id']) {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "You can't delete your own.";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }

    if (mysqli_query($conn, $deleteUser)) {
        $_SESSION['success'] = true; // Set flag for toast
        $_SESSION['toastMessage'] = "User {$deleteUserId} deleted successfully!";
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    } else {
        $_SESSION['failed'] = true; // Set flag for toast
        $_SESSION['toastFailedMessage'] = "Error user {$deleteUserId} record: " . mysqli_error($conn);
        header("Location: adminSettings.php?id=" . $user_id);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Settings</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="./assets/css/systemStyle.css" />

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

            <?php if ($showToast): ?>
                <!-- Success Toast -->
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                    <div id="successToast" class="toast text-white bg-success border-0">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo htmlspecialchars($showMessage); ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showFailedToast): ?>
                <!-- Failed Toast -->
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                    <div id="failedToast" class="toast text-white bg-danger border-0">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo htmlspecialchars($showFailedMessage); ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            <div class="mt-4 fw-bold">
                <span>Settings</span>
            </div>

            <!-- Navigation Buttons -->
            <div class="mb-3 mt-4" id="myTab" role="tablist">
                <button class="btn navtab-btn me-2 btn-sm active" id="priceTab-tab" data-bs-toggle="tab"
                    data-bs-target="#priceTab" type="button" role="tab" aria-controls="priceTab"
                    aria-selected="true">
                    <i class="bi bi-currency-dollar me-2"></i>
                    <span>Price Settings</span>
                </button>

                <button class="btn navtab-btn btn-sm" id="userTab-tab" data-bs-toggle="tab"
                    data-bs-target="#userTab" type="button" role="tab" aria-controls="userTab"
                    aria-selected="false">
                    <i class="bi bi-people me-2"></i>
                    <span>User Management</span>
                </button>
            </div>

            <div class="tab-content">
                <!-- price settings -->
                <div class="tab-pane fade show active" id="priceTab" role="tabpanel" aria-labelledby="priceTab-tab">

                    <div class="container mt-4 bg-white p-4 rounded shadow-lg">
                        <h5>Service List</h5>

                        <!-- add service modal -->
                        <div class="modal fade" id="addServiceModal">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="addServiceModalLabel">Add Service</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">

                                            <div class="mb-3">
                                                <label for="newServiceType">Service Type:</label>
                                                <input type="text" class="form-control" name="newServiceType" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="newServiceDesc">Service Description:</label>
                                                <input type="text" class="form-control" name="newServiceDesc" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="newServicePrice">Service Price:</label>
                                                <input type="text" class="form-control" name="newServicePrice" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="newServiceStatus">Service Status:</label>
                                                <select class="form-select" name="newServiceStatus" required>
                                                    <option value="Available">Available</option>
                                                    <option value="Not Available">Not Available</option>
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn login-btn" name="addServiceBtn">Add Service</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $showServiceList = "SELECT * FROM service_list";
                        $queryShowServiceList = mysqli_query($conn, $showServiceList) or die("Error: " . mysqli_error($conn));
                        ?>

                        <div>
                            <table id="serviceList" class="table table-striped mt-3 mb-3">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Service ID</th>
                                        <th>Service Type</th>
                                        <th>Service Description</th>
                                        <th>Service Price</th>
                                        <th>Service Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($rowShowServiceList = $queryShowServiceList->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $rowShowServiceList['service_id']; ?></a></td>
                                            <td><?php echo $rowShowServiceList['service_type']; ?></td>
                                            <td><?php echo $rowShowServiceList['service_desc']; ?></td>
                                            <td><?php echo $rowShowServiceList['service_price']; ?></td>
                                            <td>
                                                <?php if ($rowShowServiceList['service_status'] == "Available") { ?>
                                                    <span class="badge bg-success text-white"><?php echo $rowShowServiceList['service_status']; ?></span>
                                                <?php } else if ($rowShowServiceList['service_status'] == "Not Available") { ?>
                                                    <span class="badge bg-danger text-white"><?php echo $rowShowServiceList['service_status']; ?></span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <button class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editService<?php echo $rowShowServiceList['service_id']; ?>"><i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- service edit modal -->
                                                <div class="modal fade" id="editService<?php echo $rowShowServiceList['service_id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5"><?php echo $rowShowServiceList['service_id']; ?></h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST">
                                                                    <div class="mb-3">
                                                                        <label for="updateServiceId">Service Id:</label>
                                                                        <input type="text" class="form-control" name="updateServiceId" value="<?php echo $rowShowServiceList['service_id']; ?>" readonly>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateServiceType">Service Type</label>
                                                                        <input type="text" class="form-control" name="updateServiceType" value="<?php echo $rowShowServiceList['service_type']; ?>">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateServiceDesc">Service Description</label>
                                                                        <input type="text" class="form-control" name="updateServiceDesc" value="<?php echo $rowShowServiceList['service_desc']; ?>">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateServicePrice">Service Price</label>
                                                                        <input type="text" class="form-control" name="updateServicePrice" value="<?php echo $rowShowServiceList['service_price']; ?>">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateServiceStatus">Service Status:</label>
                                                                        <select class="form-select" name="updateServiceStatus">
                                                                            <option value="<?php echo $rowShowServiceList['service_status']; ?>" selected><?php echo $rowShowServiceList['service_status']; ?></option>
                                                                            <hr>
                                                                            <option value="Available">Available</option>
                                                                            <option value="Not Available">Not Available</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn login-btn" name="editServiceBtn">Save changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteService<?php echo $rowShowServiceList['service_id']; ?>"><i class="bi bi-trash"></i>
                                                </button>

                                                <!-- service delete modal -->
                                                <div class="modal fade" id="deleteService<?php echo $rowShowServiceList['service_id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5"><?php echo $rowShowServiceList['service_id']; ?></h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST">

                                                                    <p>Do you really want to delete?</p>
                                                                    <input type="text" name="deleteServiceId" value="<?php echo $rowShowServiceList['service_id']; ?>" readonly>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary" name="deleteServiceBtn">Confirm Delete</button>
                                                                    </div>

                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>



                        <hr class="mt-5 mb-5">



                        <h5>Finishing List</h5>

                        <!-- add finishing modal -->
                        <div class="modal fade" id="addFinishingModal">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="addFinishingModalLabel">Add Finishing</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">

                                            <div class="mb-3">
                                                <label for="newFinishingDesc">Finishing Description:</label>
                                                <input type="text" class="form-control" name="newFinishingDesc" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="newFinishingPrice">Finishing Price:</label>
                                                <input type="text" class="form-control" name="newFinishingPrice" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="newFinishingStatus">Finishing Status:</label>
                                                <select class="form-select" name="newFinishingStatus" required>
                                                    <option value="Available">Available</option>
                                                    <option value="Not Available">Not Available</option>
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn login-btn" name="addFinishingBtn">Add Finishing</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $showFinishingList = "SELECT * FROM finishing_list";
                        $queryShowFinishingList = mysqli_query($conn, $showFinishingList) or die("Error: " . mysqli_error($conn));
                        ?>

                        <div>
                            <table id="finishingList" class="table table-striped mt-3 mb-3">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Finishing ID</th>
                                        <th>Finishing Description</th>
                                        <th>Finishing Price</th>
                                        <th>Finishing Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($rowShowFinishingList = $queryShowFinishingList->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $rowShowFinishingList['finishing_id']; ?></td>
                                            <td><?php echo $rowShowFinishingList['finishing_desc']; ?></td>
                                            <td><?php echo $rowShowFinishingList['finishing_price']; ?></td>
                                            <td>
                                                <?php if ($rowShowFinishingList['finishing_status'] == "Available") { ?>
                                                    <span class="badge bg-success text-white"><?php echo $rowShowFinishingList['finishing_status']; ?></span>
                                                <?php } else if ($rowShowFinishingList['finishing_status'] == "Not Available") { ?>
                                                    <span class="badge bg-danger text-white"><?php echo $rowShowFinishingList['finishing_status']; ?></span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <button class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editFinishing<?php echo $rowShowFinishingList['finishing_id']; ?>"><i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- finishing edit modal -->
                                                <div class="modal fade" id="editFinishing<?php echo $rowShowFinishingList['finishing_id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5"><?php echo $rowShowFinishingList['finishing_id']; ?></h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST">
                                                                    <div class="mb-3">
                                                                        <label for="updateFinishingId">Finishing Id:</label>
                                                                        <input type="text" class="form-control" name="updateFinishingId" value="<?php echo $rowShowFinishingList['finishing_id']; ?>" readonly>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateFinishingDesc">Finishing Description</label>
                                                                        <input type="text" class="form-control" name="updateFinishingDesc" value="<?php echo $rowShowFinishingList['finishing_desc']; ?>">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateFinishingPrice">Finishing Price</label>
                                                                        <input type="text" class="form-control" name="updateFinishingPrice" value="<?php echo $rowShowFinishingList['finishing_price']; ?>">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="updateFinishingStatus">Finishing Status:</label>
                                                                        <select class="form-select" name="updateFinishingStatus">
                                                                            <option value="<?php echo $rowShowFinishingList['finishing_status']; ?>" selected><?php echo $rowShowFinishingList['finishing_status']; ?></option>
                                                                            <hr>
                                                                            <option value="Available">Available</option>
                                                                            <option value="Not Available">Not Available</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn login-btn" name="editFinishingBtn">Save changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteFinishing<?php echo $rowShowFinishingList['finishing_id']; ?>"><i class="bi bi-trash"></i>
                                                </button>

                                                <!-- Finishing delete modal -->
                                                <div class="modal fade" id="deleteFinishing<?php echo $rowShowFinishingList['finishing_id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5"><?php echo $rowShowFinishingList['finishing_id']; ?></h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST">

                                                                    <p>Do you really want to delete?</p>
                                                                    <input type="text" name="deleteFinishingId" value="<?php echo $rowShowFinishingList['finishing_id']; ?>" readonly>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary" name="deleteFinishingBtn">Confirm Delete</button>
                                                                    </div>

                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>



                        <br>
                    </div>
                </div>

                <!-- user management -->
                <div class="tab-pane fade" id="userTab" role="tabpanel" aria-labelledby="userTab-tab">
                    <!-- add user modal -->
                    <div class="modal fade" id="addUserModal">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addUserModalLabel">Add User</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post">

                                        <div class="mb-3">
                                            <label for="newName">Full Name:</label>
                                            <input type="text" class="form-control" name="newName" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="newEmail">Email:</label>
                                            <input type="text" class="form-control" name="newEmail" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="newPhoneNumber">Phone Number:</label>
                                            <input type="text" class="form-control" name="newPhoneNumber" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="newPassword">Password:</label>
                                            <input type="text" class="form-control" name="newPassword" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="newRole">Role:</label>
                                            <select class="form-select" name="newRole" required>
                                                <option value="Admin">Admin</option>
                                                <option value="Staff">Staff</option>
                                                <option value="Customer">Customer</option>
                                            </select>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn login-btn" name="addUserBtn">Add User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container mt-4 bg-white p-4 rounded shadow-lg">
                        <?php
                        $showUserList = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id";
                        $queryShowUserList = mysqli_query($conn, $showUserList) or die("Error: " . mysqli_error($conn));
                        ?>
                        <table id="user_management" class="table table-striped mt-3 mb-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>User ID</th>
                                    <th>Profile Image</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Role</th>
                                    <th>Create Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($rowShowUserList = $queryShowUserList->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $rowShowUserList['user_id']; ?></td>
                                        <td><img src="data:<?php echo $rowShowUserList['img_type']; ?>;base64,<?php echo base64_encode($rowShowUserList['img_data']); ?>"
                                                class="rounded-circle" width="30" height="30" alt="profile" />
                                        </td>
                                        <td><?php echo $rowShowUserList['name']; ?></td>
                                        <td><?php echo $rowShowUserList['email']; ?></td>
                                        <td><?php echo $rowShowUserList['phone_number']; ?></td>
                                        <td>
                                            <?php if ($rowShowUserList['role'] == "Admin") { ?>
                                                <span class="badge bg-success text-white"><?php echo $rowShowUserList['role']; ?></span>
                                            <?php } else if ($rowShowUserList['role'] == "Staff") { ?>
                                                <span class="badge bg-danger text-white"><?php echo $rowShowUserList['role']; ?></span>
                                            <?php } else if ($rowShowUserList['role'] == "Customer") { ?>
                                                <span class="badge bg-warning text-white"><?php echo $rowShowUserList['role']; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $rowShowUserList['create_date']; ?></td>
                                        <td>
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editUser<?php echo $rowShowUserList['user_id']; ?>"><i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Edit user modal -->
                                            <div class="modal fade" id="editUser<?php echo $rowShowUserList['user_id']; ?>">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5"><?php echo $rowShowUserList['user_id']; ?></h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="post">

                                                                <input type="hidden" class="form-control" name="updateUserId" value="<?php echo $rowShowUserList['user_id']; ?>" readonly>

                                                                <div class="mb-3">
                                                                    <label for="updateName">Full Name:</label>
                                                                    <input type="text" class="form-control" name="updateName" value="<?php echo $rowShowUserList['name']; ?>" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="updateEmail">Email:</label>
                                                                    <input type="text" class="form-control" name="updateEmail" value="<?php echo $rowShowUserList['email']; ?>" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="updatePhoneNumber">Phone Number:</label>
                                                                    <input type="text" class="form-control" name="updatePhoneNumber" value="<?php echo $rowShowUserList['phone_number']; ?>" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="updateRole">Role:</label>
                                                                    <select class="form-select" name="updateRole" required>
                                                                        <option value="Admin" <?php if ($rowShowUserList['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
                                                                        <option value="Staff" <?php if ($rowShowUserList['role'] === 'Staff') echo 'selected'; ?>>Staff</option>
                                                                        <option value="Customer" <?php if ($rowShowUserList['role'] === 'Customer') echo 'selected'; ?>>Customer</option>
                                                                    </select>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn login-btn" name="editUserBtn">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteUser<?php echo $rowShowUserList['user_id']; ?>"><i class="bi bi-trash"></i>
                                            </button>

                                            <!-- User delete modal -->
                                            <div class="modal fade" id="deleteUser<?php echo $rowShowUserList['user_id']; ?>">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5"><?php echo $rowShowUserList['user_id']; ?></h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST">

                                                                <p>Do you really want to delete?</p>
                                                                <input type="hidden" name="deleteUserId" value="<?php echo $rowShowUserList['user_id']; ?>" readonly>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger" name="deleteUserBtn">Confirm Delete</button>
                                                                </div>

                                                            </form>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>

    <?php if ($showToast): ?>
        <script>
            const toast = new bootstrap.Toast(document.getElementById('successToast'));
            toast.show();
        </script>
    <?php endif; ?>

    <?php if ($showFailedToast): ?>
        <script>
            const toastFailed = new bootstrap.Toast(document.getElementById('failedToast'));
            toastFailed.show();
        </script>
    <?php endif; ?>

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

        $('#serviceList').DataTable({
            lengthChange: false,
            pageLength: 10, // Show 10 entries per page
            scrollY: '400px', // Set vertical scroll height
            scrollCollapse: true, // Collapse table height when fewer rows
            paging: true, // Enable pagination
            columnDefs: [{
                    targets: '_all',
                    className: 'text-start'
                } // Bootstrap's left-align class
            ],
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Add New Service',
                        className: 'btn login-btn btn-sm',
                        action: function(e, dt, node, config) {
                            $('#addServiceModal').modal('show');
                        }
                    }]
                }
            }
        });

        $('#finishingList').DataTable({
            lengthChange: false,
            pageLength: 10, // Show 10 entries per page
            scrollY: '400px', // Set vertical scroll height
            scrollCollapse: true, // Collapse table height when fewer rows
            paging: true, // Enable pagination
            columnDefs: [{
                    targets: '_all',
                    className: 'text-start'
                } // Bootstrap's left-align class
            ],
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Add New Finishing',
                        className: 'btn login-btn btn-sm',
                        action: function(e, dt, node, config) {
                            $('#addFinishingModal').modal('show');
                        }
                    }]
                }
            }
        });

        $('#user_management').DataTable({
            lengthChange: false,
            pageLength: 10, // Show 10 entries per page
            scrollY: '400px', // Set vertical scroll height
            scrollCollapse: true, // Collapse table height when fewer rows
            paging: true, // Enable pagination
            columnDefs: [{
                    targets: '_all',
                    className: 'text-start'
                } // Bootstrap's left-align class
            ],
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Add New User',
                        className: 'btn login-btn btn-sm',
                        action: function(e, dt, node, config) {
                            $('#addUserModal').modal('show');
                        }
                    }]
                }
            }
        });
    </script>
</body>

</html>