<?php
include('dbms.php');
header('Content-Type: application/json');

$sql = "SELECT COUNT(*) AS total FROM `order` WHERE order_status = 'Pending'";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['pending_orders' => (int)$row['total']]);
} else {
    echo json_encode(['error' => $conn->error]);
}

$conn->close();
exit;
?>

<!-- // Update pending orders count every 5 seconds
async function fetchPendingOrders() {
try {
const response = await fetch('checkPendingOrder.php');
const data = await response.json();
const pendingOrdersElement = document.getElementById('pendingOrders');
pendingOrdersElement.textContent = data.pending_orders ?? 0;
} catch (error) {
console.error('Failed to fetch pending orders:', error);
}
}

fetchPendingOrders();
setInterval(fetchPendingOrders, 5000); -->