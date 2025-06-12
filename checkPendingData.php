<?php
include('dbms.php');
header('Content-Type: application/json');

$response = [];

$checkPendingOrders = "SELECT COUNT(*) AS total FROM `order` WHERE order_status = 'Pending'";
$queryCheckPendingOrders = $conn->query($checkPendingOrders);
if ($queryCheckPendingOrders) {
    $row = $queryCheckPendingOrders->fetch_assoc();
    $response['pending_orders'] = (int)$row['total'];
} else {
    $response['pending_orders_error'] = $conn->error;
}

$checkPendingQuotations = "SELECT COUNT(*) AS total FROM `quotation` WHERE quotation_status = 'Pending'";
$queryCheckPendingQuotations = $conn->query($checkPendingQuotations);
if ($queryCheckPendingQuotations) {
    $row = $queryCheckPendingQuotations->fetch_assoc();
    $response['pending_quotations'] = (int)$row['total'];
} else {
    $response['pending_quotations_error'] = $conn->error;
}

echo json_encode($response);

$conn->close();
exit;
?>

async function fetchPendingCounts() {
try {
const response = await fetch('checkPendingData.php');
const data = await response.json();

document.getElementById('pendingOrders').textContent = data.pending_orders ?? 0;
document.getElementById('pendingQuotations').textContent = data.pending_quotations ?? 0;
} catch (error) {
console.error('Failed to fetch pending counts:', error);
}
}

fetchPendingCounts();
setInterval(fetchPendingCounts, 5000);