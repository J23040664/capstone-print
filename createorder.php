<?php
session_start();
// Include your database connection
include 'dbms.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SESSION['id'] == $_GET['id']) {
    $user_id = $_GET['id'];
    $user_info = "SELECT a.*, b.* FROM user a LEFT JOIN profile_images b ON a.img_id = b.img_id WHERE a.user_id = '$user_id'";
    $result_user_info = mysqli_query($conn, $user_info);
    $rowShowUserInfo = mysqli_fetch_assoc($result_user_info);
} else {
    header("Location: login.php");
    exit;
}

// Preload service prices (print + size + color)
$service_prices = [];
$sql_service = "SELECT service_id, service_price FROM service_list WHERE service_status = 'Available'";
$result_service = mysqli_query($conn, $sql_service);
while ($row = mysqli_fetch_assoc($result_service)) {
    $service_prices[$row['service_id']] = $row['service_price'];
}

// Preload finishing prices
$finishing_prices = [];
$sql_finishing = "SELECT finishing_id, finishing_price FROM finishing_list WHERE finishing_status = 'Available'";
$result_finishing = mysqli_query($conn, $sql_finishing);
while ($row = mysqli_fetch_assoc($result_finishing)) {
    $finishing_prices[$row['finishing_id']] = $row['finishing_price'];
}


// Generate next item_id
function getNextItemId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(item_id) AS max_id FROM order_detail");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'I' . str_pad($num, 7, '0', STR_PAD_LEFT);
}

// Generate next order_id
function getNextOrderId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(order_id) AS max_id FROM order_detail");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'O' . str_pad($num, 7, '0', STR_PAD_LEFT);
}
// Generate next item_id
function getNextFileId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(file_id) AS max_id FROM file");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'F' . str_pad($num, 7, '0', STR_PAD_LEFT);
}
// Generate next payment_id
function getNextPaymentId($conn)
{
    $result = mysqli_query($conn, "SELECT MAX(payment_id) AS max_id FROM payment");
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    if ($max_id) {
        $num = (int)substr($max_id, 1) + 1;
    } else {
        $num = 1;
    }
    return 'P' . str_pad($num, 7, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $service_id = $_POST['serviceType'];
    $paperSize = $_POST['paperSize'];
    $color_id = $_POST['color'];
    $copies = (int)$_POST['copies'];

    // pdf page count
    require_once 'vendor/autoload.php';

    $pdfPages = 1; // default fallback

    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] == 0) {
        // No need to move or upload to folder

        $file_tmp_path = $_FILES['pdfFile']['tmp_name'];

        // Create parser
        $parser = new \Smalot\PdfParser\Parser();

        // Parse PDF
        $pdf = $parser->parseFile($file_tmp_path);

        // Read file content to store in DB
        $file_content = addslashes(file_get_contents($file_tmp_path));

        // Get page count
        $details = $pdf->getDetails();
        if (isset($details['Pages'])) {
            $pdfPages = (int)$details['Pages'];
        } else {
            $pagesArray = $pdf->getPages();
            $pdfPages = count($pagesArray);
        }
    }

    // Set to variable used in rest of the code
    $pages = $pdfPages;

    $serviceCost = str_replace('RM ', '', $_POST['serviceCost']);
    $finishing1 = $_POST['finishing1'];
    $finishing2 = $_POST['finishing2'];
    $finishing3 = $_POST['finishing3'];
    $totalCost = str_replace('RM ', '', $_POST['totalCost']);
    $remarks = $_POST['remarks'] ?? '';

    $customerName = $_POST['customerName'];
    $orderStatus = "Pending";

    // Lookup service_desc and service_price
    $service_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT service_desc, service_price FROM service_list WHERE service_id = '$service_id'"));
    $service_desc = $service_data['service_desc'];
    $service_price = $service_data['service_price'];

    // Lookup paperSize desc
    $size_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT service_desc FROM service_list WHERE service_id = '$paperSize'"));
    $size_desc = $size_data['service_desc'];

    // Lookup color desc
    $color_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT service_desc FROM service_list WHERE service_id = '$color_id'"));
    $color_desc = $color_data['service_desc'];

    // Lookup finishing 1
    if ($finishing1 !== 'None') {
        $f1_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT finishing_desc, finishing_price FROM finishing_list WHERE finishing_id = '$finishing1'"));
        $f1_desc = $f1_data['finishing_desc'];
        $f1_price = $f1_data['finishing_price'];
    } else {
        $f1_desc = '';
        $f1_price = 0;
    }

    // Lookup finishing 2
    if ($finishing2 !== 'None') {
        $f2_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT finishing_desc, finishing_price FROM finishing_list WHERE finishing_id = '$finishing2'"));
        $f2_desc = $f2_data['finishing_desc'];
        $f2_price = $f2_data['finishing_price'];
    } else {
        $f2_desc = '';
        $f2_price = 0;
    }

    // Lookup finishing 3
    if ($finishing3 !== 'None') {
        $f3_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT finishing_desc, finishing_price FROM finishing_list WHERE finishing_id = '$finishing3'"));
        $f3_desc = $f3_data['finishing_desc'];
        $f3_price = $f3_data['finishing_price'];
    } else {
        $f3_desc = '';
        $f3_price = 0;
    }

    // Prepare finishing FK safe values (NULL if 'None')
    $finishing1_value = ($finishing1 !== 'None') ? $finishing1 : null;
    $finishing2_value = ($finishing2 !== 'None') ? $finishing2 : null;
    $finishing3_value = ($finishing3 !== 'None') ? $finishing3 : null;


    // Generate IDs
    $item_id = getNextItemId($conn);
    $order_id = getNextOrderId($conn);
    $file_id = getNextFileId($conn);
    $payment_id = getNextPaymentId($conn);

    // payment status set to pending, and will update during payment page
    $payment_status = 'Pending';

    $file_name = $_FILES['pdfFile']['name'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION); // eg: pdf
    $file_tmp_path = $_FILES['pdfFile']['tmp_name'];
    $file_content = addslashes(file_get_contents($file_tmp_path));

    // Set created_at
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $created_at = date('Y-m-d H:i:s');


    // Calculate finishing_total_price and finishing_quantity
    $finishing_total_price = $f1_price + $f2_price + $f3_price;

    $finishing_quantity = 0;
    if ($finishing1 !== 'None') $finishing_quantity++;
    if ($finishing2 !== 'None') $finishing_quantity++;
    if ($finishing3 !== 'None') $finishing_quantity++;




    // Insert into order table
    mysqli_query($conn, "INSERT INTO `order` (
        order_id, created_at, item_id, service_total_price, finishing_total_price, total_price, finishing_quantity, customer_id, customer_name, order_status, payment_id, payment_status
    ) VALUES (
        '$order_id', NOW(), '$item_id', '$serviceCost', '$finishing_total_price', '$totalCost', '$finishing_quantity', '$user_id', '$customerName', '$orderStatus', '$payment_id', '$payment_status'
    )");

    // Insert into payment
    mysqli_query($conn, "INSERT INTO `payment` (
        payment_id, order_id, total_price, payment_status
    ) VALUES (
        '$payment_id', '$order_id', $totalCost, '$payment_status'
    )");

    // Insert into order_detail
    $sql_insert = "INSERT INTO order_detail (
        item_id, order_id, file_id, service_id, service_desc, service_price, copies, pages, size, colour, service_total_price,
        finishing_1, finishing_desc1, finishing_price1,
        finishing_2, finishing_desc2, finishing_price2,
        finishing_3, finishing_desc3, finishing_price3,
        item_price, remarks, created_at
    ) VALUES (
        '$item_id', '$order_id', '$file_id', '$service_id', '$service_desc', $service_price, $copies, $pages, '$size_desc', '$color_desc', $serviceCost,
        " . ($finishing1_value !== null ? "'$finishing1_value'" : "NULL") . ", '$f1_desc', $f1_price,
        " . ($finishing2_value !== null ? "'$finishing2_value'" : "NULL") . ", '$f2_desc', $f2_price,
        " . ($finishing3_value !== null ? "'$finishing3_value'" : "NULL") . ", '$f3_desc', $f3_price,
        $totalCost, '$remarks', NOW()
    )";

    if (!mysqli_query($conn, $sql_insert)) {
        echo "Order Detail Insert Error: " . mysqli_error($conn);
        exit;
    }


    // Insert into file
    $sql_file_insert = "INSERT INTO file (
        file_id, order_id, item_id, file_name, file_path, file_type
    ) VALUES (
        '$file_id', '$order_id', '$item_id', '$file_name', '$file_content', '$file_type'
    )";

    if (!mysqli_query($conn, $sql_file_insert)) {
        echo "File Insert Error: " . mysqli_error($conn);
        exit;
    }

    // If everything is successful
    echo "<script>
    alert('Order submitted successfully!');
    window.location.href='payment.php?order_id=$order_id&id=$user_id';
    </script>";
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Keep your head unchanged -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Order</title>
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


    <main id="mainContent" class="main-content">
        <div class="container-fluid">
            <div class="mt-4 fw-bold">
                <span>Create New Order</span>
            </div>

            <!-- Order Form -->
            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="max-height: 80vh; overflow-y: auto;">
                <form id="printForm" method="POST" enctype="multipart/form-data">

                    <!-- Customer Name -->
                    <div class="mb-4">
                        <label for="customerName" class="form-label">Name:</label>
                        <input class="form-control" type="text" id="customerName" name="customerName" value="<?php echo $rowShowUserInfo['name']; ?>" required>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-4">
                        <label for="pdfFile" class="form-label">Upload PDF File:</label>
                        <input class="form-control" type="file" id="pdfFile" name="pdfFile" accept="application/pdf" required>
                    </div>

                    <!-- Service Type Dropdown -->
                    <div class="mb-4">
                        <label for="serviceType" class="form-label">Type Of Services:</label>
                        <select class="form-select" id="serviceType" name="serviceType">
                            <option value="None">-- Select Service --</option>
                            <?php
                            $sql = "SELECT service_id, service_desc FROM service_list WHERE service_status = 'Available' AND service_type = 'print'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['service_id'] . "'>" . $row['service_desc'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Paper Size Dropdown -->
                    <div class="mb-4">
                        <label for="paperSize" class="form-label">Paper Size:</label>
                        <select class="form-select" id="paperSize" name="paperSize">
                            <option value="None">-- Select Paper Size --</option>
                            <?php
                            $sql = "SELECT service_id, service_desc FROM service_list WHERE service_status = 'Available' AND service_type = 'size'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['service_id'] . "'>" . $row['service_desc'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Print Color Option -->
                    <div class="mb-4">
                        <label class="form-label">Print Colour:</label>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="color" value="S006" checked>
                                    <label class="form-check-label w-100">Black & White</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="color" value="S005">
                                    <label class="form-check-label w-100">Colour</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Copies Input -->
                    <div class="mb-4">
                        <label for="copies" class="form-label">Number of Copies:</label>
                        <input class="form-control" type="number" id="copies" name="copies" min="1" value="1" required>
                    </div>

                    <!-- Pages Count -->
                    <div class="mb-4">
                        <label for="pages" class="form-label">Number of Pages (Auto Calculate When Uploaded File):</label>
                        <input class="form-control" style="background-color: #EAECEF; cursor: not-allowed;" type="number" id="pages" name="pages" readonly>
                    </div>

                    <!-- Service Cost Output -->
                    <div class="mb-4">
                        <label class="form-label">Service Cost:</label>
                        <input class="form-control" style="background-color: #EAECEF; cursor: not-allowed;" type="text" id="serviceCost" name="serviceCost" value="RM 0.00" readonly>
                    </div>

                    <!-- Finishing Type 1 Dropdown -->
                    <div class="mb-4">
                        <label for="finishing1" class="form-label">Finishing 1:</label>
                        <select class="form-select finishing-select" id="finishing1" name="finishing1">
                            <option value="None">-- Select Finishing 1 --</option>
                            <?php
                            $sql = "SELECT finishing_id, finishing_desc FROM finishing_list WHERE finishing_status = 'Available'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['finishing_id'] . "'>" . $row['finishing_desc'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Finishing Type 2 Dropdown -->
                    <div class="mb-4">
                        <label for="finishing2" class="form-label">Finishing 2:</label>
                        <select class="form-select finishing-select" id="finishing2" name="finishing2">
                            <option value="None">-- Select Finishing 2 --</option>
                            <?php
                            $sql = "SELECT finishing_id, finishing_desc FROM finishing_list WHERE finishing_status = 'Available'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['finishing_id'] . "'>" . $row['finishing_desc'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Finishing Type 3 Dropdown -->
                    <div class="mb-4">
                        <label for="finishing3" class="form-label">Finishing 3:</label>
                        <select class="form-select finishing-select" id="finishing3" name="finishing3">
                            <option value="None">-- Select Finishing 3 --</option>
                            <?php
                            $sql = "SELECT finishing_id, finishing_desc FROM finishing_list WHERE finishing_status = 'Available'";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['finishing_id'] . "'>" . $row['finishing_desc'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <!-- Finishing Cost Output -->
                    <div class="mb-4">
                        <label class="form-label">Finishing Cost:</label>
                        <input class="form-control" style="background-color: #EAECEF; cursor: not-allowed;" type="text" id="finishingCost" name="finishingCost" value="RM 0.00" readonly>
                    </div>

                    <!-- Total Cost Output -->
                    <div class="mb-4">
                        <label class="form-label">Total Cost:</label>
                        <input class="form-control" style="background-color: #EAECEF; cursor: not-allowed;" type="text" id="totalCost" name="totalCost" value="RM 0.00" readonly>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="customerOrderlist.php?id=<?php echo $user_id; ?>" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn login-btn">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const topbar = document.getElementById('topNavbar');
        const maincontent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            topbar.classList.toggle('collapsed');
            maincontent.classList.toggle('collapsed');
        });
    </script>

    <!-- JS price maps + update logic -->
    <script>
        // Service prices map (service_id -> price)
        const servicePrices = <?php echo json_encode($service_prices); ?>;
        const finishingPrices = <?php echo json_encode($finishing_prices); ?>;

        function updateServiceCost() {
            const serviceTypeId = document.getElementById('serviceType').value;
            const paperSizeId = document.getElementById('paperSize').value;
            const colorId = document.querySelector('input[name="color"]:checked').value;
            const copies = parseInt(document.getElementById('copies').value) || 0;
            const pages = parseInt(document.getElementById('pages').value) || 0;

            if (serviceTypeId === 'None' || paperSizeId === 'None' || !colorId || copies <= 0 || pages <= 0) {
                document.getElementById('serviceCost').value = 'RM 0.00';
                return;
            }

            const servicePrice = servicePrices[serviceTypeId] || 0;
            const sizePrice = servicePrices[paperSizeId] || 0;
            const colorPrice = servicePrices[colorId] || 0;

            const cost = servicePrice * sizePrice * colorPrice * copies * pages;
            document.getElementById('serviceCost').value = 'RM ' + cost.toFixed(2);

            updateTotalCost();
        }

        function updateFinishingCost() {
            const f1 = document.getElementById('finishing1').value;
            const f2 = document.getElementById('finishing2').value;
            const f3 = document.getElementById('finishing3').value;

            // Calculate prices
            const p1 = f1 !== 'None' ? parseFloat(finishingPrices[f1] || 0) : 0;
            const p2 = f2 !== 'None' ? parseFloat(finishingPrices[f2] || 0) : 0;
            const p3 = f3 !== 'None' ? parseFloat(finishingPrices[f3] || 0) : 0;


            const cost = p1 + p2 + p3;
            document.getElementById('finishingCost').value = 'RM ' + cost.toFixed(2);

            updateTotalCost();
        }

        // UX logic: disable selected options in other dropdowns
        function updateFinishingDropdowns() {
            const selectedValues = [
                document.getElementById('finishing1').value,
                document.getElementById('finishing2').value,
                document.getElementById('finishing3').value
            ];

            document.querySelectorAll('.finishing-select').forEach(select => {
                const currentValue = select.value; // store current value

                Array.from(select.options).forEach(option => {
                    if (option.value !== 'None') {
                        // Disable if selected in another dropdown (but not this one)
                        option.disabled = selectedValues.includes(option.value) && currentValue !== option.value;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }


        // Add combined event handler
        function onFinishingChange() {
            updateFinishingDropdowns();
            updateFinishingCost();
        }

        // Add event listeners
        document.getElementById('finishing1').addEventListener('change', onFinishingChange);
        document.getElementById('finishing2').addEventListener('change', onFinishingChange);
        document.getElementById('finishing3').addEventListener('change', onFinishingChange);


        function updateTotalCost() {
            const serviceCostStr = document.getElementById('serviceCost').value.replace('RM ', '');
            const finishingCostStr = document.getElementById('finishingCost').value.replace('RM ', '');

            const serviceCost = parseFloat(serviceCostStr) || 0;
            const finishingCost = parseFloat(finishingCostStr) || 0;

            const total = serviceCost + finishingCost;
            document.getElementById('totalCost').value = 'RM ' + total.toFixed(2);
        }

        // Add event listeners
        document.getElementById('serviceType').addEventListener('change', updateServiceCost);
        document.getElementById('paperSize').addEventListener('change', updateServiceCost);
        document.querySelectorAll('input[name="color"]').forEach(el => el.addEventListener('change', updateServiceCost));
        document.getElementById('copies').addEventListener('input', updateServiceCost);
        document.getElementById('pages').addEventListener('input', updateServiceCost);

        document.getElementById('pdfFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type === 'application/pdf') {
                const fileReader = new FileReader();

                fileReader.onload = function() {
                    const typedarray = new Uint8Array(this.result);

                    pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                        const pageCount = pdf.numPages;
                        document.getElementById('pages').value = pageCount;
                    }, function(reason) {
                        console.error(reason);
                        alert('Failed to read PDF file!');
                    });
                };

                fileReader.readAsArrayBuffer(file);
            } else {
                document.getElementById('pages').value = '';
            }
        });
    </script>
</body>

</html>