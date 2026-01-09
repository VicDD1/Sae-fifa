<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouveau produit</title>
    <link rel="stylesheet" href="{{ asset('css/product_create.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>

<div class="container">
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

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf 

                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="text-primary mb-3">Informations Générales</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nom du produit *</label>
                                    <input type="text" class="form-control" name="nom_produit" required value="{{ old('nom_produit') }}" placeholder="Ex: Maillot France 2024">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Prix de base (€) *</label>
                                    <input type="number" class="form-control" name="prix_base" required value="{{ old('prix_base') }}" placeholder="Ex: 80">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catégorie *</label>
<select class="form-select" name="id_categorie" required>
        <option value="">-- Choisir une catégorie --</option>
        
        @foreach($categories as $categorie)
            <option value="{{ $categorie->id_categorie }}">
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
                                            <option value="{{ $nation->id_nation }}">{{ $nation->nom_nation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Stock & Variantes</h5>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Taille *</label>
<select class="form-select" name="id_taille" required>
@foreach($tailles as $taille)
                <label>
                    <input 
                        type="checkbox"
                        name="tailles[]"
                        value="{{ $taille->id_taille }}"
                        {{ is_array(old('tailles')) && in_array($taille->id_taille, old('tailles')) ? 'checked' : '' }}
                    >
                    {{ $taille->label_taille }}
                </label>
            @endforeach
                        </select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Quantité stock *</label>
                                        <input type="number" class="form-control" name="quantite" required min="1" value="{{ old('quantite') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Colori *</label>
                                    <select class="form-select" name="id_colori" required>
                                        @foreach($coloris as $couleur)
                                        <label>
                                            <input 
                                                type="checkbox"
                                                name="couleurs[]"
                                                value="{{ $couleur->id_colori }}"
                                                {{ is_array(old('couleurs')) && in_array($couleur->id_colori, old('couleurs')) ? 'checked' : '' }}
                                            >
                                            {{ $couleur->label_colori }}
                                        </label>
                                    @endforeach
                        </select>   
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

<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>-->
</body>
</html>