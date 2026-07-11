// ==========================
// Main Menu
// ==========================

const menuBtn =
    document.getElementById('menuToggle');

const sidebar =
    document.getElementById('sidebar');

const content =
    document.getElementById('content');

menuBtn.addEventListener('click', () => {

    sidebar.classList.toggle('collapsed');
    content.classList.toggle('expanded');

});


// ==========================
// Trade Entry includes/day-trade.php
// ==========================

function updateTradeFields(select) {

    const card = select.closest(".trade-card");

    const beRow = card.querySelector(".be-result-row");
    const expenseRow = card.querySelector(".expense-row");

    beRow.style.display = "none";
    expenseRow.style.display = "none";

    if (select.value === "Break Even") {
        beRow.style.display = "block";
    }

    if (select.value === "Expense") {
        expenseRow.style.display = "block";
    }

}

// Initialize existing trade cards
document.querySelectorAll(".trade-outcome").forEach(select => {

    updateTradeFields(select);

    select.addEventListener("change", function () {
        updateTradeFields(this);
    });

});

// Add Trade Button
document.querySelectorAll(".add-trade-btn").forEach(button => {

    button.addEventListener("click", function () {

        const tradeSection = this.closest(".trade-section");
        const tradeList = tradeSection.querySelector(".trade-list");

        const cards = tradeList.querySelectorAll(".trade-card");

        // Clone the first card as the template
        const template = cards[0];
        const clone = template.cloneNode(true);

        const newIndex = cards.length;

        clone.dataset.trade = newIndex;

        clone.querySelector(".trade-title").textContent =
            "Trade #" + (newIndex + 1);

        // Reset all inputs
        clone.querySelectorAll("input").forEach(input => {
            input.value = "";
        });

        const sessionIndex = tradeList.dataset.session;

        clone.querySelectorAll("select").forEach(select => {

            select.selectedIndex = 0;

            if (select.classList.contains("trade-outcome")) {
                select.name = `sessions[${sessionIndex}][trades][${newIndex}][outcome]`;
            }

            else if (select.name.includes("primary_reason")) {
                select.name = `sessions[${sessionIndex}][trades][${newIndex}][primary_reason]`;
            }

            else if (select.name.includes("be_outcome")) {
                select.name = `sessions[${sessionIndex}][trades][${newIndex}][be_outcome]`;
            }

        });

        // Initialize the cloned trade card
        const newSelect = clone.querySelector(".trade-outcome");

        updateTradeFields(newSelect);

        newSelect.addEventListener("change", function () {
            updateTradeFields(this);
        });

        tradeList.appendChild(clone);

    });

});