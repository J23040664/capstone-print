<?php
include('navbar.php');
include('dbms.php');

// === Get today's date ===
// $today = date("Y-m-d"); // For live usage
$today = "2025-06-09"; // For testing

$startDate = date("Y-m-d", strtotime("-6 days", strtotime($today)));

// === Initialize arrays ===
$orderCounts = [];
$orderCosts = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date("Y-m-d", strtotime("-$i days", strtotime($today)));
    $orderCounts[$date] = 0;
    $orderCosts[$date] = 0.0;
}
ksort($orderCounts); // sort both arrays
ksort($orderCosts);

// === Query 1: Get order count per day ===
$sqlCount = "SELECT DATE(created_at) AS order_day, COUNT(*) AS total
             FROM `order`
             WHERE created_at BETWEEN '$startDate' AND '$today'
             GROUP BY DATE(created_at)
             ORDER BY order_day ASC";

$resultCount = $conn->query($sqlCount);
if ($resultCount) {
    while ($row = $resultCount->fetch_assoc()) {
        $date = $row['order_day'];
        if (array_key_exists($date, $orderCounts)) {
            $orderCounts[$date] = (int)$row['total'];
        }
    }
}

// === Query 2: Get total cost per day ===
$sqlCost = "SELECT DATE(created_at) AS order_day, SUM(cost) AS total_cost
            FROM `order`
            WHERE created_at BETWEEN '$startDate' AND '$today'
            GROUP BY DATE(created_at)
            ORDER BY order_day ASC";

$resultCost = $conn->query($sqlCost);
if ($resultCost) {
    while ($row = $resultCost->fetch_assoc()) {
        $date = $row['order_day'];
        if (array_key_exists($date, $orderCosts)) {
            $orderCosts[$date] = (float)$row['total_cost'];
        }
    }
}

// === Prepare data for JavaScript ===
$labels = [];
$dataCounts = [];
$dataCosts = [];

foreach ($orderCounts as $date => $count) {
    $labels[] = date("D", strtotime($date)); // e.g., Mon, Tue
    $dataCounts[] = $count;
    $dataCosts[] = round($orderCosts[$date], 2);
}

// === Get today's order count to display ===
$todayCount = $orderCounts[$today] ?? 0;
$todayCost = $orderCosts[$today] ?? 0.0;
?>

<div class="mt-3 fw-bold">
    <span>Dashboard</span>
</div>

<div class="row">
    <div class="col-md-4 mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fs-6">Daily Order</h5>
                <p class="card-text fs-3"><?php echo $todayCount; ?></p>
                <canvas id="orderCountChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fs-6">Daily Cost</h5>
                <canvas id="orderCostChart" height="100" class="mt-4"></canvas>
            </div>
        </div>
    </div>

    <!-- <div class="col-md-4 mt-4"> -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fs-6">Pending Order</h5>
            <p class="card-text fs-3">1000</p>
        </div>
    </div>
    <!-- </div> -->
</div>

</main>
</div>
</div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?php echo json_encode($labels); ?>;
    const orderCounts = <?php echo json_encode($dataCounts); ?>;
    const orderCosts = <?php echo json_encode($dataCosts); ?>;

    // Chart 1: Order Count
    new Chart(document.getElementById('orderCountChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Order Count',
                data: orderCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart 2: Total Cost
    new Chart(document.getElementById('orderCostChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Cost (RM)',
                data: orderCosts,
                fill: false,
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RM ' + value;
                        }
                    }
                }
            }
        }
    });
</script>

</body>

</html>