<?php
// app/views/dashboard/index.php

$title = "Dashboard";

// Example data for testing UI (in production, fetch these from your controller/database)
$totalRevenue = 12500.50;
$totalShoesSold = 350;
$totalSales = 80;
$totalUsers = 120;

$revenuePerDayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$revenuePerDayData = [1500, 2000, 1800, 2200, 2100, 1900, 1900];

$shoesByCategoryLabels = ['Sneakers', 'Running', 'Formal'];
$shoesByCategoryData = [40, 35, 25];

$shoesSoldByCategoryLabels = ['Sneakers', 'Running', 'Formal'];
$shoesSoldByCategoryData = [150, 120, 80];

$reviewsPerDayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$reviewsPerDayData = [10, 15, 7, 12, 20, 5, 8];

include __DIR__ . '/../inc/header.php';
?>
<div class="dashboard-container">
    <h1>Dashboard</h1>

    <!-- Metrics Summary -->
    <div class="metrics-summary">
        <div class="metric-card">
            <h2>Total Revenue</h2>
            <p>$<?php echo number_format($totalRevenue, 2); ?></p>
        </div>
        <div class="metric-card">
            <h2>Total Shoes Sold</h2>
            <p><?php echo $totalShoesSold; ?></p>
        </div>
        <div class="metric-card">
            <h2>Total Sales</h2>
            <p><?php echo $totalSales; ?></p>
        </div>
        <div class="metric-card">
            <h2>Total Users</h2>
            <p><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="charts-section">
        <!-- revenue line chart: spans 2 columns on large screens -->
        <div class="chart-card line-graph-wide">
            <h3>Revenue Per Day</h3>
            <section class="graph">
                <canvas id="revenueChart"></canvas>
            </section>
        </div>
        <!-- Pie chart (1 column) -->
        <div class="chart-card">
            <h3>Shoes by Category</h3>
            <section class="graph">
                <canvas id="shoesByCategoryChart"></canvas>
            </section>
        </div>
        <!-- Bar chart (1 column) -->
        <div class="chart-card">
            <h3>Shoes Sold by Category</h3>
            <section class="graph">
                <canvas id="shoesSoldByCategoryChart"></canvas>
            </section>
        </div>
        <!-- reviews per day line chart: spans 2 columns on large screens -->
        <div class="chart-card line-graph-wide">
            <h3>Reviews Per Day</h3>
            <section class="graph">
                <canvas id="reviewsChart"></canvas>
            </section>
        </div>
    </div>
</div>

<!-- Include Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Per Day (Line Chart)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($revenuePerDayLabels); ?>,
            datasets: [{
                label: 'Revenue ($)',
                data: <?php echo json_encode($revenuePerDayData); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false, // Removed fill
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Shoes by Category (Pie Chart)
    const shoesByCatCtx = document.getElementById('shoesByCategoryChart').getContext('2d');
    new Chart(shoesByCatCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($shoesByCategoryLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($shoesByCategoryData); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Shoes Sold by Category (Bar Chart)
    const shoesSoldCtx = document.getElementById('shoesSoldByCategoryChart').getContext('2d');
    new Chart(shoesSoldCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($shoesSoldByCategoryLabels); ?>,
            datasets: [{
                label: 'Shoes Sold',
                data: <?php echo json_encode($shoesSoldByCategoryData); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Reviews Per Day (Line Chart)
    const reviewsCtx = document.getElementById('reviewsChart').getContext('2d');
    new Chart(reviewsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($reviewsPerDayLabels); ?>,
            datasets: [{
                label: 'Reviews',
                data: <?php echo json_encode($reviewsPerDayData); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                fill: false, // Removed fill
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>