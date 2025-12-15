<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un nouveau produit | FIFA Store</title>
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <style>
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea, input[type="file"] {
            width: 100%; padding: 8px; box-sizing: border-box; border-radius: 4px; border: 1px solid #ccc;
        }
        .dynamic-fields { margin-bottom: 10px; }
        button { padding: 10px 20px; background: #333; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .add-btn { background: #00bfff; margin-left: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Créer un nouveau produit</h1>

    @if(session('success'))
        <div style="color:green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('make_product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="label_produit">Nom du produit</label>
            <input type="text" name="label_produit" id="label_produit" value="{{ old('label_produit') }}" required>
            @error('label_produit') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="prix_base">Prix (€)</label>
            <input type="number" name="prix_base" id="prix_base" value="{{ old('prix_base') }}" step="0.01" min="0" required>
            @error('prix_base') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="description_produit">Description</label>
            <textarea name="description_produit" id="description_produit" rows="4">{{ old('description_produit') }}</textarea>
            @error('description_produit') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="image">Image du produit</label>
            <input type="file" name="image" id="image" accept="image/*" required>
            @error('image') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <!-- Couleurs dynamiques -->
        <div class="form-group">
            <label>Couleurs</label>
            <div id="colors-container">
                <div class="dynamic-fields">
                    <input type="text" name="couleurs[]" placeholder="Ex: Rouge">
                </div>
            </div>
            <button type="button" class="add-btn" onclick="addColor()">Ajouter une couleur</button>
            @error('couleurs.*') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <!-- Tailles dynamiques -->
        <div class="form-group">
            <label>Tailles</label>
            <div id="sizes-container">
                <div class="dynamic-fields">
                    <input type="text" name="tailles[]" placeholder="Ex: M">
                </div>
            </div>
            <button type="button" class="add-btn" onclick="addSize()">Ajouter une taille</button>
            @error('tailles.*') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Créer le produit</button>
    </form>
</div>

<script>
    function addColor() {
        const container = document.getElementById('colors-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'couleurs[]';
        input.placeholder = 'Ex: Bleu';
        input.classList.add('dynamic-fields');
        container.appendChild(input);
    }

    function addSize() {
        const container = document.getElementById('sizes-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'tailles[]';
        input.placeholder = 'Ex: L';
        input.classList.add('dynamic-fields');
        container.appendChild(input);
    }
</script>

</body>
</html>
