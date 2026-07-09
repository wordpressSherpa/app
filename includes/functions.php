<?php

session_start();

/* ==========================
   Multi-Journal Feature
========================== */

$journalQuery = "
SELECT id, name
FROM journals
ORDER BY name
";

$journalResult = $conn->query($journalQuery);

$journals = [];

while ($row = $journalResult->fetch_assoc()) {
    $journals[] = $row;
}

$journalId = isset($_GET['journal_id'])
    ? (int) $_GET['journal_id']
    : 1;


if (isset($_GET['journal_id'])) {
    $_SESSION['journal_id'] = (int) $_GET['journal_id'];
}

$journalId = $_SESSION['journal_id'] ?? 1;

$journalInfoQuery = "
SELECT start_date
FROM journals
WHERE id = $journalId
LIMIT 1
";

$journalInfoResult = $conn->query($journalInfoQuery);
$journalStartDate = null;

if (
    $journalInfoResult &&
    $journalInfoResult->num_rows > 0
) {
    $journalInfo =
        $journalInfoResult->fetch_assoc();

    $journalStartDate =
        $journalInfo['start_date'];
}

if (
    !isset($_GET['month']) &&
    !isset($_GET['year']) &&
    !empty($journalStartDate)
) {

    $currentMonth =
        date(
            'n',
            strtotime($journalStartDate)
        );

    $currentYear =
        date(
            'Y',
            strtotime($journalStartDate)
        );

} else {

    $currentMonth = isset($_GET['month'])
        ? intval($_GET['month'])
        : date('n');

    $currentYear = isset($_GET['year'])
        ? intval($_GET['year'])
        : date('Y');

}

$monthStart = sprintf(
    "%04d-%02d-01",
    $currentYear,
    $currentMonth
);

$monthEnd = date(
    'Y-m-t',
    strtotime($monthStart)
);

$query = "
SELECT *
FROM daily_scores
WHERE journal_id = $journalId
AND trade_date BETWEEN '$monthStart' AND '$monthEnd'
ORDER BY trade_date
";

$result = $conn->query($query);

$days = [];
$today = date('Y-m-d');

$totalScore = 0;
$totalRealizedR = 0;
$totalMissedR = 0;
$aDays = 0;

$bestDay = null;
$worstDay = null;

while ($row = $result->fetch_assoc()) {
    $days[$row['trade_date']] = $row;

    $score = floatval($row['daily_score']);

    $totalScore += $score;

    $totalRealizedR += floatval($row['realized_r']);
    $totalMissedR += floatval($row['missed_r']);

    if (str_contains($row['letter_grade'], 'A')) {
        $aDays++;
    }

    if ($bestDay === null || $score > $bestDay) {
        $bestDay = $score;
    }

    if ($worstDay === null || $score < $worstDay) {
        $worstDay = $score;
    }
}

$totalDaysEntered = count($days);
$hasData = ($totalDaysEntered > 0);

$averageScore =
    $totalDaysEntered > 0
    ? round($totalScore / $totalDaysEntered, 1)
    : 0;

$totalDifferenceR =
    round(
        $totalRealizedR - $totalMissedR,
        2
    );

function getGrade($score)
{
    if ($score >= 97) {
        return "A+";
    }
    if ($score >= 93) {
        return "A";
    }
    if ($score >= 90) {
        return "A-";
    }
    if ($score >= 87) {
        return "B+";
    }
    if ($score >= 83) {
        return "B";
    }
    if ($score >= 80) {
        return "B-";
    }
    if ($score >= 77) {
        return "C+";
    }
    if ($score >= 73) {
        return "C";
    }
    if ($score >= 70) {
        return "C-";
    }
    if ($score >= 60) {
        return "D";
    }

    return "F";
}

function getColorClass($grade)
{
    if (str_contains($grade, 'A')) {
        return "green";
    }
    if (str_contains($grade, 'B')) {
        return "lightgreen";
    }
    if (str_contains($grade, 'C')) {
        return "yellow";
    }
    if (str_contains($grade, 'D')) {
        return "orange";
    }

    return "red";
}

/* ==========================
   Monthly Analytics
========================== */

$capturePercent = 0;
$leakagePercent = 0;

if (($totalRealizedR + $totalMissedR) > 0) {
    $capturePercent =
        round(
            ($totalRealizedR /
                ($totalRealizedR + $totalMissedR))
            * 100,
            1
        );

    $leakagePercent =
        round(
            ($totalMissedR /
                ($totalRealizedR + $totalMissedR))
            * 100,
            1
        );
}

$leakageClass = 'leakage-good';
$leakageTitle = '⚡⚡⚡ ELITE ';

if ($leakagePercent >= 40) {

    $leakageClass = 'leakage-alert';
    $leakageTitle = '⚠️ CRITICAL';

} elseif ($leakagePercent >= 20) {

    $leakageClass = 'leakage-caution';
    $leakageTitle = '🚨 WARNING';
}

/* ==========================
   Average Execution
========================== */

$averageExecution = 0;

$executionQuery = "
SELECT AVG(execution_management) AS avg_execution
FROM session_scores ss
JOIN daily_scores ds
ON ss.daily_id = ds.id
WHERE ds.journal_id = $journalId
AND ds.trade_date BETWEEN '$monthStart' AND '$monthEnd'
";

$executionResult =
    $conn->query($executionQuery);

if (
    $executionResult &&
    $executionResult->num_rows > 0
) {
    $executionRow =
        $executionResult->fetch_assoc();

    $averageExecution = round($executionRow['avg_execution'] ?? 0, 1);
}

/* ==========================
   Execution Rating
========================== */

$executionLabel = "🚨 Weak";
$executionClass = "danger-card";

if ($averageExecution >= 93) {
    $executionLabel = "⚡ Elite";
    $executionClass = "elite-card";
} elseif ($averageExecution >= 88) {
    $executionLabel = "✅ Strong";
    $executionClass = "solid-card";
} elseif ($averageExecution >= 80) {
    $executionLabel = "⚠ Needs Work";
    $executionClass = "warning-card";
} else {
    $executionLabel = "🚨 Weak";
    $executionClass = "danger-card";
}

/* ==========================
   Capture Quality Rating
========================== */

$captureLabel = "⚡ Elite Capture";
$captureClass = "elite-card";

if ($leakagePercent >= 40) {
    $captureLabel = "🚨 Major Leakage";
    $captureClass = "danger-card";
} elseif ($leakagePercent >= 30) {
    $captureLabel = "⚠ Leakage Risk";
    $captureClass = "warning-card";
} elseif ($leakagePercent >= 20) {
    $captureLabel = "✓ Solid Capture";
    $captureClass = "solid-card";
}

/* ==========================
   Current A Streak
========================== */

$currentAStreak = 0;

$streakQuery = "
SELECT letter_grade
FROM daily_scores
WHERE journal_id = $journalId
ORDER BY trade_date DESC
";

$streakResult = $conn->query($streakQuery);

while ($row = $streakResult->fetch_assoc()) {
    if (
        in_array(
            $row['letter_grade'],
            ['A+', 'A', 'A-']
        )
    ) {
        $currentAStreak++;
    } else {
        break;
    }
}

/* ==========================
   Longest A Streak
========================== */

$longestAStreak = 0;
$tempStreak = 0;

$allGradesQuery = "
SELECT letter_grade
FROM daily_scores
WHERE journal_id = $journalId
ORDER BY trade_date
";

$allGradesResult =
    $conn->query($allGradesQuery);

while ($row = $allGradesResult->fetch_assoc()) {
    if (
        in_array(
            $row['letter_grade'],
            ['A+', 'A', 'A-']
        )
    ) {
        $tempStreak++;

        if ($tempStreak > $longestAStreak) {
            $longestAStreak =
                $tempStreak;
        }
    } else {
        $tempStreak = 0;
    }
}

/* ==========================
   Streak Card Styling
========================== */

if ($hasData) {

    $currentStreakClass =
        ($currentAStreak > 0)
        ? 'achievement-green'
        : 'achievement-neutral';

    $bestStreakClass =
        ($longestAStreak > 0)
        ? 'achievement-green'
        : 'achievement-neutral';

} else {

    $currentStreakClass =
        'achievement-neutral';

    $bestStreakClass =
        'achievement-neutral';

}

/* ==========================
   Page Variables
========================== */

if ($totalDaysEntered > 0) {

    $averageGrade =
        getGrade($averageScore);

} else {

    $averageGrade = '-';

}

if ($totalDaysEntered == 0) {

    $monthlyGradeClass = 'grade-empty';

} else {

    $monthlyGradeClass = 'grade-f';

    if (str_contains($averageGrade, 'A')) {
        $monthlyGradeClass = 'grade-a';
    } elseif (str_contains($averageGrade, 'B')) {
        $monthlyGradeClass = 'grade-b';
    } elseif (str_contains($averageGrade, 'C')) {
        $monthlyGradeClass = 'grade-c';
    } elseif (str_contains($averageGrade, 'D')) {
        $monthlyGradeClass = 'grade-d';
    }

}

$totalDaysInMonth =
    date('t', strtotime($monthStart));

$firstDay =
    date('w', strtotime($monthStart));

/* ==========================
   Performance Curve
========================== */

$curveLabels = [];
$curveScores = [];
$curveAverage = [];

$curveQuery = "
SELECT trade_date, daily_score
FROM daily_scores
WHERE journal_id = $journalId
ORDER BY trade_date
";

$curveResult = $conn->query($curveQuery);

$tempScores = [];

while ($row = $curveResult->fetch_assoc()) {
    $score =
        round(
            $row['daily_score'],
            1
        );

    $curveLabels[] =
        date(
            'M j',
            strtotime($row['trade_date'])
        );

    $curveScores[] = $score;

    $tempScores[] = $score;

    /*
        Rolling Average
        Uses all available days
        until we have 30.
    */

    $window =
        array_slice(
            $tempScores,
            -30
        );

    $curveAverage[] =
        round(
            array_sum($window)
            /
            count($window),
            1
        );
}

/* ==========================
   Months
========================== */

$prevMonth = $currentMonth - 1;
    $prevYear = $currentYear;

    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }

    $nextMonth = $currentMonth + 1;
    $nextYear = $currentYear;

    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

?>