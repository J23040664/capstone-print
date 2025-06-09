<?php
// Include your database connection
include 'dbms.php';

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      overflow-x: hidden;
      background-color: #fff;
    }

    /* Sidebar styling */
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

    /* Top navbar positioning */
    .top-navbar {
      margin-left: 240px;
      transition: margin-left 0.3s ease;
    }

    .top-navbar.collapsed {
      margin-left: 80px;
    }

    /* Main content positioning */
    .main-content {
      margin-left: 240px;
      transition: margin-left 0.3s ease;
      padding: 1rem;
    }

    .main-content.collapsed {
      margin-left: 80px;
    }

    /* Responsive adjustments */
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
        <a href="orderlist.html" class="nav-link"><i class="bi bi-card-list"></i><span>Manage Orders</span></a>
      </li>
    </ul>
  </div>

  <!-- Top Navbar -->
  <nav id="topNavbar" class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3 top-navbar">
    <div class="container-fluid">
      <button class="btn btn-outline-secondary me-2" id="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>

      <!-- User dropdown on the right -->
      <div class="d-flex align-items-center ms-auto">
        <div class="dropdown">
          <button class="btn dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
            <img src="./assets/icon/userpicture.png" class="rounded-circle" width="30" height="30"
              alt="profile_picture" />
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


    <main id="mainContent" class="main-content">
        <div class="container-fluid">
            <div class="mt-3 fw-bold">
                <span>Create New Order</span>
            </div>

            <!-- Order Form -->
            <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="max-height: 80vh; overflow-y: auto;">
                <form id="printForm" enctype="multipart/form-data">

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
                        <label for="pages" class="form-label">Number of Pages:</label>
                        <input class="form-control" type="number" id="pages" name="pages" min="1" value="1" required>
                    </div>

                    <!-- Service Cost Output -->
                    <div class="mb-4">
                        <label class="form-label">Service Cost:</label>
                        <input class="form-control" type="text" id="serviceCost" name="serviceCost" value="RM 0.00" readonly>
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
                        <input class="form-control" type="text" id="finishingCost" name="finishingCost" value="RM 0.00" readonly>
                    </div>

                    <!-- Total Cost Output -->
                    <div class="mb-4">
                        <label class="form-label">Total Cost:</label>
                        <input class="form-control" type="text" id="totalCost" name="totalCost" value="RM 0.00" readonly>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="orderlist.html" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

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



    </script>
</body>
</html>
