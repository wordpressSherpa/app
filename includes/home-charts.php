<!-- Home Page Charts - PERFORMANCE CURVE / AVG CHART -->
<div class="charts-row">

    <div class="curve-card">

        <div class="curve-title">
            Performance Curve & Trend
        </div>
        <div class="curve-subtitle">
            Green = Daily Score • White = Trend • Gold = A Grade • Gray = B Grade
        </div>
        <canvas id="performanceChart"></canvas>

    </div>

<!-- RADAR CHART -->
<?php
            /* ==========================
       Process Breakdown Radar
    ========================== */

            $avgRules = 0;
            $avgEmotion = 0;
            $avgSetup = 0;
            $avgContext = 0;
            $avgExecution = 0;

            $radarQuery = "
SELECT
    AVG(rules_followed) AS rules_avg,
    AVG(emotional_control) AS emotion_avg,
    AVG(setup_quality) AS setup_avg,
    AVG(context_quality) AS context_avg,
    AVG(execution_management) AS execution_avg
FROM session_scores
";

            $radarResult = $conn->query($radarQuery);

            if ($radarResult && $radarResult->num_rows > 0) {
                $radar = $radarResult->fetch_assoc();

                $avgRules =
                    round($radar['rules_avg'] ?? 0, 1);

                $avgEmotion =
                    round($radar['emotion_avg'] ?? 0, 1);

                $avgSetup =
                    round($radar['setup_avg'] ?? 0, 1);

                $avgContext =
                    round($radar['context_avg'] ?? 0, 1);

                $avgExecution =
                    round($radar['execution_avg'] ?? 0, 1);
            }

            ?>


            <div class="curve-card">

                <div class="curve-title">
                    Process Breakdown
                </div>

                <div class="curve-subtitle">
                    Where your process is strongest and weakest
                </div>

                <canvas id="radarChart"></canvas>
                
            </div>

        </div>


      <script>

window.curveLabels =
<?php echo json_encode($curveLabels); ?>;

window.curveScores =
<?php echo json_encode($curveScores); ?>;

window.curveAverage =
<?php echo json_encode($curveAverage); ?>;

window.radarData = [
    <?php echo $avgRules; ?>,
    <?php echo $avgEmotion; ?>,
    <?php echo $avgSetup; ?>,
    <?php echo $avgContext; ?>,
    <?php echo $avgExecution; ?>
];

</script>