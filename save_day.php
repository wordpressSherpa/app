<?php

require_once 'config/database.php';

session_start();

$journalId = $_SESSION['journal_id'] ?? 1;

if (!isset($_POST['trade_date']) || !isset($_POST['sessions'])) {
    die("Invalid request.");
}

$trade_date = $_POST['trade_date'];

$totalProcessScore = 0;
$totalSessionScore = 0;
$validSessions = 0;

$totalRealizedR = 0;
$totalMissedR = 0;

$sessionData = [];

/*
|--------------------------------------------------------------------------
| Process Sessions
|--------------------------------------------------------------------------
*/

foreach ($_POST['sessions'] as $session) {
    $rules = trim($session['rules'] ?? '');
    $emotion = trim($session['emotion'] ?? '');
    $setup = trim($session['setup'] ?? '');
    $context = trim($session['context'] ?? '');
    $execution = trim($session['execution'] ?? '');

    $realizedR =
        ($session['realized_r'] === '')
        ? 0
        : floatval($session['realized_r']);

    $missedR =
        ($session['missed_r'] === '')
        ? 0
        : floatval($session['missed_r']);

    $scoreFields = [];

    if ($rules !== '')
        $scoreFields[] = floatval($rules);
    if ($emotion !== '')
        $scoreFields[] = floatval($emotion);
    if ($setup !== '')
        $scoreFields[] = floatval($setup);
    if ($context !== '')
        $scoreFields[] = floatval($context);
    if ($execution !== '')
        $scoreFields[] = floatval($execution);

    if (
        count($scoreFields) === 0 &&
        $realizedR == 0 &&
        $missedR == 0
    ) {
        continue;
    }

    $processScore = 0;

    if (count($scoreFields) > 0) {
        $processScore =
            array_sum($scoreFields) /
            count($scoreFields);
    }

    $penalty = $missedR * 3;

    $sessionScore =
        max(
            0,
            $processScore - $penalty
        );

    $totalProcessScore += $processScore;
    $totalSessionScore += $sessionScore;

    $totalRealizedR += $realizedR;
    $totalMissedR += $missedR;

    $validSessions++;

    $sessionData[] = [

        'name' => $session['name'],

        'rules' =>
            ($rules === '' ? null : $rules),

        'emotion' =>
            ($emotion === '' ? null : $emotion),

        'setup' =>
            ($setup === '' ? null : $setup),

        'context' =>
            ($context === '' ? null : $context),

        'execution' =>
            ($execution === '' ? null : $execution),

        'realized_r' =>
            $realizedR,

        'missed_r' =>
            $missedR,

        'process_score' =>
            round($processScore, 2),

        'session_score' =>
            round($sessionScore, 2),

        'pnl_result' =>
            'Flat',
        
        'trades' => $session['trades'] ?? []
    ];
}

if ($validSessions == 0) {
    die("No valid sessions entered.");
}

/*
|--------------------------------------------------------------------------
| Daily Calculations
|--------------------------------------------------------------------------
*/

$processScore =
    round(
        $totalProcessScore /
        $validSessions,
        2
    );

$dailyScore =
    round(
        $totalSessionScore /
        $validSessions,
        2
    );

/* BONUS POINS FOR NO MISSED R
if ($totalMissedR == 0)
{
$dailyScore += 10;
}
*/

$dailyScore =
    max(
        0,
        min(
            100,
            $dailyScore
        )
    );

$differenceR =
    round(
        $totalRealizedR -
        $totalMissedR,
        2
    );

/*
|--------------------------------------------------------------------------
| Grade
|--------------------------------------------------------------------------
*/

$grade = "F";

if ($dailyScore >= 97)
    $grade = "A+";
elseif ($dailyScore >= 93)
    $grade = "A";
elseif ($dailyScore >= 90)
    $grade = "A-";
elseif ($dailyScore >= 87)
    $grade = "B+";
elseif ($dailyScore >= 83)
    $grade = "B";
elseif ($dailyScore >= 80)
    $grade = "B-";
elseif ($dailyScore >= 77)
    $grade = "C+";
elseif ($dailyScore >= 73)
    $grade = "C";
elseif ($dailyScore >= 70)
    $grade = "C-";
elseif ($dailyScore >= 60)
    $grade = "D";

/*
|--------------------------------------------------------------------------
| Existing Day?
|--------------------------------------------------------------------------
*/

$check = $conn->prepare("
    SELECT id
    FROM daily_scores
    WHERE trade_date = ?
    AND journal_id = ?
");

$check->bind_param(
    "si",
    $trade_date,
    $journalId
);

$check->execute();

$result =
    $check->get_result();

if ($result->num_rows > 0) {
    $existing =
        $result->fetch_assoc();

    $dailyId =
        $existing['id'];

    $deleteSessions =
        $conn->prepare("
            DELETE FROM session_scores
            WHERE daily_id = ?
        ");

    $deleteSessions->bind_param(
        "i",
        $dailyId
    );

    $deleteSessions->execute();

    $update = $conn->prepare("
        UPDATE daily_scores
        SET
            process_score=?,
            daily_score=?,
            letter_grade=?,
            realized_r=?,
            missed_r=?,
            difference_r=?
        WHERE id=?
    ");

    $update->bind_param(
        "ddsdddi",
        $processScore,
        $dailyScore,
        $grade,
        $totalRealizedR,
        $totalMissedR,
        $differenceR,
        $dailyId
    );

    $update->execute();
} else {
    $insert = $conn->prepare("
        INSERT INTO daily_scores
(
    trade_date,
    journal_id,
    process_score,
    daily_score,
    letter_grade,
    realized_r,
    missed_r,
    difference_r
)
        VALUES
(?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insert->bind_param(
        "siddsddd",
        $trade_date,
        $journalId,
        $processScore,
        $dailyScore,
        $grade,
        $totalRealizedR,
        $totalMissedR,
        $differenceR
    );

    $insert->execute();

    $dailyId =
        $conn->insert_id;
}


/*
|--------------------------------------------------------------------------
| Save Sessions
|--------------------------------------------------------------------------
*/

foreach ($sessionData as $sessionIndex => $session) {
    $stmt = $conn->prepare("
        INSERT INTO session_scores
        (
            daily_id,
            session_name,
            rules_followed,
            emotional_control,
            setup_quality,
            context_quality,
            execution_management,
            realized_r,
            missed_r,
            process_score,
            session_score,
            pnl_result
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isiiiiidddds",
        $dailyId,
        $session['name'],
        $session['rules'],
        $session['emotion'],
        $session['setup'],
        $session['context'],
        $session['execution'],
        $session['realized_r'],
        $session['missed_r'],
        $session['process_score'],
        $session['session_score'],
        $session['pnl_result']
    );

    $stmt->execute();

    if ($stmt->error) {
        die($stmt->error);
    }

    $sessionId = $conn->insert_id;

    if (!empty($_POST['sessions'][$sessionIndex]['trades'])) {

        foreach ($_POST['sessions'][$sessionIndex]['trades'] as $tradeIndex => $trade) {

            $outcome = $trade['outcome'] ?? '';

            if ($outcome == '') {
                continue;
            }

            $reason = !empty($trade['primary_reason'])
                ? $trade['primary_reason']
                : null;

            $beOutcome = !empty($trade['be_outcome'])
                ? $trade['be_outcome']
                : null;

            $tradeStmt = $conn->prepare("
            INSERT INTO trades
            (
                session_id,
                trade_number,
                outcome,
                primary_reason,
                be_outcome
            )
            VALUES
            (?, ?, ?, ?, ?)
        ");

            $tradeNumber = $tradeIndex + 1;

            $tradeStmt->bind_param(
                "iisss",
                $sessionId,
                $tradeNumber,
                $outcome,
                $reason,
                $beOutcome
            );
            $tradeStmt->execute();

        }

    }
}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;
?>