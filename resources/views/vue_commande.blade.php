<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passer une commande - FIFA Store</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/vue_commande.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>

@include('header')


<main class="checkout-container">

    <!-- INFORMATIONS DE LIVRAISON -->
    <section class="checkout-box">
        <h1 class="title">Informations de livraison</h1>

        @if (session('error'))
            <p>{{ session('error') }}</p>
        @endif

        @if ($errors->any())
            <div>
                <p>Erreurs :</p>
                <ul>
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('commande.valider') }}" method="POST">
            @csrf

            <div class="input-group">
                <label>Nom complet</label>
                <input type="text" name="nom" 
                    required 
                    minlength="2" 
                    maxlength="60"
                    pattern="[A-Za-zÀ-ÿ' -]{2,60}">
            </div>

            <div class="input-group">
                <label>Adresse</label>
                <input type="text" id="adresse_cmd" name="adresse" required autocomplete="on">
                <div id="suggestions_cmd" class="suggestions"></div>
            </div>

            <div class="input-group">
                <label>Ville</label>
                <input type="text" id="ville_cmd" name="ville" required>
            </div>

            <div class="input-group">
                <label>Code postal</label>
                <input type="text" id="cp_cmd" name="cp" required>
            </div>

            <div class="input-group">
                <label>Téléphone</label>
                <input type="text" name="telephone"
                    required
                    pattern="\d{10}"
                    minlength="10" maxlength="10"
                    inputmode="numeric">

            </div>

            <h2 class="subtitle">Mode de livraison</h2>

            <div class="input-group">
                <select name="mode_livraison" id="mode_livraison" required>
                    @foreach($modes as $m)
                        <option value="{{ $m->id_mode_livraison }}" data-type="{{ strtolower($m->type_livraison) }}">
                            {{ $m->type_livraison }} ({{ number_format($m->prix_mode_livraison, 2) }} €)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Section Point Relais -->
            <div id="point-relais-section" style="display: none;">
                <h2 class="subtitle">Choisir un point relais</h2>
                <p style="color: #888; font-size: 0.9em; margin-bottom: 10px;">
                    <i class="fa-solid fa-info-circle"></i> Remplissez d'abord votre ville et code postal pour voir les points relais disponibles.
                </p>
                <button type="button" id="btn-charger-relais" class="btn-secondary" style="margin-bottom: 15px;">
                    <i class="fa-solid fa-rotate"></i> Charger les points relais
                </button>
                <div id="points-relais-list" class="points-relais-container">
                    <!-- Les points relais seront générés ici -->
                </div>
                <input type="hidden" name="point_relais_nom" id="point_relais_nom">
                <input type="hidden" name="point_relais_adresse" id="point_relais_adresse">
            </div>

            <h2 class="subtitle">Méthode de paiement</h2>

            <div class="payment-options">
                <label><input type="radio" name="paiement" value="carte" checked> Carte bancaire</label>
            </div>

            <button type="submit" class="btn-submit">Valider la commande</button>

        </form>
    </section>

    <!-- RÉCAPITULATIF -->
    <aside class="summary-box">
        <h2 class="summary-title">Récapitulatif</h2>

        @foreach($lignes as $ligne)
        <div class="summary-item">
            <img src="../assets/photo_produit/{{ $ligne->id_produit }}.webp" alt="">
            <div>
                <strong>{{ $ligne->produit->label_produit }}</strong><br>
                Taille : {{ $ligne->taille->label_taille ?? 'Unique' }} |
                Couleur : {{ $ligne->couleur->label_colori ?? 'Par défaut' }}
                <br>
                Quantité : {{ $ligne->quantitee }}
            </div>
        </div>
        @endforeach

        <div class="total-box">
            <span>Total articles :</span>
            <span class="total-price">{{ number_format($total, 2) }} €</span>
        </div>
    </aside>
<footer>
    <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
    <a href="/privacy_policy"> Conditions d'utilisation </a>
        <span>|</span>
     <a href="/privacy_policy"> Respect de la vie privée </a> 
</footer>
 
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const adresseInput = document.getElementById("adresse_cmd");
    const suggestionsBox = document.getElementById("suggestions_cmd");
    const villeInput = document.getElementById("ville_cmd");
    const cpInput = document.getElementById("cp_cmd");
    const modeLivraisonSelect = document.getElementById("mode_livraison");
    const pointRelaisSection = document.getElementById("point-relais-section");
    const btnChargerRelais = document.getElementById("btn-charger-relais");
    const pointsRelaisList = document.getElementById("points-relais-list");
    const pointRelaisNomInput = document.getElementById("point_relais_nom");
    const pointRelaisAdresseInput = document.getElementById("point_relais_adresse");

    let debounce;

    adresseInput.addEventListener("input", () => {
        const q = adresseInput.value.trim();

        if (q.length < 3) {
            suggestionsBox.innerHTML = "";
            suggestionsBox.style.display = "none";
            return;
        }

        clearTimeout(debounce);
        debounce = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=8&q=${encodeURIComponent(q)}`, {
                headers: { "Accept-Language": "fr" }
            })
            .then(res => res.json())
            .then(results => {
                suggestionsBox.innerHTML = "";

                if (results.length === 0) {
                    suggestionsBox.style.display = "none";
                    return;
                }

                results.forEach(r => {
                    const addr = r.address || {};
                    const road = addr.road || "";
                    const house = addr.house_number || "";
                    const city = addr.city || addr.town || addr.village || "";
                    const cp = addr.postcode || "";

                    const text = `${house ? house + " " : ""}${road}, ${cp} ${city}`;

                    const div = document.createElement("div");
                    div.className = "suggestion-item";
                    div.textContent = text;

                    div.addEventListener("click", () => {
                        adresseInput.value = `${house ? house + " " : ""}${road}`;
                        villeInput.value = city;
                        cpInput.value = cp;

                        suggestionsBox.innerHTML = "";
                        suggestionsBox.style.display = "none";
                    });

                    suggestionsBox.appendChild(div);
                });

                suggestionsBox.style.display = "block";
            });
        }, 300);
    });

    document.addEventListener("click", (e) => {
        if (!adresseInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.innerHTML = "";
            suggestionsBox.style.display = "none";
        }
    });

    // Fonction pour vérifier si le mode est un point relais
    function isPointRelais(type) {
        if (!type) return false;
        const normalized = type.toLowerCase().trim();
        return normalized.includes("relais") || normalized.includes("point relais");
    }

    // Vérifier au chargement initial si le mode sélectionné est "Point Relais"
    const initialOption = modeLivraisonSelect.options[modeLivraisonSelect.selectedIndex];
    const initialType = initialOption.getAttribute("data-type");
    if (isPointRelais(initialType)) {
        pointRelaisSection.style.display = "block";
    }

    modeLivraisonSelect.addEventListener("change", () => {
        const selectedOption = modeLivraisonSelect.options[modeLivraisonSelect.selectedIndex];
        const type = selectedOption.getAttribute("data-type");

        if (isPointRelais(type)) {
            pointRelaisSection.style.display = "block";
        } else {
            pointRelaisSection.style.display = "none";
            pointsRelaisList.innerHTML = "";
            pointRelaisNomInput.value = "";
            pointRelaisAdresseInput.value = "";
        }
    });

    btnChargerRelais.addEventListener("click", () => {
        const ville = villeInput.value.trim();
        const cp = cpInput.value.trim();

        if (!ville || !cp) {
            alert("Veuillez remplir la ville et le code postal pour charger les points relais.");
            return;
        }

        pointsRelaisList.innerHTML = "";

        for (let i = 1; i <= 5; i++) {
            const pointRelais = {
                nom: `Point Relais ${i}`,
                adresse: `${i} Rue Exemple, ${cp} ${ville}`
            };

            const div = document.createElement("div");
            div.className = "point-relais-item";
            div.textContent = `${pointRelais.nom} - ${pointRelais.adresse}`;

            div.addEventListener("click", () => {
                document.querySelectorAll(".point-relais-item").forEach(item => item.classList.remove("selected"));
                div.classList.add("selected");

                pointRelaisNomInput.value = pointRelais.nom;
                pointRelaisAdresseInput.value = pointRelais.adresse;
            });

            pointsRelaisList.appendChild(div);
        }
    });
});
</script>
 @include('botman')
</body>
</html>
