<?php
session_start();
include('dbms.php');

if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") {
    $user_id = $_SESSION['user_id'];
} else {
    header("Location: login.php");
    exit;
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addServiceBtn'])) {
    $newServiceId = getNextIServiceId($conn);
    $newServiceType1 = $_POST['newServiceType'];
    $newServiceDesc = $_POST['newServiceDesc'];
    $newServicePrice = $_POST['newServicePrice'];
    $newServiceStatus = $_POST['newServiceStatus'];

    $addService = "INSERT INTO service_list (service_id, service_type, service_desc, service_price, service_status) 
    VALUE ('$newServiceId', '$newServiceType1', '$newServiceDesc', '$newServicePrice', '$newServiceStatus')";

    if (mysqli_query($conn, $addService)) {
        echo "<script>
            alert(' Service Added successfully.');
            window.location.href = 'adminSettings.php';
          </script>";
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

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
        echo "<script>
            alert(' Service updated successfully.');
            window.location.href = 'adminSettings.php';
          </script>";
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteServiceBtn'])) {

    $deleteServiceId = $_POST['deleteServiceId'];

    $deleteService = "DELETE FROM service_list WHERE service_id = '$deleteServiceId'";

    if (mysqli_query($conn, $deleteService)) {
        echo "<script>
            alert(' Service Deleted successfully.');
            window.location.href = 'adminSettings.php';
          </script>";
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addFinishingBtn'])) {
    $newFinishingId = getNextIFinishingId($conn);
    $newFinishingDesc = $_POST['newFinishingDesc'];
    $newFinishingPrice = $_POST['newFinishingPrice'];
    $newFinishingStatus = $_POST['newFinishingStatus'];

    $addFinishing = "INSERT INTO finishing_list (finishing_id, finishing_desc, finishing_price, finishing_status) 
    VALUE ('$newFinishingId', '$newFinishingDesc', '$newFinishingPrice', '$newFinishingStatus')";

    print_r($_POST);
    if (mysqli_query($conn, $addFinishing)) {
        echo "<script>
            alert(' Finishing Added successfully.');
            window.location.href = 'adminSettings.php';
          </script>";
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editFinishingBtn'])) {
    $updateFinishingId = $_POST['updateFinishingId'];
    $updateFinishingDesc = $_POST['updateFinishingDesc'];
    $updateFinishingPrice = $_POST['updateFinishingPrice'];
    $updateFinishingStatus = $_POST['updateFinishingStatus'];

    $updateFinishing = "UPDATE finishing_list 
    SET finishing_desc = '$updateFinishingDesc', finishing_price = '$updateFinishingPrice', finishing_status = '$updateFinishingStatus'
    WHERE finishing_id = '$updateFinishingId'";

    if (mysqli_query($conn, $updateFinishing)) {
        echo "<script>
            alert(' Finishing updated successfully.');
            window.location.href = 'adminSettings.php';
          </script>";
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Responsive Sidebar Layout</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script src="./assets/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css">

    <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.dataTables.js"></script>

    <style>
        body {
            overflow-x: hidden;
            background-color: #ffff;
        }

        /* Sidebar */
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

        /* top navbar */
        .top-navbar {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
        }

        .top-navbar.collapsed {
            margin-left: 80px;
        }

        /* Main content */
        .main-content {
            margin-left: 240px;
            transition: margin-left 0.3s ease;
            padding: 1rem;
        }

        .main-content.collapsed {
            margin-left: 80px;
        }

        /* mobile adjustments */
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

        /* table */
        #example th,
        #example td {
            text-align: center;
            width: 16.66%
        }
    </style>
</head>

<body>

    <!-- sidebar -->
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

    <!-- top Navbar -->
    <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3 top-navbar">
        <div class="container-fluid">
            <button class="btn btn-outline-secondary me-2" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center ms-auto">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2" type="button"
                        data-bs-toggle="dropdown">
                        <img src="./assets/icon/userpicture.png" class="rounded-circle" width="30" height="30"
                            alt="profile_picture">
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
                <span>Settings</span>
            </div>

            <!-- Navigation Buttons -->
            <div class="mb-3 mt-3" id="myTab" role="tablist">
                <button class="btn btn-outline-primary me-2 btn-sm active" id="priceTab-tab" data-bs-toggle="tab"
                    data-bs-target="#priceTab" type="button" role="tab" aria-controls="priceTab"
                    aria-selected="true">
                    <i class="bi bi-currency-dollar me-2"></i>
                    <span>Price Settings</span>
                </button>

                <button class="btn btn-outline-primary btn-sm" id="userTab-tab" data-bs-toggle="tab"
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
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">Add Service</button>
                        </div>

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
                                                <button type="submit" class="btn btn-primary" name="addServiceBtn">Add Service</button>
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
                                <thead>
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
                                                                        <button type="submit" class="btn btn-primary" name="editServiceBtn">Save changes</button>
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
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFinishingModal">Add Finishing</button>
                        </div>

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
                                                <button type="submit" class="btn btn-primary" name="addFinishingBtn">Add Finishing</button>
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
                                <thead>
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
                                                                        <button type="submit" class="btn btn-primary" name="editFinishingBtn">Save changes</button>
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

                    <div class="container mt-4" style="background-color: gray; min-height: 500px;">
                        <br>
                        <table id="user_management" class="table table-striped mt-3 mb-3">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Create Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">123</a></td>
                                    <td>Admin1</td>
                                    <td>Admin</td>
                                    <td>14-5-2025</td>
                                    <td><span class="badge bg-success text-white">Active</span></td>
                                    <td>
                                        <a href="#" class="btn btn-primary"><i class="bi bi-pencil-square"></i></a>
                                        <a href="#" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
            </div>

        </div>
    </main>

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

        $('#user_management').DataTable({
            lengthChange: false,
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Add New User',
                        className: 'btn btn-primary',
                        action: function(e, dt, node, config) {
                            window.location.href = 'addnewuser.html';
                        }
                    }]
                }
            }
        });
    </script>
</body>

</html>