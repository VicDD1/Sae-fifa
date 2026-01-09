<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | FIFA ID</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/account_creation.css">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

</head>

<script>
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

        // Normalize names to match your <select> values
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

    // Hide dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!adresseInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.innerHTML = "";
        }
    });
});
</script>
<body>
        <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

            
            <a href="/blog">L'Actu </a>

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
                        <img style="text-decoration: none; display: flex; align-items: center; width:40px;" src="{{asset('assets/iconEdit.png')}}" alt="voir mes informations"></img>
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
            <div class="nav-right-group">
                <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>
                 <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>

            </div>
            @endif

                
            @endauth
            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
                @endif
            @endauth

            @auth
                @if (!Auth::user()->professionnel && Auth::user()->id_user_connecte !== 11 && Auth::user()->id_user_connecte !== 13)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if ((Auth::user()->id_user_connecte !== 12 || Auth::user()->id_user_connecte !== 11) && Auth::user()->professionnel)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
    </header>
    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    <div class="right-panel">
        <div class="login-box">
            <h2 class="login-title">Creer un compte professionnel</h2>
            <h1>Etape 1/2</h1>

            <form method="POST" action="{{ route('registerPro.step1.post') }}">
                @csrf

                <div>
                    <label class="input-label">Votre société</label>
                    <input type="text" name="nom_societe" value="{{ old('nom_societe') }}" class="custom-input" required>
                    @error('nom_societe')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="input-label">Numero de TVA de votre entreprise</label>
                    <input type="text" name="numero_TVA" value="{{ old('numero_TVA') }}" class="custom-input" required>
                    @error('numero_TVA')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Votre activité</label>
                    <input type="text" name="activite_professionnel" value="{{ old('activite_professionnel') }}" class="custom-input">
                    @error('activite_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Votre adresse email</label>
                    <input type="email" name="email_professionnel" value="{{ old('email_professionnel') }}" class="custom-input" required>
                    @error('email_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Nom</label>
                    <input type="text" name="nom_professionnel" value="{{ old('nom_professionnel') }}" class="custom-input" required>
                    @error('nom_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Prenom</label>
                    <input type="text" name="prenom_professionnel" value="{{ old('prenom_professionnel') }}" class="custom-input" required>
                    @error('prenom_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Adresse</label>
                    <input id="adresse_auto" type="text" name="adresse_professionnel" value="{{ old('adresse_professionnel') }}" class="custom-input" autocomplete="off" required>
                    <div id="adresse_suggestions" class="suggestions"></div>
                    @error('adresse_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Code postal</label>
                    <input id="postal_auto" type="text" name="code_postal_professionnel" value="{{ old('code_postal_professionnel') }}" class="custom-input" required>
                    @error('code_postal_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Pays d'implentation de l'entreprise</label>
                    <select id="pays_auto" name="pays_professionnel" class="custom-input" required>
                        @foreach([
                            "Afghanistan","Afrique du Sud","Albanie","Algérie","Allemagne","Andorre","Angola","Antigua-et-Barbuda",
                            "Arabie Saoudite","Argentine","Arménie","Australie","Autriche","Azerbaïdjan","Bahamas","Bahreïn",
                            "Bangladesh","Barbade","Belgique","Belize","Bénin","Bhoutan","Biélorussie","Bolivie","Bosnie-Herzégovine",
                            "Botswana","Brésil","Brunei","Bulgarie","Burkina Faso","Burundi","Cambodge","Cameroun","Canada","Cap-Vert",
                            "Chili","Chine","Chypre","Colombie","Comores","Congo","Corée du Nord","Corée du Sud","Costa Rica","Côte d'Ivoire",
                            "Croatie","Cuba","Danemark","Djibouti","Dominique","Égypte","Émirats Arabes Unis","Équateur","Érythrée",
                            "Espagne","Estonie","Eswatini","États-Unis","Éthiopie","Fidji","Finlande","France","Gabon","Gambie","Géorgie",
                            "Ghana","Grèce","Grenade","Guatemala","Guinée","Guinée-Bissau","Guinée équatoriale","Guyana","Haïti","Honduras",
                            "Hongrie","Inde","Indonésie","Irak","Iran","Irlande","Islande","Israël","Italie","Jamaïque","Japon","Jordanie",
                            "Kazakhstan","Kenya","Kirghizistan","Kiribati","Kosovo","Koweït","Laos","Lesotho","Lettonie","Liban","Liberia",
                            "Libye","Liechtenstein","Lituanie","Luxembourg","Madagascar","Malaisie","Malawi","Maldives","Mali","Malte",
                            "Maroc","Marshall","Maurice","Mauritanie","Mexique","Micronésie","Moldavie","Monaco","Mongolie","Monténégro",
                            "Mozambique","Myanmar","Namibie","Nauru","Népal","Nicaragua","Niger","Nigéria","Norvège","Nouvelle-Zélande",
                            "Oman","Ouganda","Ouzbékistan","Pakistan","Palaos","Panama","Papouasie-Nouvelle-Guinée","Paraguay",
                            "Pays-Bas","Pérou","Philippines","Pologne","Portugal","Qatar","République Centrafricaine","République Dominicaine",
                            "République Tchèque","Roumanie","Royaume-Uni","Russie","Rwanda","Saint-Kitts-et-Nevis","Saint-Marin",
                            "Saint-Vincent-et-les-Grenadines","Sainte-Lucie","Salomon","Salvador","Samoa","Sao Tomé-et-Principe","Sénégal",
                            "Serbie","Seychelles","Sierra Leone","Singapour","Slovaquie","Slovénie","Somalie","Soudan","Soudan du Sud",
                            "Sri Lanka","Suède","Suisse","Suriname","Syrie","Tadjikistan","Tanzanie","Tchad","Thaïlande","Timor oriental",
                            "Togo","Tonga","Trinité-et-Tobago","Tunisie","Turkménistan","Turquie","Tuvalu","Ukraine","Uruguay","Vanuatu",
                            "Vatican","Venezuela","Vietnam","Yémen","Zambie","Zimbabwe"
                        ] as $country)
                            <option value="{{ $country }}" {{ old('pays_professionnel') == $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                    @error('pays_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Ville</label>
                    <input id="ville_auto" type="text" name="ville_professionnel" value="{{ old('ville_professionnel') }}" class="custom-input" required>
                    @error('ville_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="input-label">Numero de telephone</label>
                    <div style="display: flex; gap: 5px;">
                        <select name="telephone_country_code" class="custom-input" style="width: 80px;" required>
                            <option value="+33" selected>+33</option>
                            <option value="+32">+32</option>
                            <option value="+34">+34</option>
                            <option value="+44">+44</option>
                        </select>
                        <input type="text" name="telephone_professionnel" value="{{ old('telephone_professionnel') }}" class="custom-input" style="flex:1;" required>
                    </div>
                    @error('telephone_professionnel')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">POURSUIVRE</button>
            </form>

            <footer class="footer">
                <a href="#">Conditions d'utilisation</a>
                <span>|</span>
                <a href="/privacy_policy">Respect de la vie privée</a>
            </footer>
        </div>
    </div>
    @include('botman')
</body>

</html>
