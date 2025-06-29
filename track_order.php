<?php

include 'dbms.php';
session_start();

$order_status = null;
$order_details = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? '';

    if (!empty($order_id)) {
        // --- IMPORTANT: Replace this with actual database query ---
        // Example: Query your database to find the order
        // This is a MOCK-UP. You need to write actual database interaction.
        // For demonstration, let's simulate some data:

        $stmt = $conn->prepare("SELECT order_id, service_type, quantity, status, order_date FROM orders WHERE order_id = ?");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order_data = $result->fetch_assoc();
            $order_details = [
                'ID' => htmlspecialchars($order_data['order_id']),
                'Service Type' => htmlspecialchars($order_data['service_type']),
                'Quantity' => htmlspecialchars($order_data['quantity']),
                'Date' => htmlspecialchars($order_data['order_date']),
                'Status' => htmlspecialchars($order_data['status'])
            ];
            $order_status = htmlspecialchars($order_data['status']);
        } else {
            $error_message = "Order ID '$order_id' not found.";
        }
        $stmt->close();

        // --- END MOCK-UP ---

    } else {
        $error_message = "Please enter an Order ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Art & Print SS15</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    require_once 'includes/header.php';
    ?>

    <main>
        <section id="track-order" class="section active">
            <div class="container">
                <h2 class="section-title">Track Your Order</h2>

                <div class="order-tracking-form">
                    <p class="section-test">Enter your Order ID below to check the status of your print job.</p>
                    <form action="track_order.php" method="POST">
                        <div class="form-group">
                            <label for="order_id">Order ID:</label>
                            <input type="text" id="order_id" name="order_id" placeholder="e.g., APSS15-12345" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Track Order</button>
                    </form>

                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <?php echo $error_message; ?>
                        </div>
                    <?php elseif ($order_details): ?>
                        <div class="order-details-card">
                            <h3>Order #<?php echo $order_details['ID']; ?></h3>
                            <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($order_status); ?>"><?php echo $order_status; ?></span></p>
                            <ul>
                                <?php foreach ($order_details as $key => $value): ?>
                                    <?php if ($key !== 'ID' && $key !== 'Status'):?>
                                        <li><strong><?php echo $key; ?>:</strong> <?php echo $value; ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                            <p class="section-test">
                                <?php
                                    if (strtolower($order_status) == 'pending') {
                                        echo "Your order is currently awaiting processing. We'll start soon!";
                                    } elseif (strtolower($order_status) == 'processing') {
                                        echo "Good news! Your order is being actively worked on. We'll notify you when it's ready.";
                                    } elseif (strtolower($order_status) == 'ready') {
                                        echo "Your order is complete and ready for collection! Please pick it up at your convenience.";
                                    } else {
                                        echo "Thank you for your patience.";
                                    }
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php
    require_once 'includes/footer.php';
    ?>
    <script src="js/smooth-scrolling.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>