<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Actualités Officielles | FIFA ID</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    
    
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/header.css') }}"> --}} 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/blog_index.css">

    
</head>
<body>
    <header>
        <nav>
            <a href="/"> <img style="text-decoration: none; display: flex; width:120px;" src="{{ asset('assets/logoBlanc.png') }}" alt="Retourner à l'accueil"></a>
            <a href="/produits">Fifa Store</a>
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
                <div class="nav-right-group">
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
                        {{-- Gestion intelligente des images (Lien web ou local) --}}
                        <img src="{{ Str::startsWith(trim($blog->image_path), ['http', 'https']) ? $blog->image_path : asset($blog->image_path) }}" 
                            alt="{{ $blog->titre }}">
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