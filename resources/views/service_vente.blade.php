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

                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf 

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Chaise de bureau" value="{{ old('name') }}">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Prix (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required placeholder="Ex: 49.99" value="{{ old('price') }}">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description détaillée...">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                &larr; Annuler
                            </a>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                Sauvegarder
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>