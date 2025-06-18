<?php
include 'dbms.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_payment'])) {
        echo "<script>alert('Payment was not completed. You can complete your payment from the order list page.'); window.location.href = 'customerOrderlist.php?id=" . $_POST['user_id'] . "';</script>";
        exit;
    }

    $order_id = $_POST['order_id'];
    $payment_type = $_POST['payment_method'] === 'Online' ? 'Online' : 'Walk-in';

    // Update payment table
    $sql_update_payment = "UPDATE `payment` SET payment_status = 'Paid', payment_type = ?, payment_date = NOW() WHERE order_id = ?";
    $stmt_update_payment = $conn->prepare($sql_update_payment);
    $stmt_update_payment->bind_param("ss", $payment_type, $order_id);
    $stmt_update_payment->execute();

    // Update order table
    $sql_update_order = "UPDATE `order` SET payment_status = 'Paid' WHERE order_id = ?";
    $stmt_update_order = $conn->prepare($sql_update_order);
    $stmt_update_order->bind_param("s", $order_id);
    $stmt_update_order->execute();

    if ($stmt_update_payment->affected_rows > 0 && $stmt_update_order->affected_rows > 0) {
        echo "<script>alert('Payment successful!'); window.location.href = 'customerOrderlist.php?id=" . $_POST['user_id'] . "';</script>";
        exit;
    } else {
        echo "<script>alert('Payment update failed.');</script>";
    }
}

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}
$order_id = $_GET['order_id'];

$sql_order = "SELECT * FROM `order` WHERE order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("s", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
if (!$order) {
    die("Order not found.");
}

$sql_detail = "SELECT service_desc, service_total_price, pages, copies, 
                      finishing_desc1, finishing_price1,
                      finishing_desc2, finishing_price2,
                      finishing_desc3, finishing_price3
               FROM `order_detail` WHERE order_id = ?";
$stmt_detail = $conn->prepare($sql_detail);
$stmt_detail->bind_param("s", $order_id);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; overflow-x: hidden; }
    .content-box { background: #fff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05); }
    .online-portals { display: none; }
    .sidebar {
      width: 240px;
      background-color: #343a40;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      overflow-y: auto;
      z-index: 1030;
      transition: all 0.3s ease;
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
    .top-navbar {
      margin-left: 240px;
      transition: margin-left 0.3s ease;
    }
    .top-navbar.collapsed {
      margin-left: 80px;
    }
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
        transform: translateX(0);
        left: 0;
      }
      .sidebar.collapsed {
        transform: translateX(-100%);
      }
      .top-navbar,
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
<div id="sidebar" class="sidebar d-flex flex-column p-3">
  <div class="s_logo fs-5">
    <span>PrintEase</span>
  </div>
  <hr />
  <ul class="nav nav-pills flex-column">
    <li class="nav-item">
      <a href="dashboard.html" class="nav-link"><i class="bi bi-house"></i><span>Dashboard</span></a>
    </li>
    <li class="nav-item">
      <a href="customerOrderlist.php" class="nav-link active"><i class="bi bi-card-list"></i><span>My Orders</span></a>
    </li>
  </ul>
</div>

<nav id="topNavbar" class="navbar navbar-light bg-light shadow-sm px-3 top-navbar">
  <div class="container-fluid">
    <button class="btn btn-outline-secondary me-2" id="toggleSidebar">
      <i class="bi bi-list"></i>
    </button>
    <span class="navbar-brand mb-0 h1">Payment</span>
  </div>
</nav>

<main id="mainContent" class="main-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="content-box">
          <h5>🧾 Order Summary</h5>
          <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
          <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
          <hr>
          <?php while ($row = $result_detail->fetch_assoc()): ?>
            <div class="mb-3">
              <p><strong><?= $row['service_desc'] ?> - RM<?= number_format($row['service_total_price'], 2) ?></strong></p>
              <p>Pages: <?= $row['pages'] ?> | Copies: <?= $row['copies'] ?></p>
              <?php
                for ($i = 1; $i <= 3; $i++) {
                  $desc = $row["finishing_desc$i"];
                  $price = $row["finishing_price$i"];
                  if (!empty($desc) && $price > 0) {
                    echo "<p>Finishing: $desc - RM" . number_format($price, 2) . "</p>";
                  }
                }
              ?>
            </div>
            <hr>
          <?php endwhile; ?>
          <h6>Total Price: <strong>RM<?= number_format($order['total_price'], 2) ?></strong></h6>
        </div>
      </div>
      <div class="col-md-6">
        <div class="content-box">
          <!-- Choose Payment Method Form Section -->
          <h5>💳 Choose Payment Method</h5>
          <form method="POST">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($order['customer_id']) ?>">

            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment_method" id="walkin" value="Walk-in" checked>
              <label class="form-check-label" for="walkin">
                Walk-in Payment (Pay at Counter)
                <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="popover" title="How Walk-in Payment Works" data-bs-content="Walk-in orders are not processed or printed until payment is made at the store. This helps avoid uncollected or fake orders.">
                  <i class="bi bi-question-circle"></i>
                </button>
              </label>
            </div>
            <div class="alert alert-warning mt-2" id="walkinNotice" style="display: block;">
              Walk-in orders will only be printed once payment is made at the store.
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="payment_method" id="online" value="Online">
              <label class="form-check-label" for="online">Online Payment</label>
            </div>

            <div class="online-portals" id="portalOptions">
              <label class="form-label">Select Payment Portal</label>
              <select class="form-select" name="portal">
                <option selected disabled>-- Choose Portal --</option>
                <option value="TnG">Touch 'n Go</option>
                <option value="FPX">FPX</option>
                <option value="GrabPay">GrabPay</option>
              </select>
            </div>

            <div id="onlineAgreement" class="form-text text-danger mt-3" style="display: none;">
              <strong>Note:</strong> Once online payment is made, your order will be processed immediately. <br>
              Orders paid online are <strong>non-refundable and cannot be canceled</strong>. <br>
              Please collect using your <strong>Order ID</strong> at the store.
            </div>

            <div class="d-flex justify-content-between mt-4">
              <button type="submit" class="btn btn-success w-50 me-2">Proceed to Pay</button>
              <button type="submit" name="cancel_payment" class="btn btn-outline-danger w-50">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Enable Popover & Bootstrap Functionality -->
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

  const walkinRadio = document.getElementById('walkin');
  const onlineRadio = document.getElementById('online');
  const portalOptions = document.getElementById('portalOptions');

  function togglePortal() {
    portalOptions.style.display = onlineRadio.checked ? 'block' : 'none';
    document.getElementById('onlineAgreement').style.display = onlineRadio.checked ? 'block' : 'none';
    document.getElementById('walkinNotice').style.display = walkinRadio.checked ? 'block' : 'none';
  }

  walkinRadio.addEventListener('change', togglePortal);
  onlineRadio.addEventListener('change', togglePortal);

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle=\"popover\"]');
  popoverTriggerList.forEach(el => new bootstrap.Popover(el));
</script>

</body>
</html>
