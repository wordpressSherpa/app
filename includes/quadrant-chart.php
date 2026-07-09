<?php

$scatterPoints = [];

$scatterQuery = "
SELECT
    trade_date,
    daily_score,
    difference_r,
    letter_grade
FROM daily_scores
WHERE daily_score IS NOT NULL
AND journal_id = $journalId
ORDER BY trade_date
";

$scatterResult = $conn->query($scatterQuery);

while ($row = $scatterResult->fetch_assoc()) {

    $scatterPoints[] = [
        'x' => round($row['difference_r'], 2),
        'y' => round($row['daily_score'], 1),
        'grade' => $row['letter_grade'],
        'date' => date(
            'F j, Y',
            strtotime($row['trade_date'])
        )
    ];
}

?>

<div class="curve-card">

    <div class="curve-title">
        Luck vs Discipline
    </div>

    <div class="curve-subtitle">
        Are profits coming from process or luck?
    </div>

    <canvas id="disciplineChart"></canvas>

</div>

<div class="quadrant-legend">

    <span>🚨 Process Failure</span>
    <span>🍀 Lucky Win</span>
    <span>✅ Good Loss</span>
    <span>⚡ Disciplined Winner</span>

</div>

<script>

    window.disciplineData =
        <?php echo json_encode($scatterPoints); ?>;

</script>