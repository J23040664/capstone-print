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