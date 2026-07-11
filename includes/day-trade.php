<div class="trade-section">

    <h4 class="trade-heading">Trades</h4>

    <div class="trade-list" data-session="<?php echo $index; ?>">

        <?php

        $sessionTrades = $tradeData[$session] ?? [];

        // Show one blank trade when none exist
        if (empty($sessionTrades)) {
            $sessionTrades[] = [];
        }

        $tradeIndex = 0;

        foreach ($sessionTrades as $currentTrade):

        ?>

            <div class="trade-card" data-trade="<?php echo $tradeIndex; ?>">

                <div class="trade-title">
                    Trade #<?php echo $tradeIndex + 1; ?>
                </div>

                <label>Outcome</label>

                <select
                    class="trade-outcome"
                    name="sessions[<?php echo $index; ?>][trades][<?php echo $tradeIndex; ?>][outcome]">

                    <option value="">Select...</option>

                    <option value="Profit" <?php echo (($currentTrade['outcome'] ?? '') === 'Profit') ? 'selected' : ''; ?>>
                        Profit
                    </option>

                    <option value="Expense" <?php echo (($currentTrade['outcome'] ?? '') === 'Expense') ? 'selected' : ''; ?>>
                        Expense
                    </option>

                    <option value="Break Even" <?php echo (($currentTrade['outcome'] ?? '') === 'Break Even') ? 'selected' : ''; ?>>
                        Break Even
                    </option>

                </select>

                <div class="expense-row" style="display:none;">

                    <label>Reason (Expense)</label>

                    <?php $selectedReason = $currentTrade['primary_reason'] ?? ''; ?>

                    <select
                        name="sessions[<?php echo $index; ?>][trades][<?php echo $tradeIndex; ?>][primary_reason]">

                        <option value="">Select...</option>

                        <optgroup label="Trader">

                            <option value="Missing Criteria" <?php echo ($selectedReason === 'Missing Criteria') ? 'selected' : ''; ?>>Missing Criteria</option>

                            <option value="Emotion" <?php echo ($selectedReason === 'Emotion') ? 'selected' : ''; ?>>Emotion</option>

                            <option value="Tilt" <?php echo ($selectedReason === 'Tilt') ? 'selected' : ''; ?>>Tilt</option>

                            <option value="Technical Mistake" <?php echo ($selectedReason === 'Technical Mistake') ? 'selected' : ''; ?>>Technical Mistake</option>

                        </optgroup>

                        <optgroup label="Market">

                            <option value="Offsides" <?php echo ($selectedReason === 'Offsides') ? 'selected' : ''; ?>>Offsides</option>

                            <option value="Consolidation" <?php echo ($selectedReason === 'Consolidation') ? 'selected' : ''; ?>>Consolidation</option>

                            <option value="Stop Hunt" <?php echo ($selectedReason === 'Stop Hunt') ? 'selected' : ''; ?>>Stop Hunt</option>

                        </optgroup>

                        <optgroup label="Model">

                            <option value="Stopped Then Reached TP" <?php echo ($selectedReason === 'Stopped Then Reached TP') ? 'selected' : ''; ?>>Stopped Then Reached TP</option>

                        </optgroup>

                    </select>

                </div>

                <div class="be-result-row" style="display:none;">

                    <label>Break Even Trade Outcome</label>

                    <select
                        name="sessions[<?php echo $index; ?>][trades][<?php echo $tradeIndex; ?>][be_outcome]">

                        <option value="">Select...</option>

                        <option value="Would Have Been Expense" <?php echo (($currentTrade['be_outcome'] ?? '') === 'Would Have Been Expense') ? 'selected' : ''; ?>>
                            Would Have Been Expense
                        </option>

                        <option value="Would Have Been Profitable" <?php echo (($currentTrade['be_outcome'] ?? '') === 'Would Have Been Profitable') ? 'selected' : ''; ?>>
                            Would Have Been Profitable
                        </option>

                    </select>

                </div>

            </div>

        <?php

            $tradeIndex++;

        endforeach;

        ?>

    </div>

    <button type="button" class="add-trade-btn">
        + Add Trade
    </button>

</div>