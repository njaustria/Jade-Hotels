<?php
include 'admin_class.php';
$crud = new Action();

$total_revenue = $crud->getTotalRevenue();
$check_ins = $crud->getCheckIns();
$check_outs = $crud->getCheckOuts();
$total_booked = $crud->getTotalBooked(); // Assuming a method to get total booked
?>

<style>
.dashboard { padding: 20px; background: #f8f9fa; min-height: 100vh; }
.dashboard h1 { font-size: 2rem; font-weight: 600; color: #2c3e50; margin-bottom: 30px; }

.cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
.card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}
.card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.15); }

.card-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
.card-title { font-size: 14px; color: #6c757d; font-weight: 500; margin: 0; }
.card-menu { color: #6c757d; cursor: pointer; }

.card-value { font-size: 2.5rem; font-weight: 700; margin: 10px 0; }
.card-change { font-size: 13px; }
.positive { color: #28a745; }
.negative { color: #dc3545; }
.change-text { color: #6c757d; margin-left: 5px; }

.revenue { border-left: 4px solid #17a2b8; }
.revenue .card-value { color: #17a2b8; }
.total-booked { border-left: 4px solid #ffc107; } /* New color for total booked */
.total-booked .card-value { color: #ffc107; }
.checkin { border-left: 4px solid #28a745; }
.checkin .card-value { color: #28a745; }
.checkout { border-left: 4px solid #6f42c1; }
.checkout .card-value { color: #6f42c1; }

.chart {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.chart h3 { font-size: 1.2rem; font-weight: 600; color: #2c3e50; margin-bottom: 20px; }
.chart-placeholder {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

@media (max-width: 768px) {
    .cards { grid-template-columns: 1fr; }
}
</style>

<div class="dashboard">
    <h1>Dashboard</h1>

    <div class="cards">
        <div class="card revenue">
            <div class="card-header">
                <p class="card-title">Total Revenue</p>
                <span class="card-menu">⋯</span>
            </div>
            <div class="card-value">₱<?php echo number_format($total_revenue['total'], 0); ?></div>
            <div class="card-change">
                <span class="<?php echo $total_revenue['percentage_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $total_revenue['percentage_change'] >= 0 ? '↗' : '↘'; ?>
                    <?php echo abs($total_revenue['percentage_change']); ?>%
                </span>
                <span class="change-text">from last week</span>
            </div>
        </div>

        <div class="card total-booked">
            <div class="card-header">
                <p class="card-title">Total Booked</p>
                <span class="card-menu">⋯</span>
            </div>
            <div class="card-value"><?php echo $total_booked['total']; ?></div>
            <div class="card-change">
                <span class="<?php echo $total_booked['percentage_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $total_booked['percentage_change'] >= 0 ? '↗' : '↘'; ?>
                    <?php echo abs($total_booked['percentage_change']); ?>%
                </span>
                <span class="change-text">from last week</span>
            </div>
        </div>

        <div class="card checkin">
            <div class="card-header">
                <p class="card-title">Total Check-ins</p>
                <span class="card-menu">⋯</span>
            </div>
            <div class="card-value"><?php echo $check_ins['total']; ?></div>
            <div class="card-change">
                <span class="<?php echo $check_ins['percentage_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $check_ins['percentage_change'] >= 0 ? '↗' : '↘'; ?>
                    <?php echo abs($check_ins['percentage_change']); ?>%
                </span>
                <span class="change-text">from last week</span>
            </div>
        </div>

        <div class="card checkout">
            <div class="card-header">
                <p class="card-title">Total Check-outs</p>
                <span class="card-menu">⋯</span>
            </div>
            <div class="card-value"><?php echo $check_outs['total']; ?></div>
            <div class="card-change">
                <span class="<?php echo $check_outs['percentage_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $check_outs['percentage_change'] >= 0 ? '↗' : '↘'; ?>
                    <?php echo abs($check_outs['percentage_change']); ?>%
                </span>
                <span class="change-text">from last week</span>
            </div>
        </div>
    </div>

    <div class="chart">
        <h3>Revenue Trend</h3>
        <div class="chart-placeholder">
            Revenue chart will be displayed here
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Card menu functionality
    document.querySelectorAll('.card-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Card menu clicked');
        });
    });

    // Auto-refresh every 5 minutes
    setInterval(() => {
        console.log('Refreshing dashboard data...');
    }, 300000);
});
</script>