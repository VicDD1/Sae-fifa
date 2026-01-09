<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un nouveau produit | FIFA Store</title>
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <input type="file" name="image" id="image" accept="image/*" >
            @error('image') <span style="color:red;">{{ $message }}</span> @enderror
        </div>


<div class="form-group">
    <label for="id_nation">Nation</label>
    <select name="id_nation" id="id_nation" required>
        <option value="">-- Choisir une nation --</option>
        @foreach($nations as $nation)
            <option 
                value="{{ $nation->id_nation }}"
                {{ old('id_nation') == $nation->id_nation ? 'selected' : '' }}
            >
                {{ $nation->nom_nation }}
            </option>
        @endforeach
    </select>
    @error('id_nation')
        <span style="color:red;">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="id_categorie">Catégorie</label>
    <select name="id_categorie" id="id_categorie" required>
        <option value="">-- Choisir une catégorie --</option>
        @foreach($categories as $categorie)
            <option 
                value="{{ $categorie->id_categorie }}"
                {{ old('id_categorie') == $categorie->id_categorie ? 'selected' : '' }}
            >
                {{ $categorie->label_categorie }}
            </option>
        @endforeach
    </select>
    @error('id_categorie')
        <span style="color:red;">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label>Couleurs</label>

    <div class="dropdown-checkbox">
        <button type="button" class="dropdown-btn" onclick="toggleDropdown('colors-dropdown')">
            Choisir des couleurs
        </button>

        <div id="colors-dropdown" class="dropdown-content">
            @foreach($couleurs as $couleur)
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
        </div>
    </div>

    @error('couleurs') <span class="error">{{ $message }}</span> @enderror
</div>
<div class="form-group">
    <label>Tailles</label>

    <div class="dropdown-checkbox">
        <button type="button" class="dropdown-btn" onclick="toggleDropdown('sizes-dropdown')">
            Choisir des tailles
        </button>

        <div id="sizes-dropdown" class="dropdown-content">
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
        </div>
    </div>

    @error('tailles') <span class="error">{{ $message }}</span> @enderror
</div>





        <button type="submit">Créer le produit</button>
    </form>
</div>

<script>
function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('open');
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('.dropdown-content').forEach(drop => {
        if (!drop.parentElement.contains(e.target)) {
            drop.classList.remove('open');
        }
    });
});

document.querySelectorAll('.dropdown-content').forEach(drop => {
    drop.classList.add('dropdown-hidden');
});
</script>

<style>
.dropdown-content.open {
    display: block;
}
</style>
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
