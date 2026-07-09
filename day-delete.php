<?php

require_once 'config/database.php';

$date = $_GET['date'] ?? '';

if (!$date)
{
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Find Day
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM daily_scores
    WHERE trade_date = ?
");

$stmt->bind_param("s", $date);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    $dailyId = $row['id'];

    /*
    |--------------------------------------------------------------------------
    | Delete Sessions
    |--------------------------------------------------------------------------
    */

    $deleteSessions = $conn->prepare("
        DELETE FROM session_scores
        WHERE daily_id = ?
    ");

    $deleteSessions->bind_param(
        "i",
        $dailyId
    );

    $deleteSessions->execute();

    /*
    |--------------------------------------------------------------------------
    | Delete Day
    |--------------------------------------------------------------------------
    */

    $deleteDay = $conn->prepare("
        DELETE FROM daily_scores
        WHERE id = ?
    ");

    $deleteDay->bind_param(
        "i",
        $dailyId
    );

    $deleteDay->execute();
}

header("Location: index.php");
exit;