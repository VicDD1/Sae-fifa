document.addEventListener("DOMContentLoaded", () => {
    console.log("vote.js chargÃ©");

    const playerSelects = [
        document.getElementById("joueur1"),
        document.getElementById("joueur2"),
        document.getElementById("joueur3"),
        document.getElementById("joueur4")
    ];

    const rankSelects = document.querySelectorAll(".classement-select");

    function updatePlayerOptions() {
        const values = playerSelects
            .map(s => s.value)
            .filter(v => v !== "");

        playerSelects.forEach(select => {
            Array.from(select.options).forEach(option => {
                if (option.value === "") {
                    option.disabled = false;
                    return;
                }

                if (values.includes(option.value) && option.value !== select.value) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    function updateRankOptions() {
        const values = Array.from(rankSelects)
            .map(s => s.value)
            .filter(v => v !== "");

        rankSelects.forEach(select => {
            Array.from(select.options).forEach(option => {
                if (option.value === "") {
                    option.disabled = false;
                    return;
                }

                if (values.includes(option.value) && option.value !== select.value) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    playerSelects.forEach(select => {
        if (select) {
            select.addEventListener("change", () => {
                updatePlayerOptions();
            });
        }
    });

    rankSelects.forEach(select => {
        select.addEventListener("change", () => {
            updateRankOptions();
        });
    });

    updatePlayerOptions();
    updateRankOptions();
});

