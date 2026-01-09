<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une Catégorie</title>
    <link rel="stylesheet" href="{{ asset('css/categorie_create.css') }}">
</head>
<body>

    <h1>Ajouter une nouvelle catégorie</h1>

    @if(session('success'))
        <div class="alert">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('categorie.store') }}" method="POST">
        @csrf
        
        <label for="label_categorie"><strong>Nom de la catégorie :</strong></label><br>
        <input type="text" name="label_categorie" id="label_categorie" placeholder="Ex: Maillots Retro" required>
        <br>

        <button type="submit">Enregistrer</button>
    </form>
    
    <a href="/produits" class="back-link">← Retourner à la liste de produits</a>

</body>
</html>