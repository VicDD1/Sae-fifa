document.addEventListener("DOMContentLoaded", () => {
    const adresseInput = document.getElementById("adresse_auto");
    const suggestionsBox = document.getElementById("adresse_suggestions");
    const postalInput = document.getElementById("postal_auto");
    const villeInput = document.getElementById("ville_auto");
    const paysInput = document.getElementById("pays_auto");

    let debounceTimeout;

    function extractHouseNumber(input) {
        const match = input.match(/^(\d+)\s+(.*)/);
        return match ? { houseNumber: match[1], street: match[2] } : { houseNumber: "", street: input };
    }

    function setCountry(value) {
        if (!value) return;

       
        const map = {
            "France": "France",
            "Belgium": "Belgique",
            "Switzerland": "Suisse",
            "Germany": "Allemagne",
            "Spain": "Espagne",
            "United Kingdom": "Royaume-Uni",
            "United States": "États-Unis",
            "Netherlands": "Pays-Bas",
            "Italy": "Italie",
            "Portugal": "Portugal"
        };

        const countryName = map[value] || value;

        for (let option of paysInput.options) {
            if (option.value.toLowerCase() === countryName.toLowerCase()) {
                paysInput.value = option.value;
                break;
            }
        }
    }

    adresseInput.addEventListener("input", () => {
        const query = adresseInput.value.trim();
        if (query.length < 3) {
            suggestionsBox.innerHTML = "";
            return;
        }

        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            const { houseNumber, street } = extractHouseNumber(query);

            const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=10&q=${encodeURIComponent(query)}`;

            fetch(url, {
                headers: {
                    "Accept-Language": "fr"
                }
            })
            .then(res => res.json())
            .then(results => {
                suggestionsBox.innerHTML = "";

                const seen = new Set();

                results.forEach(r => {
                    const addr = r.address || {};
                    const realStreet = addr.road || street || "";
                    const postcode = addr.postcode || "";
                    const city = addr.city || addr.town || addr.village || "";
                    const country = addr.country || "";

                    const suggestionText = `${houseNumber ? houseNumber + " " : ""}${realStreet}, ${postcode} ${city}`;

                    if (seen.has(suggestionText)) return;
                    seen.add(suggestionText);

                    const item = document.createElement("div");
                    item.className = "suggestion-item";
                    item.textContent = suggestionText;

                    item.addEventListener("click", () => {
                        adresseInput.value = `${houseNumber ? houseNumber + " " : ""}${realStreet}`;
                        postalInput.value = postcode;
                        villeInput.value = city;

                        // ✅ Autofill country select
                        setCountry(country);

                        suggestionsBox.innerHTML = "";
                    });

                    suggestionsBox.appendChild(item);
                });
            })
            .catch(err => console.error("Fetch error:", err));
        }, 300);
    });

   
    document.addEventListener("click", (e) => {
        if (!adresseInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.innerHTML = "";
        }
    });
});