<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Order</title>

  <!-- Bootstrap and Bootstrap Icons CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Bootstrap JS Bundle -->
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

  <!-- Main Content -->
  <main id="mainContent" class="main-content">
    <div class="container-fluid">
      <div class="mt-3 fw-bold">
        <span>Create New Order</span>
      </div>

      <!-- Order Form -->
      <div class="container mt-4 bg-white p-4 rounded shadow-sm" style="max-height: 80vh; overflow-y: auto;">
        <form id="printForm" enctype="multipart/form-data">

          <!-- Customer Name Input -->
          <div class="mb-4">
            <label for="customerName" class="form-label">Customer Name:</label>
            <input type="text" class="form-control" id="customerName" required>
          </div>

          <!-- Email Input (Optional) -->
          <div class="mb-4">
            <label for="email" class="form-label">Email (optional):</label>
            <input type="email" class="form-control" id="email">
          </div>
          
          <!-- File Upload Field -->
          <div class="mb-4">
            <label for="file" class="form-label">Upload File (PDF only):</label>
            <input class="form-control" type="file" id="file" name="file" accept="application/pdf" required>
          </div>

          <!-- Service Type Dropdown -->
          <div class="mb-4">
            <label for="typeservices" class="form-label">Type Of Services:</label>
            <select class="form-select" id="serviceType" name="serviceType">
              <option value="None">-- Select Service --</option>
              <?php
              // Include your database connection
              include 'dbms.php'; // make sure this file connects to your DB

              $sql = "SELECT service_id, service_desc FROM service_list WHERE service_status = 'Available' and service_type = 'print'";
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
              // Include your database connection
              include 'dbms.php'; // make sure this file connects to your DB

              $sql = "SELECT service_id, service_desc FROM service_list WHERE service_status = 'Available' and service_type = 'size'";
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
                  <input class="form-check-input" type="radio" name="colour" value="S006" checked>
                  <label class="form-check-label w-100">Black & White</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="colour" value="S005">
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

          <!-- Pages Count (Read-only, to be updated via script) -->
          <div class="mb-4">
            <label for="pages" class="form-label">Number of Pages:</label>
            <input class="form-control" type="number" id="pages" name="pages" value="1"> 
          </div>

          <!-- Service Cost Output -->
          <div class="mb-4">
            <label class="form-label">Service Cost:</label>
            <input class="form-control" type="text" id="serviceCost" name="serviceCost" value="RM 0.00" readonly>
          </div>

          <!-- Finishing Type 1 Dropdown -->
          <div class="mb-4">
            <label for="finishing1" class="form-label">Finishing 1:</label>
            <select class="form-select" id="finishing1" name="finishing1">
              <option value="None">-- Select Finishing 1 --</option>
              <?php
              // Include your database connection
              include 'dbms.php'; // make sure this file connects to your DB

              $sql = "SELECT finishing_id, finishing_type FROM finishing_list WHERE finishing_status = 'Available'";
              $result = mysqli_query($conn, $sql);

              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='" . $row['finishing_id'] . "'>" . $row['finishing_type'] . "</option>";
              }
              ?>
            </select>
          </div>
          
          <!-- Finishing Type 2  Dropdown -->
          <div class="mb-4">
            <label for="finishing2" class="form-label">Finishing 2:</label>
            <select class="form-select" id="finishing2" name="finishing2">
              <option value="None">-- Select Finishing 2 --</option>
              <?php
              // Include your database connection
              include 'dbms.php'; // make sure this file connects to your DB

              $sql = "SELECT finishing_id, finishing_type FROM finishing_list WHERE finishing_status = 'Available'";
              $result = mysqli_query($conn, $sql);

              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='" . $row['finishing_id'] . "'>" . $row['finishing_type'] . "</option>";
              }
              ?>
            </select>
          </div>
          
          <!-- Finishing Type 3 Dropdown -->
          <div class="mb-4">
            <label for="finishing3" class="form-label">Finishing 2:</label>
            <select class="form-select" id="finishing3" name="finishing3">
              <option value="None">-- Select Finishing 3 --</option>
              <?php
              // Include your database connection
              include 'dbms.php'; // make sure this file connects to your DB

              $sql = "SELECT finishing_id, finishing_type FROM finishing_list WHERE finishing_status = 'Available'";
              $result = mysqli_query($conn, $sql);

              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='" . $row['finishing_id'] . "'>" . $row['finishing_type'] . "</option>";
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

          <!-- Remarks Textarea -->
          <div class="mb-5">
            <label for="remarks" class="form-label">Remarks:</label>
            <textarea class="form-control" rows="3" name="remarks" id="remarks" placeholder="Any specific requirements..."></textarea>
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

  <script>
    // Sidebar toggle logic
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const topNavbar = document.getElementById('topNavbar');
    const mainContent = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
      // Toggle 'collapsed' class on each section to show/hide sidebar
      sidebar.classList.toggle('collapsed');
      topNavbar.classList.toggle('collapsed');
      mainContent.classList.toggle('collapsed');
    });
    document.getElementById('printForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        // Collect all form data
        const formData = {
          customerName: document.getElementById('customerName').value,
          email: document.getElementById('email').value,
          file: document.getElementById('file').files[0],
          serviceType: document.getElementById('serviceType').value,
          paperSize: document.getElementById('paperSize').value,
          color: document.querySelector('input[name="color"]:checked').value,
          copies: document.getElementById('copies').value,
          pages: document.getElementById('pages').value,
          serviceCost: document.getElementById('serviceCost').value,
          finishing1: document.getElementById('finishing1').value,
          finishing2: document.getElementById('finishing2').value,
          finishing3: document.getElementById('finishing3').value,
          finishingCost: document.getElementById('finishingCost').value,
          totalCost: document.getElementById('totalCost').value,
          remarks: document.getElementById('remarks').value
        };

        // For file upload, we need to use FormData
        const formDataObj = new FormData();
        for (const key in formData) {
          formDataObj.append(key, formData[key]);
        }

        // Send data to server using AJAX
        fetch('process_order.php', {
          method: 'POST',
          body: formDataObj
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Order submitted successfully!');
            window.location.href = 'orderlist.html'; // Redirect after success
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while submitting the order.');
        });
      });

      // You can add other event listeners here to update costs in real-time
      // For example, when copies or pages change:
      document.getElementById('copies').addEventListener('change', updateCosts);
      document.getElementById('pages').addEventListener('change', updateCosts);
      
      function updateCosts() {
        // Your logic to calculate and update costs
        // This would update the serviceCost, finishingCost, and totalCost fields
      }
    </script>
</body>

</html>