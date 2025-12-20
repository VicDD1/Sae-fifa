<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passer une commande - FIFA Store</title>

    <link rel="stylesheet" href="{{ asset('css/vue_commande.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<header>
        <nav>
            <a href="/">Accueil</a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            <a href="/players">Les joueurs</a>
            <a href="https://www.fifa.com/fr/news" target="_blank">Les Articles</a>

            @auth
                @php
                    $panier = \App\Models\Panier::where('id_user_connecte', Auth::id())->first();
                    $totalQuantite = $panier ? $panier->lignes->sum('quantitee') : 0;
                @endphp
            @endauth

            @guest
                @php $totalQuantite = 0; @endphp
            @endguest

            <a href="{{ route('panier.index') }}" style="margin-left: 15px; font-weight: bold;">
                <i class="fa-solid fa-cart-shopping"></i> Mon Panier ({{ $totalQuantite }})
            </a>

            @auth
                <div style="display: inline-flex; align-items: center; margin-left: 20px; color: white;">
                    
                    <a href="/mon-profil" style="text-decoration: none; display: flex; align-items: center;">
                        <span style="margin-right: 10px; font-weight: bold; border-bottom: 2px solid #00ff87;">
                            {{ Auth::user()->prenom_user_connecte ?? Auth::user()->surnom_user_connecte }}
                        </span>
                    </a>

                    <form action="/logout" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" title="Se déconnecter" style="background: none; border: none; cursor: pointer; color: #ffcccc;">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>

                </div>
            @endauth

            @guest
                <a href="/connexion" class="account_creation" title="Se connecter">
                    <img src="{{ asset('assets/icone.png') }}" alt="Compte">
                </a>
            @endguest
@auth

            @if (Auth::user()->id_user_connecte === 12 || Auth::user()->id_user_connecte === 11)
                <a class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>


                <a href="/proposer_un_produit"  class="account_creation"><p>faire une demande de produit</p></a>
            @endif
                
            @endauth
            @auth
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
            @endauth

            @auth
                @if (!Auth::user()->professionnel)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if (Auth::user()->professionnel)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
    </header>


<main class="checkout-container">

    <!-- INFORMATIONS DE LIVRAISON -->
    <section class="checkout-box">
        <h1 class="title">Informations de livraison</h1>

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
                <select name="mode_livraison" required>
                    @foreach($modes as $m)
                        <option value="{{ $m->id_mode_livraison }}">
                            {{ $m->type_livraison }} ({{ number_format($m->prix_mode_livraison, 2) }} €)
                        </option>

                    @endforeach
                </select>
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

</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const adresseInput = document.getElementById("adresse_cmd");
    const suggestionsBox = document.getElementById("suggestions_cmd");
    const villeInput = document.getElementById("ville_cmd");
    const cpInput = document.getElementById("cp_cmd");

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
});
</script>

</body>
</html>
