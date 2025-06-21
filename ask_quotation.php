<?php
include('dbms.php');

// Generate next quotation id
function getNextQuotationId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(quotation_id) AS max_id FROM quotation");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'Q' . str_pad($num, 7, '0', STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitQuotationBtn'])) {
    $newQuotationId     = getNextQuotationId($conn);
    $newReqName         = mysqli_real_escape_string($conn, $_POST['newReqName']);
    $newReqEmail        = mysqli_real_escape_string($conn, $_POST['newReqEmail']);
    $newReqPhoneNumber  = mysqli_real_escape_string($conn, $_POST['newReqPhoneNumber']);
    $newReqRequest      = mysqli_real_escape_string($conn, $_POST['newReqRequest']);
    $newReqQuantity     = intval($_POST['newReqQuantity']);
    $newReqPaperSize    = mysqli_real_escape_string($conn, $_POST['newReqPaperSize']);
    $newReqPaperType    = mysqli_real_escape_string($conn, $_POST['newReqPaperType']);
    $newReqFinishing    = mysqli_real_escape_string($conn, $_POST['newReqFinishing']);
    $newReqRemarks      = mysqli_real_escape_string($conn, $_POST['newReqRemarks']);
    $newReqQuotationStatus = "Pending";
    $newReqCreateDate   = date('Y-m-d');

    // Handle file upload safely
    if (isset($_FILES['newReqFileData']) && $_FILES['newReqFileData']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['newReqFileData']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);
        $fileContent = file_get_contents($fileTmpPath);

        // Encode binary content for safe SQL insert
        $fileContentEncoded = mysqli_real_escape_string($conn, base64_encode($fileContent));
        $fileTypeEscaped = mysqli_real_escape_string($conn, $fileType);
    } else {
        $fileContentEncoded = null;
        $fileTypeEscaped = null;
    }

    $addQuotation = "INSERT INTO quotation (
            quotation_id,
            requester_name,
            requester_email,
            requester_phone_number,
            request_type,
            quantity,
            paper_size,
            paper_type,
            finishing,
            file_type,
            file_data,
            remark,
            quotation_status,
            create_date
        ) VALUES (
            '$newQuotationId',
            '$newReqName',
            '$newReqEmail',
            '$newReqPhoneNumber',
            '$newReqRequest',
            $newReqQuantity,
            '$newReqPaperSize',
            '$newReqPaperType',
            '$newReqFinishing',
            " . ($fileTypeEscaped ? "'$fileTypeEscaped'" : "NULL") . ",
            " . ($fileContentEncoded ? "'$fileContentEncoded'" : "NULL") . ",
            '$newReqRemarks',
            '$newReqQuotationStatus',
            '$newReqCreateDate'
        )
    ";

    if (mysqli_query($conn, $addQuotation)) {
        echo "<script>
            alert('Quotation submitted successfully.');
            window.location.href = 'ask_quotation.php';
        </script>";
        exit;
    } else {
        echo "Database error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ask Quotation</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

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
            <h2 class="mb-4">Ask for Quotation</h2>
            <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="newReqName" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="newReqEmail" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="newReqPhoneNumber" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type of Request</label>
                        <select name="newReqRequest" class="form-select">
                            <option value="None">-- Select --</option>
                            <option value="Poster">Poster</option>
                            <option value="Booklet">Booklet</option>
                            <option value="Business Card">Business Card</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="newReqQuantity" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Paper Size</label>
                        <select name="newReqPaperSize" class="form-select">
                            <option value="None">-- Select --</option>
                            <option value="A4">A4</option>
                            <option value="A3">A3</option>
                            <option value="Custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Paper Type</label>
                        <select name="newReqPaperType" class="form-select">
                            <option value="None">-- Select --</option>
                            <option value="Glossy">Glossy</option>
                            <option value="Matte">Matte</option>
                            <option value="Art Paper">Art Paper</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Finishing (optional)</label>
                    <input type="text" name="newReqFinishing" class="form-control" placeholder="e.g. Lamination, Binding">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Reference File (optional)</label>
                    <input type="file" name="newReqFileData" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label">Additional Notes</label>
                    <textarea name="newReqRemarks" class="form-control" rows="4" placeholder="Any specific requirements..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100" name="submitQuotationBtn">Send Quotation Request</button>
            </form>
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