<?php

require_once 'config/database.php';

session_start();

$journalId = $_SESSION['journal_id'] ?? 1;

$date = $_GET['date'] ?? date('Y-m-d');

/* ==========================
   Load Existing Session Data
========================== */

$sessionData = [];

$dailyQuery = "
SELECT id
FROM daily_scores
WHERE trade_date = '$date'
AND journal_id = $journalId
LIMIT 1
";

$dailyResult = $conn->query($dailyQuery);

if ($dailyResult && $dailyResult->num_rows > 0) {
       $dailyRow = $dailyResult->fetch_assoc();

       $dailyId = $dailyRow['id'];

       // Load session data for the day
       $sessionQuery = "
              SELECT *
              FROM session_scores
              WHERE daily_id = $dailyId
       ";

       $sessionResult = $conn->query($sessionQuery);

       while ($row = $sessionResult->fetch_assoc()) {
              $sessionData[$row['session_name']] = $row;
       }

       // Load trades for each session
       $tradeData = [];
       $tradeQuery = "
              SELECT
              t.*,
              s.session_name
              FROM trades t
              JOIN session_scores s
              ON t.session_id = s.id
              WHERE s.daily_id = $dailyId
              ORDER BY
              s.id,
              t.trade_number
       ";

       $tradeResult = $conn->query($tradeQuery);

       while ($row = $tradeResult->fetch_assoc()) {

              $tradeData[$row['session_name']][$row['trade_number']] = $row;

       }
}

/* ==========================
   Load Journals
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

?>

<!-- Get Header -->
<?php include 'includes/header.php'; ?>

<div class="container ">

       <h1>Daily Trading Scorecard</h1>

       <h2><?php echo $date; ?></h2>

       <form action="save_day.php" method="POST">

              <input type="hidden" name="trade_date" value="<?php echo $date; ?>">

              <?php

              $sessions = [
                     "AM Session",
                     "Lunch Raid",
                     "PM Session"
              ];

              foreach ($sessions as $index => $session):

                     $currentData =
                            $sessionData[$session] ?? [];

                     $fieldsToCheck = [
                            'rules_followed',
                            'emotional_control',
                            'setup_quality',
                            'context_quality',
                            'execution_management',
                            'realized_r',
                            'missed_r'
                     ];

                     $hasData = false;

                     foreach ($fieldsToCheck as $field) {

                            if (
                                   isset($currentData[$field]) &&
                                   $currentData[$field] !== ''
                            ) {

                                   $hasData = true;
                                   break;
                            }
                     }
                     ?>

                     <div
                            class="session-card session-<?php echo $index; ?> <?php echo ($index > 0 && !$hasData ? 'collapsed' : ''); ?>">

                            <div class="session-header">

                                   <h3><?php echo $session; ?></h3>

                                   <button type="button" class="session-toggle">

                                          <?php echo ($index > 0 && !$hasData ? '▶' : '▼'); ?>

                                   </button>

                            </div>


                            <div class="session-body">

                                   <input type="hidden" name="sessions[<?php echo $index; ?>][name]"
                                          value="<?php echo $session; ?>">

                                   <div class="session-layout">

                                          <!-- LEFT COLUMN -->
                                          <div class="session-metrics">

                                                 <label>Rules Followed</label>

                                                 <input type="number" min="0" max="100"
                                                        name="sessions[<?php echo $index; ?>][rules]"
                                                        value="<?php echo $currentData['rules_followed'] ?? ''; ?>">

                                                 <label>Emotional Control</label>

                                                 <input type="number" min="0" max="100"
                                                        name="sessions[<?php echo $index; ?>][emotion]"
                                                        value="<?php echo $currentData['emotional_control'] ?? ''; ?>">

                                                 <label>Setup Quality</label>

                                                 <input type="number" min="0" max="100"
                                                        name="sessions[<?php echo $index; ?>][setup]"
                                                        value="<?php echo $currentData['setup_quality'] ?? ''; ?>">

                                                 <label>Context Quality</label>

                                                 <input type="number" min="0" max="100"
                                                        name="sessions[<?php echo $index; ?>][context]"
                                                        value="<?php echo $currentData['context_quality'] ?? ''; ?>">

                                                 <label>Execution / Management</label>

                                                 <input type="number" min="0" max="100"
                                                        name="sessions[<?php echo $index; ?>][execution]"
                                                        value="<?php echo $currentData['execution_management'] ?? ''; ?>">

                                                 <label>Realized R</label>

                                                 <input type="number" step="0.01"
                                                        name="sessions[<?php echo $index; ?>][realized_r]"
                                                        value="<?php echo $currentData['realized_r'] ?? ''; ?>">

                                                 <label>Missed R</label>

                                                 <input type="number" step="0.01"
                                                        name="sessions[<?php echo $index; ?>][missed_r]"
                                                        value="<?php echo $currentData['missed_r'] ?? ''; ?>">

                                          </div>

                                          <!-- RIGHT COLUMN -->
                                          <div class="session-trades">

                                                 <?php include 'includes/day-trade.php'; ?>

                                          </div>

                                   </div>
                            </div>
                     </div>

              <?php endforeach; ?>

              <button type="submit">
                     Save Day
              </button>

              <?php if (!empty($dailyId)): ?>

                     <button type="button" class="delete-button" onclick="confirmDelete()">
                            Delete Day
                     </button>

              <?php endif; ?>
       </form>

</div>
<script>

       function confirmDelete() {
              if (
                     confirm(
                            'Delete this trading day and all session data?'
                     )
              ) {
                     window.location =
                            'day-delete.php?date=<?php echo $date; ?>';
              }
       }

       /* Open and Collapse Session Boxes*/
       document.querySelectorAll('.session-header').forEach(header => {

              header.addEventListener('click', () => {

                     const card =
                            header.closest('.session-card');

                     const button =
                            header.querySelector('.session-toggle');

                     card.classList.toggle('collapsed');

                     button.textContent =
                            card.classList.contains('collapsed')
                                   ? '▶'
                                   : '▼';
              });

       });
</script>


<!-- Get Footer -->
<?php include 'includes/footer.php'; ?>