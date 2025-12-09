console.log("vote.js chargé !");

document.addEventListener("DOMContentLoaded", function () {

    /* ============================
       BLOQUAGE DES JOUEURS DUPLIQUÉS
       ============================*/

    const selectsJoueurs = [
        document.getElementById("joueur1"),
        document.getElementById("joueur2"),
        document.getElementById("joueur3"),
        document.getElementById("joueur4")
    ];

    function updateJoueurOptions() {
        const selectedValues = selectsJoueurs
            .map(s => s.value)
            .filter(v => v !== "");

        selectsJoueurs.forEach(select => {
            const currentValue = select.value;

            for (let option of select.options) {
                if (option.value === "") continue;

                if (selectedValues.includes(option.value) && option.value !== currentValue) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            }
        });
    }

    selectsJoueurs.forEach(select => {
        select.addEventListener("change", updateJoueurOptions);
    });


    /* ============================
       BLOQUAGE DES CLASSEMENTS DUPLIQUÉS
       ============================*/

    const selectsClassements = [
        document.getElementById("classement1"),
        document.getElementById("classement2"),
        document.getElementById("classement3"),
        document.getElementById("classement4")
    ];

    function updateClassementOptions() {
        const selectedRanks = selectsClassements
            .map(s => s.value)
            .filter(v => v !== "");

        selectsClassements.forEach(select => {
            const currentValue = select.value;

            for (let option of select.options) {

                if (option.value === "") continue;

                if (selectedRanks.includes(option.value) && option.value !== currentValue) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            }
        });
    }

    selectsClassements.forEach(select => {
        select.addEventListener("change", updateClassementOptions);
    });

});
