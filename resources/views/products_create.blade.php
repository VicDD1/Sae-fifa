<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouveau produit</title>
    <link rel="stylesheet" href="{{ asset('css/product_create.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    
    <style>
        /* Pour que le dropdown ressemble à un input form-control */
        .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }
        .dropdown-menu {
            max-height: 250px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0 fs-4">Créer un nouveau produit</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('make_product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf 

                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="text-primary mb-3">Informations Générales</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nom du produit *</label>
                                    <input type="text" class="form-control" name="nom_produit" required value="{{ old('nom_produit') }}" placeholder="Ex: Maillot France 2024">
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Prix de base (€) *</label>
                                        <input type="number" class="form-control" name="prix_base" required value="{{ old('prix_base') }}" placeholder="Ex: 80">
                                    </div>
                                    {{-- Stock déplacé ici --}}
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Stock Global *</label>
                                        <input type="number" class="form-control" name="quantite" required min="1" value="{{ old('quantite') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catégorie *</label>
                                    <select class="form-select" name="id_categorie" required>
                                        <option value="">-- Choisir une catégorie --</option>
                                        @foreach($categories as $categorie)
                                            <option value="{{ $categorie->id_categorie }}" {{ old('id_categorie') == $categorie->id_categorie ? 'selected' : '' }}>
                                                {{ $categorie->label_categorie }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nation *</label>
                                    <select class="form-select" name="id_nation" required>
                                        <option value="">-- Choisir une nation --</option>
                                        @foreach($nations as $nation)
                                            <option value="{{ $nation->id_nation }}" {{ old('id_nation') == $nation->id_nation ? 'selected' : '' }}>
                                                {{ $nation->nom_nation }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Variantes & Visuel</h5>

                                <div class="mb-3">
                                    <label class="form-label">Tailles disponibles *</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start bg-white text-dark border-secondary-subtle" type="button" id="dropdownTailles" data-bs-toggle="dropdown" aria-expanded="false">
                                            -- Choisir les tailles --
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2" aria-labelledby="dropdownTailles">
                                            @foreach($tailles as $taille)
                                            <li class="form-check">
                                                <input class="form-check-input checkbox-taille" 
                                                       type="checkbox" 
                                                       name="tailles[]" 
                                                       value="{{ $taille->id_taille }}" 
                                                       id="t_{{ $taille->id_taille }}"
                                                       {{ is_array(old('tailles')) && in_array($taille->id_taille, old('tailles')) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 stretched-link" for="t_{{ $taille->id_taille }}">
                                                    {{ $taille->label_taille }}
                                                </label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <small class="text-muted" id="selectedTaillesText"></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Coloris disponibles *</label>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start bg-white text-dark border-secondary-subtle" type="button" id="dropdownCouleurs" data-bs-toggle="dropdown" aria-expanded="false">
                                            -- Choisir les couleurs --
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2" aria-labelledby="dropdownCouleurs">
                                            @foreach($coloris as $couleur)
                                            <li class="form-check">
                                                <input class="form-check-input checkbox-couleur" 
                                                       type="checkbox" 
                                                       name="couleurs[]" 
                                                       value="{{ $couleur->id_colori }}" 
                                                       id="c_{{ $couleur->id_colori }}"
                                                       {{ is_array(old('couleurs')) && in_array($couleur->id_colori, old('couleurs')) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 stretched-link" for="c_{{ $couleur->id_colori }}">
                                                    {{ $couleur->label_colori }}
                                                </label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <small class="text-muted" id="selectedCouleursText"></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Photo du produit *</label>
                                    <input type="file" class="form-control" name="photo" accept="image/*" required>
                                    <small class="text-muted">Format: jpg, png, jpeg (max 2Mo)</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <label class="form-label">Description détaillée</label>
                            <textarea class="form-control" name="description_produit" rows="3" placeholder="Description du produit...">{{ old('description_produit') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success btn-lg px-5">Enregistrer le produit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function updateDropdownLabel(checkboxClass, buttonId, defaultText) {
        const checkboxes = document.querySelectorAll('.' + checkboxClass);
        const button = document.getElementById(buttonId);
        
        function update() {
            let selected = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    // On récupère le texte du label associé
                    selected.push(cb.nextElementSibling.innerText.trim());
                }
            });
            
            if (selected.length > 0) {
                // Affiche les 3 premiers éléments, puis "..." si plus
                if(selected.length > 3) {
                    button.innerText = selected.slice(0, 3).join(', ') + ' et ' + (selected.length - 3) + ' autres';
                } else {
                    button.innerText = selected.join(', ');
                }
            } else {
                button.innerText = defaultText;
            }
        }

        // Écouter les changements
        checkboxes.forEach(cb => cb.addEventListener('change', update));
        // Lancer une fois au chargement (pour les "old" inputs)
        update();
    }

    // Activer la fonction pour Tailles et Couleurs
    document.addEventListener('DOMContentLoaded', function() {
        updateDropdownLabel('checkbox-taille', 'dropdownTailles', '-- Choisir les tailles --');
        updateDropdownLabel('checkbox-couleur', 'dropdownCouleurs', '-- Choisir les couleurs --');
    });
</script>

</body>
</html>