<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouveau produit</title>
    <link rel="stylesheet" href="{{ asset('css/product_create.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    
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
                    <form action="{{ route('product.validate', $product->id_produit) }}" method="POST">
                    @csrf

                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="text-primary mb-3">Informations Générales</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label" >Nom du produit *</label>
                                    <input type="text" class="form-control" name="label_produit" required value="{{ old('label_produit', $product->label_produit) }}" readonly>
                                </div>
                                <input type="hidden"
       name="nom_produit"
       value="{{ $product->label_produit }}">

                                <div class="mb-3">
                                    <label class="form-label">Prix de base (€) *</label>
                                    <input type="number" class="form-control" name="prix_base" required value="{{ old('prix_base', $product->prix_base) }}" placeholder="Ex: 80">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catégorie *</label>
                                    <select class="form-select" disabled>
@foreach($categories as $categorie)
    <option value="{{ $categorie->id_categorie }}"
        {{ $product->id_categorie == $categorie->id_categorie ? 'selected' : '' }}>
        {{ $categorie->label_categorie }}
    </option>
@endforeach
</select>

<input type="hidden"
       name="id_categorie"
       value="{{ $product->id_categorie }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nation *</label>
                                    <select class="form-select" disabled>
@foreach($nations as $nation)
    <option value="{{ $nation->id_nation }}"
        {{ $product->id_nation == $nation->id_nation ? 'selected' : '' }}>
        {{ $nation->nom_nation }}
    </option>
@endforeach
</select>

<input type="hidden"
       name="id_nation"
       value="{{ $product->id_nation }}">

                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">Stock & Variantes</h5>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Taille *</label>
                                    <select class="form-select" name="id_taille" disabled   >
                                    @foreach($tailles as $taille)
    <input type="checkbox"
           disabled
           {{ $product->tailles->contains('id_taille', $taille->id_taille) ? 'checked' : '' }}>
    {{ $taille->label_taille }}
@endforeach

@foreach($product->tailles as $taille)
    <input type="hidden" name="tailles[]" value="{{ $taille->id_taille }}">
@endforeach
                        </select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Quantité stock *</label>
                                        <input type="number" class="form-control" name="quantite" required min="0" value="{{ old('quantite', $product->quantite) }}" >
                                        
<input type="hidden"
       name="quantite"
       value="{{ $stock ?? 0 }}">
                                    </div>
                                </div>
                                

                                <div class="mb-3">
                                    <label class="form-label">Colori *</label>
                                    <select class="form-select" hidden disabled>
                                    @foreach($coloris as $couleur)
                                            <input type="checkbox" disabled {{ $product->couleurs->contains('id_colori', $couleur->id_colori) ? 'checked' : '' }}>
                                            {{ $couleur->label_colori }}
                                    @endforeach

                                    @foreach($product->couleurs as $couleur)
                                            <input type="hidden" name="couleurs[]" value="{{ $couleur->id_colori }}">
                                    @endforeach
                                        </select>   
                                                </div>

                                                <div class="mb-3">
                                                                            <label class="form-label">Photo du produit *</label>
                                                                            @if($product->photo)
                                            <img src="{{ asset($product->photo->code_photo) }}" width="150">
                                            <input type="hidden" name="photo_existante" value="{{ $product->photo->code_photo }}">
                                    @endif

                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <label class="form-label">Description détaillée</label>
                            <textarea class="form-control"
                                name="description_produit"
                                rows="3" readonly>{{ old('description_produit', $product->description_produit) }}</textarea>

                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-success">
        Enregistrer le produit
    </button>
</form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>