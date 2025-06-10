<?php
include('navbar.php');
include('dbms.php');

// === Get today's date ===
// $today = date("Y-m-d"); // Use system date
$today = "2025-01-06"; // Uncomment this for testing fixed date

// === Calculate the start date (6 days ago) ===
$startDate = date("Y-m-d", strtotime("-6 days", strtotime($today)));

// === Initialize array for past 7 days (default count = 0) ===
$dateLabels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date("Y-m-d", strtotime("-$i days", strtotime($today)));
    $dateLabels[$date] = 0;
}
ksort($dateLabels); // Sort dates from oldest to newest

// === SQL to get total orders per day in past week ===
$sql = "SELECT DATE(order_date) AS order_day, COUNT(*) AS total
        FROM `order`
        WHERE order_date BETWEEN '$startDate' AND '$today'
        GROUP BY DATE(order_date)
        ORDER BY order_day ASC";

$result = $conn->query($sql);

// === Update $dateLabels with actual counts ===
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['order_day'];
        if (array_key_exists($date, $dateLabels)) {
            $dateLabels[$date] = (int)$row['total'];
        }
    }
}

// === Prepare arrays for JavaScript ===
$labels = [];
$data = [];
foreach ($dateLabels as $date => $count) {
    $labels[] = date("D", strtotime($date)); // e.g., "Mon", "Tue"
    $data[] = $count;
}

// === Get today's order count to display ===
$todayCount = $dateLabels[$today] ?? 0;
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
                <canvas id="weeklyOrderChart" width="600" height="400"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fs-6">Daily Cost</h5>
                <p class="card-text fs-3">1000</p>
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
    const ctx = document.getElementById('weeklyOrderChart').getContext('2d');

    const weeklyOrderChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Orders',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.raw}`;
                        }
                    }
                },
                legend: {
                    display: true
                }
            }
        }
    });
</script>
</body>

</html>