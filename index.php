<?php

require_once 'config/database.php';
include 'includes/functions.php';
include 'includes/header.php'; 

?>

<div class="dashboard">

    <!-- Home Page Month Stats -->
    <?php //include 'includes/home-stats.php'; ?>

    <!-- Home Page Achievements -->
    <?php include 'includes/home-achievements.php'; ?>

    <div class="month-nav">

        <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>">
            ◀
        </a>

        <h1>
            <?php
            echo date(
                'F Y',
                strtotime($monthStart)
            );
            ?>
        </h1>

        <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>">
            ▶
        </a>

    </div>

    <!-- Home Page Calendar -->
    <?php include 'includes/home-calendar.php'; ?>

    <!-- Home Page Streak Boxes -->
    <?php include 'includes/home-analytics.php'; ?>

    <!-- Home Page Charts -->
    <?php include 'includes/home-charts.php'; ?>

    <!-- Home Page Quadrant Chart -->
    <?php include 'includes/quadrant-chart.php'; ?>

</div>

<script src="assets/charts.js"></script>

</div>

<!-- Get Footer -->
<?php include 'includes/footer.php'; ?>