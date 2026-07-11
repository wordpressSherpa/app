<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Trading Scorecard</title>

    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">

    <link rel="icon" type="images/png" href="images/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="images/svg+xml" href="images/favicon.svg" />
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png" />

</head>

<body>

    <!-- App Shell -->

    <div class="topbar">

        <button id="menuToggle" class="menu-btn">
            ☰
        </button>

        <div class="brand">

            <div class="brand-icon"><a href="/scorecard/">🧪</a></div>

            <div>
                <div class="brand-title">
                    <a href="/scorecard/app">The Consistency Lab</a>
                </div>

                <div class="brand-subtitle">
                    Build Consistency. Improve Results.
                </div>
            </div>

        </div>
        <div class="current-journal-title">

            <?php
            foreach ($journals as $journal) {
                if ($journal['id'] == $journalId) {
                    echo htmlspecialchars($journal['name']);
                    break;
                }
            }
            ?>

        </div>
        <div class="topbar-journal">
            <div class="journal-switcher">

                <a href="journal-create.php" class="journal-create-btn">
                    + New Journal
                </a>

                <form method="GET" class="journal-form">

                    <select name="journal_id" class="journal-select" onchange="this.form.submit()">
                        <?php foreach ($journals as $journal): ?>
                            <option value="<?= $journal['id'] ?>" <?= $journal['id'] == $journalId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($journal['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </form>

            </div>

        </div>

    </div>

    <div id="sidebar" class="sidebar open">

        <a href="/scorecard/app" class="nav-item active">
            <span>📖</span>
            <span>Journal</span>
        </a>

        <a href="/scorecard/app/page-trades.php" class="nav-item">
            <span>📊</span>
            <span>Trades</span>
        </a>

        <div class="sidebar-footer">

            <a href="account.php" class="nav-item secondary">
                <span class="icon">👤</span>
                <span>Account</span>
            </a>

            <a href="settings.php" class="nav-item secondary">
                <span class="icon">⚙️</span>
                <span>Settings</span>
            </a>

        </div>

    </div>

    <div id="content" class="content-area"></div>