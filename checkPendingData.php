<?php
include('dbms.php');
header('Content-Type: application/json');

$response = [];

$checkTodaySales = "SELECT SUM(total_price) AS total_sales_today FROM `order` WHERE DATE(created_at) = CURDATE()";
$queryCheckTodaySales = $conn->query($checkTodaySales);
if ($queryCheckTodaySales) {
    $row = $queryCheckTodaySales->fetch_assoc();
    $response['today_sales'] = number_format((float)$row['total_sales_today'], 2, '.', '');
} else {
    $response['today_sales_error'] = $conn->error;
}

$checkTodayOrders = "SELECT COUNT(*) AS total_order_today FROM `order` WHERE DATE(created_at) = CURDATE()";
$queryCheckTodayOrders = $conn->query($checkTodayOrders);
if ($queryCheckTodayOrders) {
    $row = $queryCheckTodayOrders->fetch_assoc();
    $response['today_orders'] = (int)$row['total_order_today'];
} else {
    $response['today_Orders_error'] = $conn->error;
}

$checkPendingOrders = "SELECT COUNT(*) AS total_pending_orders FROM `order` WHERE order_status = 'Pending'";
$queryCheckPendingOrders = $conn->query($checkPendingOrders);
if ($queryCheckPendingOrders) {
    $row = $queryCheckPendingOrders->fetch_assoc();
    $response['pending_orders'] = (int)$row['total_pending_orders'];
} else {
    $response['pending_orders_error'] = $conn->error;
}

$checkPendingQuotations = "SELECT COUNT(*) AS total_pending_quotations FROM `quotation` WHERE quotation_status = 'Pending'";
$queryCheckPendingQuotations = $conn->query($checkPendingQuotations);
if ($queryCheckPendingQuotations) {
    $row = $queryCheckPendingQuotations->fetch_assoc();
    $response['pending_quotations'] = (int)$row['total_pending_quotations'];
} else {
    $response['pending_quotations_error'] = $conn->error;
}

echo json_encode($response);

$conn->close();
exit;
?>