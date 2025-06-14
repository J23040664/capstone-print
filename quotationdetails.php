<?php
include('dbms.php');

$quotation_id = $_GET['quotation_id'];
$showQuotation = "SELECT * FROM quotation WHERE quotation_id = '$quotation_id'";
$queryShowQuotation = mysqli_query($conn, $showQuotation) or die("Error: " . mysqli_error($conn));

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['viewFile'])) {
    $viewFile = "SELECT file_data, file_type FROM quotation WHERE quotation_id = '$quotation_id'";
    $queryViewFile = mysqli_query($conn, $viewFile);

    if ($rowViewFile = mysqli_fetch_assoc($queryViewFile)) {
        if (!empty($rowViewFile['file_data'])) {
            $fileDataEncoded = $rowViewFile['file_data'];  // base64 encoded string from DB
            $fileType = $rowViewFile['file_type'] ?? 'application/octet-stream'; // fallback
            $fileData = base64_decode($fileDataEncoded);  // decode before output
            header("Content-Type: $fileType");
            header("Content-Disposition: inline; filename=\"reference_file\"");
            echo $fileData;
            exit;
        }
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
            window.location.href = 'quotationlist.php';
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

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

        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                left: -240px;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .top-navbar,
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body class="bg-light">
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
            <li class="nav-item">
                <a href="ask_quotation.html" class="nav-link"><i class="bi bi-question-circle"></i><span>Ask Quotation</span></a>
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
                            alt="profile_picture" />
                        <span>abc</span>
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
        <div class="container mt-5 mb-5">
            <h2 class="mb-4">Quotation Details</h2>
            <div class="bg-white p-4 rounded shadow-sm">
                <?php while ($rowShowQuotation = mysqli_fetch_assoc($queryShowQuotation)) { ?>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="<?php echo $rowShowQuotation['requester_name'] ?? ''; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" value="<?php echo $rowShowQuotation['requester_email'] ?? ''; ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="<?php echo $rowShowQuotation['requester_phone_number'] ?? ''; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type of Request</label>
                            <select class="form-select" disabled>
                                <option value="None" <?php if (empty($rowShowQuotation['request_type'])) echo 'selected'; ?>>None</option>
                                <option value="Poster" <?php if ($rowShowQuotation['request_type'] === 'Poster') echo 'selected'; ?>>Poster</option>
                                <option value="Booklet" <?php if ($rowShowQuotation['request_type'] === 'Booklet') echo 'selected'; ?>>Booklet</option>
                                <option value="Business Card" <?php if ($rowShowQuotation['request_type'] === 'Business Card') echo 'selected'; ?>>Business Card</option>
                                <option value="Others" <?php if ($rowShowQuotation['request_type'] === 'Others') echo 'selected'; ?>>Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" value="<?php echo $rowShowQuotation['quantity'] ?? ''; ?>" disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Paper Size</label>
                            <select name="newReqPaperSize" class="form-select" disabled>
                                <option value="None" <?php if (empty($rowShowQuotation['paper_size'])) echo 'selected'; ?>>None</option>
                                <option value="A4" <?php if ($rowShowQuotation['paper_size'] === 'A4') echo 'selected'; ?>>A4</option>
                                <option value="A3" <?php if ($rowShowQuotation['paper_size'] === 'A3') echo 'selected'; ?>>A3</option>
                                <option value="Custom" <?php if ($rowShowQuotation['paper_size'] === 'Custom') echo 'selected'; ?>>Custom</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Paper Type</label>
                            <select name="newReqPaperType" class="form-select" disabled>
                                <option value="None" <?php if (empty($rowShowQuotation['paper_type'])) echo 'selected'; ?>>None</option>
                                <option value="Glossy" <?php if ($rowShowQuotation['paper_type'] === 'Glossy') echo 'selected'; ?>>Glossy</option>
                                <option value="Matte" <?php if ($rowShowQuotation['paper_type'] === 'Matte') echo 'selected'; ?>>Matte</option>
                                <option value="Art Paper" <?php if ($rowShowQuotation['paper_type'] === 'Art Paper') echo 'selected'; ?>>Art Paper</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Finishing (optional)</label>
                        <input type="text" class="form-control" value="<?php echo $rowShowQuotation['finishing'] ?? ''; ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Reference File (optional)</label>
                        <?php if (!empty($rowShowQuotation['file_type'])): ?>
                            <form method="post" target="_blank">
                                <button type="submit" class="btn btn-success w-20" name="viewFile">View File</button>
                            </form>
                        <?php else: ?>
                            <input type="text" class="form-control" value="No file uploaded" disabled>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Additional Notes</label>
                        <textarea class="form-control" rows="4" placeholder="Any specific requirements..." value="<?php echo $rowShowQuotation['remark'] ?? ''; ?>" disabled></textarea>
                    </div>

                    <a href="mailto:<?php echo $rowShowQuotation['requester_email']; ?>" class="btn btn-success mb-3 w-100">Reply</a>

                    <hr class="mt-3 mb-5">

                    <form method="POST">

                        <div class="col-md-4">
                            <label for="quotationStatus">Quotation Status:</label>
                            <select class="form-select mt-3 mb-3" name="quotationStatus" required>
                                <option value="Pending" <?php if ($rowShowQuotation['quotation_status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Done" <?php if ($rowShowQuotation['quotation_status'] === 'Done') echo 'selected'; ?>>Done</option>
                            </select>
                        </div>

                        <hr class="mt-5 mb-5">

                        <div class="row">
                            <a href="quotationlist.php" class="btn btn-secondary mb-3">Back</a>
                            <button type="submit" class="btn btn-primary" name="editQuotationBtn">Save & Changes</button>
                        </div>

                    </form>
                <?php } ?>
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
    </script>
</body>

</html>