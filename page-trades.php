<?php

require_once 'config/database.php';
include 'includes/functions.php';
include 'includes/header.php';

// Get Trade Data for the Current Journal
$currentJournal = $_SESSION['journal_id'] ?? 1;

$lossStats = [
    'total' => 0,
    'Market' => 0,
    'Behavior' => 0,
    'Model' => 0
];

$sql = "
SELECT
    t.primary_reason,
    COUNT(*) AS total
FROM trades t
INNER JOIN session_scores s
    ON t.session_id = s.id
WHERE
    s.journal_id = ?
    AND t.outcome = 'Expense'
GROUP BY t.primary_reason
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentJournal);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $reason = trim($row['primary_reason']);
    $count = (int) $row['total'];

    // Total losing trades
    $lossStats['total'] += $count;

    // Assign each reason to its category
    switch ($reason) {

        // Behavior
        case 'Missing Criteria':
        case 'Emotion':
        case 'Tilt':
        case 'Technical Mistake':
            $lossStats['Behavior'] += $count;
            break;

        // Market
        case 'Offsides':
        case 'Consolidation':
        case 'Stop Hunt':
            $lossStats['Market'] += $count;
            break;

        // Model
        case 'Stopped Then Reached TP':
            $lossStats['Model'] += $count;
            break;
    }
}

// Calculate percentages
$marketPercent = $lossStats['total'] > 0
    ? round(($lossStats['Market'] / $lossStats['total']) * 100)
    : 0;

$behaviorPercent = $lossStats['total'] > 0
    ? round(($lossStats['Behavior'] / $lossStats['total']) * 100)
    : 0;

$modelPercent = $lossStats['total'] > 0
    ? round(($lossStats['Model'] / $lossStats['total']) * 100)
    : 0;

// =========================
// Break Even Stats
// =========================
$beStats = [
    'total' => 0,
    'Would Have Been Profitable' => 0,
    'Would Have Been Expense' => 0
];

$sql = "
SELECT
    t.be_outcome,
    COUNT(*) AS total
FROM trades t
INNER JOIN session_scores s
    ON t.session_id = s.id
WHERE
    s.journal_id = ?
    AND t.outcome = 'Break Even'
GROUP BY t.be_outcome
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentJournal);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $outcome = trim($row['be_outcome']);
    $count = (int) $row['total'];

    $beStats['total'] += $count;

    if (isset($beStats[$outcome])) {
        $beStats[$outcome] = $count;
    }
}

$profitablePercent = $beStats['total'] > 0
    ? round(($beStats['Would Have Been Profitable'] / $beStats['total']) * 100)
    : 0;

$expensePercent = $beStats['total'] > 0
    ? round(($beStats['Would Have Been Expense'] / $beStats['total']) * 100)
    : 0;

// =========================
// Top Mistakes
// =========================

$topMistakes = [];

$sql = "
SELECT
    t.primary_reason,
    COUNT(*) AS total
FROM trades t
INNER JOIN session_scores s
    ON t.session_id = s.id
WHERE
    s.journal_id = ?
    AND t.outcome = 'Expense'
GROUP BY t.primary_reason
ORDER BY total DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentJournal);
$stmt->execute();

$topMistakes = $stmt->get_result();

?>

<div class="dashboard">

    <div class="container">

        <h1 class="page-title">Trade Analysis</h1>

        <div class="page-trades">
            <!-- =========================
                Loss Analysis
            ========================== -->
            <div class="analysis-card loss-card">

                <h2>Expense Analysis</h2>

                <div class="analysis-summary">

                    <div class="loss-bar">

                        <div class="loss-header">
                            <span>🌐 Market Variance</span>
                            <span><?= $lossStats['Market']; ?> (<?= $marketPercent; ?>%)</span>
                        </div>

                        <div class="bar-track">
                            <div class="bar-fill market" style="width: <?= $marketPercent; ?>%;"></div>
                        </div>

                    </div>

                    <div class="loss-bar">

                        <div class="loss-header">
                            <span>🧠 Behavior Variance</span>
                            <span><?= $lossStats['Behavior']; ?> (<?= $behaviorPercent; ?>%)</span>
                        </div>

                        <div class="bar-track">
                            <div class="bar-fill behavior" style="width: <?= $behaviorPercent; ?>%;"></div>
                        </div>

                    </div>

                    <div class="loss-bar">

                        <div class="loss-header">
                            <span>🔧 Model Variance</span>
                            <span><?= $lossStats['Model']; ?> (<?= $modelPercent; ?>%)</span>
                        </div>

                        <div class="bar-track">
                            <div class="bar-fill model" style="width: <?= $modelPercent; ?>%;"></div>
                        </div>

                    </div>

                    <div class="summary-total-wrapper">
                        <div class="summary-total">
                            <span>Total Expense Trades</span>
                            <strong><?= $lossStats['total']; ?></strong>
                        </div>
                    </div>

                </div>

            </div>

            <!-- =========================
                Break Even Analysis
            ========================== -->

            <div class="analysis-card be-card">

                <h2>Break Even Analysis</h2>

                <div class="analysis-summary">

                    <div class="loss-bar">

                        <div class="loss-header">
                            <span>✅ Would Have Been Profitable</span>
                            <span><?= $beStats['Would Have Been Profitable']; ?>
                                (<?= $profitablePercent; ?>%)
                            </span>
                        </div>

                        <div class="bar-track">
                            <div class="bar-fill profitable" style="width: <?= $profitablePercent; ?>%;">
                            </div>
                        </div>

                    </div>

                    <div class="loss-bar">

                        <div class="loss-header">
                            <span>❌ Would Have Been Expense</span>
                            <span><?= $beStats['Would Have Been Expense']; ?>
                                (<?= $expensePercent; ?>%)
                            </span>
                        </div>

                        <div class="bar-track">
                            <div class="bar-fill expense" style="width: <?= $expensePercent; ?>%;">
                            </div>
                        </div>

                    </div>

                    <div class="summary-total-wrapper">
                        <div class="summary-total">
                            <span>Total Break Even Trades</span>
                            <strong><?= $beStats['total']; ?></strong>
                        </div>
                    </div>

                </div>

            </div>

            <!-- =========================
                Mistake Analysis
            ========================== -->

            <div class="analysis-card history-card">

                <h2>Top Mistakes</h2>

                <div class="analysis-summary">

                    <?php while ($row = $topMistakes->fetch_assoc()):

                        $count = (int) $row['total'];

                        $percent = $lossStats['total'] > 0
                            ? round(($count / $lossStats['total']) * 100)
                            : 0;

                        // Pick bar color
                        switch ($row['primary_reason']) {

                            case 'Missing Criteria':
                            case 'Emotion':
                            case 'Tilt':
                            case 'Technical Mistake':
                                $barClass = 'behavior';
                                $icon = '🧠';
                                break;

                            case 'Offsides':
                            case 'Consolidation':
                            case 'Stop Hunt':
                                $barClass = 'market';
                                $icon = '🌐';
                                break;

                            default:
                                $barClass = 'model';
                                $icon = '🔧';
                                break;
                        }

                        ?>

                        <div class="loss-bar">

                            <div class="loss-header">

                                <span>
                                    <?= $icon; ?>
                                    <?= htmlspecialchars($row['primary_reason']); ?>
                                </span>

                                <span>
                                    <?= $count; ?>
                                    (
                                    <?= $percent; ?>%)
                                </span>

                            </div>

                            <div class="bar-track">

                                <div class="bar-fill <?= $barClass; ?>" style="width: <?= $percent; ?>%;"></div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            </div>


        </div>

    </div>

</div>

<script src="assets/charts.js"></script>

</div>

<!-- Get Footer -->
<?php include 'includes/footer.php'; ?>