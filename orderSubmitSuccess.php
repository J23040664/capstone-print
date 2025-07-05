<?php
session_start();
$order_id = $_GET['order_id'];
$user_id = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Submitted</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #FFF6F5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card-custom {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .icon {
            background: linear-gradient(135deg, #27ad5a, #32cd74);
            border-radius: 50%;
            width: 64px;
            height: 64px;
            line-height: 64px;
            font-size: 32px;
            color: white;
            margin: 0 auto 1rem;
            position: relative;
        }

        .icon::after {
            content: "✔";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @media (max-width: 480px) {
            .card-custom {
                margin: 1rem;
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="card card-custom">
        <div class="icon"></div>
        <h2 class="fw-semibold mb-2">Thank you for your order!</h2>
        <p class="text-muted mb-4">Your request has been submitted. We will process it shortly.</p>
        <div class="d-flex gap-2 flex-wrap justify-content-center">
            <!-- view order -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                <a href="editOrder.php?order_id=<?php echo $order_id; ?>&id=<?php echo $user_id; ?>" class="btn btn-outline-success flex-fill">View Order</a>
            <?php } ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Staff") { ?>
                <a href="orderDetails.php?order_id=<?php echo $order_id; ?>&id=<?php echo $user_id; ?>" class="btn btn-outline-success flex-fill">View Order</a>
            <?php } ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") { ?>
                <a href="orderDetails.php?id=<?php echo $user_id; ?>" class="btn btn-success flex-fill">View Order Details</a>
            <?php } ?>

            <!-- back to dashboard -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Admin") { ?>
                <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="btn btn-success flex-fill">Back to home</a>
            <?php } ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Staff") { ?>
                <a href="adminDashboard.php?id=<?php echo $user_id; ?>" class="btn btn-success flex-fill">Back to home</a>
            <?php } ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "Customer") { ?>
                <a href="customerDashboard.php?id=<?php echo $user_id; ?>" class="btn btn-success flex-fill">Back to home</a>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>