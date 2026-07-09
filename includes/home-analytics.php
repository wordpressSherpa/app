<?php 

if (!$hasData) {

    $captureClass = 'rating-neutral';
    $executionClass = 'rating-neutral';
    $leakageClass = 'rating-neutral';

} else {

    if ($capturePercent >= 80) {
        $captureClass = 'rating-great';
        $captureLabel = '⚡ ELITE CAPTURE';
    }
    elseif ($capturePercent >= 60) {
        $captureClass = 'rating-average';
        $captureLabel = '✓ SOLID';
    }
    elseif ($capturePercent >= 50) {
        $captureClass = 'rating-concerning';
        $captureLabel = '⚠ NEEDS WORK';
    }
    else {
        $captureClass = 'rating-poor';
        $captureLabel = '🚨 LEAKING EDGE';
    }

    if ($averageExecution >= 90) {
        $executionClass = 'rating-great';
    }
    elseif ($averageExecution >= 89) {
        $executionClass = 'rating-average';
    }
    elseif ($averageExecution >= 79) {
        $executionClass = 'rating-concerning';
    }
    else {
        $executionClass = 'rating-poor';
    }

    if ($leakagePercent <= 20) {
        $leakageClass = 'rating-great';
    }
    elseif ($leakagePercent <= 35) {
        $leakageClass = 'rating-average';
    }
    elseif ($leakagePercent <= 50) {
        $leakageClass = 'rating-concerning';
    }
    else {
        $leakageClass = 'rating-poor';
    }

}

?>

<!-- Home Page Analytics Boxes -->
<div class="analytics-grid">

    <div class="analytics-card <?php echo $captureClass; ?>">

        <div class="analytics-label">
            Capture Quality
        </div>

        <div class="analytics-status">
            <?php echo $captureLabel; ?>
        </div>

        <div class="analytics-value">
            <?php echo round($capturePercent); ?>%
        </div>

    </div>

    <div class="analytics-card realized-card <?php echo $captureClass; ?>">

        <div class="analytics-label">
            Realized R
        </div>

        <div class="analytics-value">
            <?php echo round($totalRealizedR, 2); ?>R
        </div>

    </div>

    <div class="analytics-card missed-card <?php echo $captureClass; ?>">

        <div class="analytics-label">
            Missed R
        </div>

        <div class="analytics-value">
            <?php echo round($totalMissedR, 2); ?>R
        </div>

    </div>

    <div class="analytics-card <?php echo $executionClass; ?>">

        <div class="analytics-label">
            Average Execution
        </div>

        <div class="analytics-status">
            <?php echo $executionLabel; ?>
        </div>

        <div class="analytics-value">
            <?php echo round($averageExecution); ?>%
        </div>

    </div>

    <div class="analytics-card <?php echo $leakageClass; ?>">

        <div class="analytics-label">
            Opportunity Leakage
        </div>

        <div class="analytics-status tier-badge">
            <?php echo $leakageTitle; ?>
        </div>

        <div class="analytics-value">
            <?php echo round($leakagePercent); ?>%
        </div>

    </div>

</div>