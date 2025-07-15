<?php
session_start();
include('dbms.php');

// must include at each page, to prevent change user id from url
if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin" && $_SESSION['id'] == $_GET['id']) {
    $user_id = $_GET['id'];
    $quotation_id = $_GET['quotation_id'];
    // show the user info
    $showUserInfo = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $queryShowUserInfo = mysqli_query($conn, $showUserInfo) or die(mysqli_error($conn));
    $rowShowUserInfo = mysqli_fetch_assoc($queryShowUserInfo);
} else {
    header("Location: login.php");
    exit;
}

$showQuotationDetails = "SELECT * FROM quotation WHERE quotation_id = '$quotation_id'";
$queryShowQuotationDetails = mysqli_query($conn, $showQuotationDetails) or die("Error: " . mysqli_error($conn));

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['viewFileBtn'])) {
    $query = "SELECT file_type, file_data FROM quotation WHERE quotation_id = '$quotation_id'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $fileType = strtolower($row['file_type']);
        $fileData = $row['file_data'];

        header("Content-Type: application/$fileType");
        header("Content-Disposition: inline; filename=\"quotation.pdf\"");
        echo $fileData;
        exit;
    } else {
        echo "File not found.";
    }
}

// edit quotation status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editQuotationBtn'])) {
    $updateQuotationStatus = $_POST['quotationStatus'];

    $updateQuotation = "UPDATE quotation 
    SET quotation_status = '$updateQuotationStatus'
    WHERE quotation_id = '$quotation_id'";

    if (mysqli_query($conn, $updateQuotation)) {
        echo "<script>
            alert(' Quotation updated successfully.');
            window.location.href = 'adminQuotationlist.php?id=$user_id';
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
    <title>Quotation Details</title>

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
        <div class="container mt-5 mb-5">
            <h2 class="mb-4">Quotation Details</h2>
            <div class="bg-white p-4 rounded shadow-sm">
                <?php while ($rowShowQuotationDetails = mysqli_fetch_assoc($queryShowQuotationDetails)) { ?>
                    <!-- Customer Info -->
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="requester_name" class="form-control" value="<?php echo $rowShowQuotationDetails['requester_name'] ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="requester_email" class="form-control" value="<?php echo $rowShowQuotationDetails['requester_email'] ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="requester_phone_number" class="form-control" value="<?php echo $rowShowQuotationDetails['requester_phone_number'] ?>" disabled>
                    </div>

                    <!-- Prefered Contact Method -->
                    <div class="mb-3">
                        <label class="form-label d-block">Preferred Contact Method</label>
                        <div class="form-check form-check-inline">
                            <?php if (strtolower($rowShowQuotationDetails['contact_method']) == "email") { ?>
                                <input class="form-check-input" type="radio" checked disabled>
                            <?php } else { ?>
                                <input class="form-check-input" type="radio" disabled>
                            <?php } ?>
                            <label class="form-check-label">Email</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <?php if (strtolower($rowShowQuotationDetails['contact_method']) == "whatsapps") { ?>
                                <input class="form-check-input" type="radio" checked disabled>
                            <?php } else { ?>
                                <input class="form-check-input" type="radio" disabled>
                            <?php } ?>
                            <label class="form-check-label">WhatsApp</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <?php if (strtolower($rowShowQuotationDetails['contact_method']) == "phone") { ?>
                                <input class="form-check-input" type="radio" checked disabled>
                            <?php } else { ?>
                                <input class="form-check-input" type="radio" disabled>
                            <?php } ?>
                            <label class="form-check-label">Phone Call</label>
                        </div>
                    </div>

                    <!-- Request Type -->
                    <div class="mb-3">
                        <label class="form-label">Request Type</label>
                        <input type="text" name="request_type" class="form-control" value="<?php echo $rowShowQuotationDetails['request_type'] ?>" disabled>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="text" name="quantity" class="form-control" value="<?php echo $rowShowQuotationDetails['quantity'] ?>" disabled>
                    </div>

                    <!-- Paper Size -->
                    <div class="mb-3">
                        <label class="form-label">Paper Size</label>
                        <select name="paper_size" id="paper_size" class="form-select" disabled>
                            <?php if ($rowShowQuotationDetails['paper_size'] == "") { ?>
                                <option value="" selected>Select a size</option>
                            <?php } else if (strtolower(trim($rowShowQuotationDetails['paper_size'])) == "a4") { ?>
                                <option value="A4">A4</option>
                            <?php } else if (strtolower(trim($rowShowQuotationDetails['paper_size'])) == "a3") { ?>
                                <option value="A3">A3</option>
                            <?php } else if (strtolower(trim($rowShowQuotationDetails['paper_size'])) == "a5") { ?>
                                <option value="A5">A5</option>
                            <?php } else if (strtolower(trim($rowShowQuotationDetails['paper_size'])) == "custom") { ?>
                                <option value="Custom">Custom</option>
                            <?php } ?>
                        </select>
                    </div>

                    <?php if (strtolower(trim($rowShowQuotationDetails['paper_size'])) == "a4" || strtolower(trim($rowShowQuotationDetails['paper_size'])) == "a3" || strtolower(trim($rowShowQuotationDetails['paper_size'])) == "a5") { ?>
                        <div id="size-display" class="mb-3" style="display: block;">
                            <label class="form-label">Dimensions</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control" value="<?php echo $rowShowQuotationDetails['size_width']; ?>" placeholder="Width" disabled>
                                <span>x</span>
                                <input type="number" class="form-control" value="<?php echo $rowShowQuotationDetails['size_height']; ?>" placeholder="Height" disabled>
                                <select id="unit" class="form-select" style="width: 100px;">
                                    <option value="cm">cm</option>
                                    <option value="mm">mm</option>
                                    <option value="inch">inch</option>
                                </select>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($rowShowQuotationDetails['paper_size'] == "Custom") { ?>
                        <div id="custom-size-inputs" style="display: block;">
                            <label class="form-label mt-2">Enter Custom Size</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" class="form-control" value="<?php echo $rowShowQuotationDetails['size_width']; ?>" placeholder="Width" disabled>
                                <span>x</span>
                                <input type="number" class="form-control" value="<?php echo $rowShowQuotationDetails['size_height']; ?>" placeholder="Height" disabled>

                                <select id="custom_unit" name="custom_unit" class="form-select" style="width: 100px;">
                                    <option value="cm">cm</option>
                                    <option value="mm">mm</option>
                                    <option value="inch">inch</option>
                                </select>
                                <input type="hidden" name="size_unit" id="hidden_size_unit">
                                <input type="hidden" name="size_width" id="hidden_size_width">
                                <input type="hidden" name="size_height" id="hidden_size_height">
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Paper Type -->
                    <div class="mt-3 mb-3">
                        <label class="form-label">Paper Type</label>
                        <input type="text" name="paper_type" class="form-control" value="<?php echo $rowShowQuotationDetails['paper_type'] ?>" disabled>
                    </div>

                    <!-- Finishing -->
                    <div class="mb-3">
                        <label class="form-label">Finishing</label>
                        <input type="text" name="finishing" class="form-control" value="<?php echo $rowShowQuotationDetails['finishing'] ?>" disabled>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <?php if (!empty($rowShowQuotationDetails['file_data'])): ?>
                            <form method="post" target="_blank">
                                <input type="hidden" class="form-control" name="viewFileId" value="<?php echo htmlspecialchars($rowShowQuotationDetails['file_id']); ?>">
                                <button type="submit" class="btn login-btn w-20" name="viewFileBtn">View File</button>
                            </form>
                        <?php else: ?>
                            <input type="text" class="form-control" value="No file uploaded" disabled>
                        <?php endif; ?>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-3">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" rows="4" class="form-control" placeholder="Any notes or requests..." disabled><?php echo $rowShowQuotationDetails['remark'] ?></textarea>
                    </div>



                    <a href="mailto:<?php echo $rowShowQuotationDetails['requester_email']; ?>" class="btn btn-success mb-3 w-100">Reply</a>

                    <hr class="mt-3 mb-5">

                    <form method="POST">

                        <div class="col-md-4">
                            <label for="quotationStatus">Quotation Status:</label>
                            <select class="form-select mt-3 mb-3" name="quotationStatus" required>
                                <option value="Pending" <?php if ($rowShowQuotationDetails['quotation_status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Done" <?php if ($rowShowQuotationDetails['quotation_status'] === 'Done') echo 'selected'; ?>>Done</option>
                            </select>
                        </div>

                        <hr class="mt-5 mb-5">

                        <div class="row">
                            <a href="adminQuotationlist.php?id=<?php echo $user_id; ?>" class="btn btn-secondary mb-3">Back</a>
                            <button type="submit" class="btn login-btn" name="editQuotationBtn">Save & Changes</button>
                        </div>

                    </form>
                <?php } ?>
            </div>
        </div>
    </main>

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
    </script>
</body>

</html>