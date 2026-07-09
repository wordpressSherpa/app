<!-- Home Page Achievements -->

<div class="achievement-grid">

    <div class="analytics-card achievement-card">

        <div class="analytics-icon">📅   
            <span class="analytics-label">
                Trading Days
            </span>
        </div>

      

        <div class="analytics-value">
            <?php echo $totalDaysEntered; ?>
        </div>

    </div>

    <div class="analytics-card streak-card <?php echo $currentStreakClass; ?>">

        <div class="analytics-icon">🔥
            <span class="analytics-label">
                 Hot Streak
            </span>
        </div>

        <div class="analytics-value">
            <?php echo $currentAStreak; ?> <span class="small-text">DAYS</span>
        </div>

    </div>

    <div class="analytics-card streak-card <?php echo $bestStreakClass; ?>">

        <div class="analytics-icon">🏆
            <span class="analytics-label">
                Best Streak
            </span>
        </div>

        <div class="analytics-value">
            <?php echo $longestAStreak; ?> <span class="small-text">DAYS</span>
        </div>

    </div>

    <div class="analytics-card best-day-card <?php echo $bestStreakClass; ?>">

        <div class="analytics-icon">💪
            <span class="analytics-label">
                Best Day
            </span>
        </div>

        <div class="analytics-value">
            <?php echo $bestDay !== null ? round($bestDay) : '-'; ?>%
        </div>

    </div>

    <div class="analytics-card worst-day-card <?php echo $bestStreakClass; ?>">

        <div class="analytics-icon">🚨
            <span class="analytics-label">
                Worst Day
            </span>
        </div>

        <div class="analytics-value">
           <?php echo $worstDay !== null ? round($worstDay) : '-'; ?>%
        </div>

    </div>

    <div class="analytics-card grade-card <?php echo $monthlyGradeClass; ?>">

        <div class="analytics-icon">⭐
            <span class="analytics-label">
                Monthly Grade
            </span>
        </div>

        <div class="analytics-value">
            <?php echo $averageGrade; ?> <?php echo round($averageScore); ?>%
        </div>

    </div>


</div>