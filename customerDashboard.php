<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar {
      background-color: #ee5351;
      min-height: 100vh;
      padding-top: 20px;
    }
    .sidebar a {
      color: white;
      padding: 10px 20px;
      display: block;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #d44846;
    }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block sidebar">
      <div class="position-sticky">
        <h5 class="text-white text-center mb-4">My Dashboard</h5>
        <a href="#">🏠 Home</a>
        <a href="#">📄 My Orders</a>
        <a href="#">💳 Payments</a>
        <a href="#">📁 Files</a>
        <a href="#">⚙️ Settings</a>
        <a href="#">🚪 Logout</a>
      </div>
    </nav>

    <!-- Main content -->
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-3 border-bottom">
        <div>
            <h2>Hello, Yong 👋</h2>
            <p class="text-muted">Here’s a quick summary of your printing activity.</p>
        </div>
        </div>

        <!-- Shortcut Buttons -->
        <div class="mb-4">
        <div class="btn-group" role="group" aria-label="Shortcut actions">
            <a href="create_order.php" class="btn btn-outline-primary">➕ Create Order</a>
            <a href="orderlist_customer.php" class="btn btn-outline-success">📄 My Orders</a>
            <a href="files_customer.php" class="btn btn-outline-info">📁 View Files</a>
            <a href="payment_portal.php" class="btn btn-outline-warning">💳 Make Payment</a>
        </div>
        </div>


      <!-- Cards -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Total Orders</h5>
            <p class="fs-4 fw-bold text-primary">12</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Pending Payments</h5>
            <p class="fs-4 fw-bold text-warning">3</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5>Completed Jobs</h5>
            <p class="fs-4 fw-bold text-success">9</p>
          </div>
        </div>
      </div>

      <!-- Recent Orders Table -->
      <div class="card p-4">
        <h5 class="mb-3">Recent Orders</h5>
        <table class="table table-bordered table-striped">
          <thead class="table-light">
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Status</th>
              <th>Payment</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>ORD001</td>
              <td>2025-06-16</td>
              <td><span class="badge bg-warning">Processing</span></td>
              <td><span class="badge bg-danger">Unpaid</span></td>
              <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
            </tr>
            <tr>
              <td>ORD002</td>
              <td>2025-06-12</td>
              <td><span class="badge bg-success">Completed</span></td>
              <td><span class="badge bg-success">Paid</span></td>
              <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
            </tr>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</div>

</body>
</html>
