<!-- Home Page Calendar -->

<div class="calendar-grid">

    <div class="header">Sun</div>
    <div class="header">Mon</div>
    <div class="header">Tue</div>
    <div class="header">Wed</div>
    <div class="header">Thu</div>
    <div class="header">Fri</div>
    <div class="header">Sat</div>
    <div class="header week-header">Week</div>

    <?php

    $cellCounter = 0;
    $weekNumber = 1;

    for ($i = 0; $i < $firstDay; $i++) {
        echo "<div class='empty'></div>";
        $cellCounter++;
    }

    $weekScores = [];
    $weekDifferenceR = [];
    $weekMissedR = [];

    for ($day = 1; $day <= $totalDaysInMonth; $day++) {
        $date =
            sprintf(
                "%04d-%02d-%02d",
                $currentYear,
                $currentMonth,
                $day
            );

        if (isset($days[$date])) {
            $grade =
                $days[$date]['letter_grade'];

            $score =
                round(
                    $days[$date]['daily_score']
                );

            $differenceR =
                round(
                    $days[$date]['difference_r'],
                    2
                );

            $weekScores[] = $score;
            $weekDifferenceR[] = $differenceR;


            $class =
                getColorClass($grade);

            if ($date === $today) {
                $class .= " today";
            }

            $blankClass = "day blank";

            if ($date === $today) {
                $blankClass .= " today";
            }

            $missedR = floatval(
                $days[$date]['missed_r']
            );

            $weekMissedR[] = $missedR;

            if ($missedR == 0) {

                $fireBadge =
                    "<div class='fire-badge'>🔥 No Missed R</div>";

            } else if ($missedR <= .5) {
                $fireBadge =
                    "<div class='missed-badge'>Missed {$missedR}R</div>";
            } else {

                $fireBadge =
                    "<div class='missed-badge'>🚨 Missed {$missedR}R</div>";

            }

            echo "
<a href='day.php?date=$date'
   class='day $class'>

    <div class='day-number'>$day</div>

    <div class='grade'>$grade</div>

    <div class='score'>$score%</div>

    $fireBadge

</a>
        ";
        } else {

            $dayOfWeek =
                date(
                    'w',
                    strtotime($date)
                );

            if (
                $dayOfWeek == 0 ||
                $dayOfWeek == 6
            ) {
                $blankClass =
                    "day blank weekend";
            } else {
                $blankClass =
                    "day blank weekday";
            }

            if ($date === $today) {
                $blankClass .= " today";
            }

            echo "
<a href='day.php?date=$date'
   class='$blankClass'>

        <div class='day-number'>$day</div>

    </a>
    ";
        }

        $cellCounter++;

        if ($cellCounter % 7 == 0) {
            if (count($weekScores) > 0) {
                $weeklyScore =
                    round(
                        array_sum($weekScores)
                        /
                        count($weekScores)
                    );

                $weeklyGrade =
                    getGrade($weeklyScore);

                $weeklyR =
                    round(
                        array_sum($weekDifferenceR),
                        2
                    );
                $weeklyMissedR =
                    round(
                        array_sum($weekMissedR),
                        2
                    );
                $weekClass =
                    getColorClass($weeklyGrade);

                if ($weeklyMissedR == 0) {

                    $weeklyFireBadge =
                        "<div class='fire-badge'>🔥 No Missed R</div>";

                } else if ($weeklyMissedR <= 1) {
                    $weeklyFireBadge =
                        "<div class='missed-badge'>Missed {$weeklyMissedR}R</div>";
                } else {

                    $weeklyFireBadge =
                        "<div class='missed-badge'>🚨 Missed {$weeklyMissedR}R</div>";

                }

                echo "
            <div class='week-summary $weekClass'>

                <div class='week-label'>
                    W{$weekNumber}
                </div>

                <div class='grade'>
                    $weeklyGrade
                </div>

                <div class='score'>
                    $weeklyScore%
                </div>

                $weeklyFireBadge

            </div>
            ";

                $weekNumber++;
            } else {
                echo "<div class='week-summary blank'></div>";
            }

            $weekScores = [];
            $weekDifferenceR = [];
            $weekMissedR = [];
        }
    }

    while ($cellCounter % 7 != 0) {
        echo "<div class='empty'></div>";
        $cellCounter++;
    }

    ?>

</div>