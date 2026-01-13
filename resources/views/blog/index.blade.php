<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Actualités Officielles | FIFA ID</title>
    {{-- On garde ton header existant --}}
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === FIFA MODERN STYLE === */
        body {
            background-color: #f4f7f6; /* Gris très clair pour le fond de page */
            font-family: 'Figtree', sans-serif;
            color: #1a1a1a; /* Noir doux pour le texte (très lisible) */
        }

        .blog-header {
            background: #001431; /* Bleu Nuit FIFA */
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
            background-image: linear-gradient(45deg, #001431 0%, #00265c 100%);
        }

        .blog-header h1 {
            font-size: 3rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -1px;
            margin: 0;
        }

        .blog-header p {
            color: #00ff87; /* Vert FIFA */
            font-weight: 600;
            margin-top: 10px;
            font-size: 1.2rem;
        }

        /* GRID SYSTEM */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            padding-bottom: 50px;
        }

        /* CARTE ARTICLE */
        .news-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: #00ff87;
        }

        .card-img-wrapper {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .news-card:hover .card-img-wrapper img {
            transform: scale(1.05);
        }

        .card-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-date {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #001431;
            margin: 0 0 15px 0;
            line-height: 1.3;
        }

        .card-excerpt {
            color: #555;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .btn-read {
            display: inline-block;
            background: transparent;
            color: #045694;
            font-weight: 700;
            text-decoration: none;
            padding: 10px 0;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            align-self: flex-start;
        }

        .btn-read:hover {
            color: #00ff87;
            border-bottom-color: #00ff87;
        }

    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex;  width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>


            <!-- CORRECTION : lien Vote propre -->
            <a href="{{ route('vote.page') }}">Vote</a>

           
            <a href="/blog">L'Actu</a>

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
                <a style="margin-left: auto;" class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>
                 <a style="margin-left: auto;" class="account_creation" href="/localisation_des_ventes"><img src="{{ asset('assets/map.png') }}" alt="Compte"></a>

            </div>
            @endif
            @if (Auth::user()->id_user_connecte === 35)
                <a class="account_creation" href="/gestion-rgpd">Espace DPO </a>
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

    {{-- EN-TÊTE STYLÉ --}}
    <div class="blog-header">
        <div class="container">
            <h1>FIFA News</h1>
            <p>Toute l'actualité du football mondial</p>
        </div>
    </div>

    <main class="container">
        <div class="news-grid">
            @forelse($blogs as $blog)
                <article class="news-card">
                    <a href="{{ route('blog.show', $blog->idblog) }}" class="card-img-wrapper">
                        <img src="{{ Str::startsWith(trim($blog->image_path), ['http', 'https']) ? $blog->image_path : asset($blog->image_path) }}" 
                            alt="{{ $blog->titre }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                    
                    <div class="card-content">
                        <div class="card-date">
                            <i class="fa-regular fa-calendar"></i> {{ $blog->created_at ? $blog->created_at->format('d M Y') : 'Récent' }}
                        </div>
                        
                        <a href="{{ route('blog.show', $blog->idblog) }}" style="text-decoration:none;">
                            <h2 class="card-title">{{ $blog->titre }}</h2>
                        </a>

                        <p class="card-excerpt">
                            {{ Str::limit($blog->resume ?? $blog->description, 100) }}
                        </p>

                        <a href="{{ route('blog.show', $blog->idblog) }}" class="btn-read">
                            LIRE L'ARTICLE <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            @empty
                <div style="text-align:center; width:100%; padding: 50px; color: #666;">
                    <h3>Aucune actualité pour le moment.</h3>
                </div>
            @endforelse
        </div>
        @include('botman')
    </main>
</body>
</html>