<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $blog->titre }} | FIFA</title>
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === STYLE GLOBAL === */
        body { background-color: #f0f2f5; font-family: 'Figtree', sans-serif; color: #333; }
        
        .main-container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }

        /* === ARTICLE === */
        .article-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
        
        .article-hero {
            position: relative;
            height: 400px;
        }
        
        .article-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-hero-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(0,20,49,0.9), transparent);
            padding: 40px;
            color: white;
        }

        .article-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            line-height: 1.1;
        }

        .article-meta {
            color: #00ff87;
            font-weight: 600;
            margin-top: 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .article-body {
            padding: 40px;
            font-size: 1.15rem;
            line-height: 1.8;
            color: #2c3e50; /* Gris foncé très lisible */
        }

        /* === COMMENTAIRES === */
        .comments-wrapper {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #001431;
            margin-bottom: 30px;
            border-left: 5px solid #00ff87;
            padding-left: 15px;
        }

        /* Formulaire */
        .comment-form textarea {
            width: 100%;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s;
            resize: vertical;
            color: #333;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: #045694;
            background: white;
        }

        .btn-fifa {
            background: #00ff87;
            color: #001431;
            font-weight: 800;
            text-transform: uppercase;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 15px;
            transition: transform 0.2s;
            box-shadow: 0 4px 15px rgba(0, 255, 135, 0.3);
        }

        .btn-fifa:hover {
            transform: scale(1.05);
            background: #00e676;
        }

        /* Liste des avis */
        .comment-item {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            background: #001431;
            color: #00ff87;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 15px;
        }

        .user-info h4 { margin: 0; color: #001431; font-weight: 700; }
        .user-info span { font-size: 0.8rem; color: #888; }

        .comment-text {
            color: #444;
            font-size: 1rem;
            line-height: 1.5;
            margin-left: 55px; /* Aligné sous le nom */
        }

        .actions { margin-left: 55px; margin-top: 10px; }
        
        .btn-reply-link {
            background: none; border: none;
            color: #045694; font-weight: 600; cursor: pointer;
            font-size: 0.9rem; padding: 0;
        }
        .btn-reply-link:hover { text-decoration: underline; }

        /* Réponses */
        .replies-container {
            margin-left: 55px;
            margin-top: 15px;
            border-left: 3px solid #e0e0e0;
            padding-left: 20px;
        }

        .reply-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .login-prompt {
            background: #001431;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .login-prompt a { color: #00ff87; font-weight: bold; }

    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/">Accueil</a>
            <a href="/produits">Fifa Store</a>


            <a href="{{ route('vote.page') }}">Vote</a>

            <a href="/players">Les joueurs</a>
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
                <a class="account_creation" href="/statistiques_de_ventes"><img src="{{ asset('assets/statistique.png') }}" alt="Compte"></a>


                <a href="/proposer_un_produit"  class="account_creation"><p>faire une demande de produit</p></a>
            @endif
                
            @endauth
            @auth
                <a href="{{ route('commande.liste') }}" class="btn btn-primary">
                    Mes commandes
                </a>
            @endauth

            @auth
                @if (!Auth::user()->professionnel)
                    <a href="/creer_un_compte_professionnel_1" class="account_creation">
                        <p>Compte professionnel</p>
                    </a>
                @endif

                @if (Auth::user()->professionnel)
                    <a href="/proposer_un_produit" class="account_creation">
                        <p>faire une demande de produit</p>
                    </a>
                @endif
            @endauth
        </nav>
    </header>

    <main class="main-container">
        
        {{-- ARTICLE --}}
        <article class="article-card">
            <div class="article-hero">
                <img src="{{ $blog->image_path ? asset($blog->image_path) : 'https://digitalhub.fifa.com/transform/09d7367c-333e-4363-8eb0-a54865aa454b/FIFA-World-Cup-Trophy-Generic?io=transform:fill,width:1920,height:1080&quality=75' }}" alt="Cover">
                <div class="article-hero-overlay">
                    <div class="article-meta">
                        <i class="fa-regular fa-clock"></i> Publié le {{ $blog->created_at ? $blog->created_at->format('d/m/Y') : 'Récent' }}
                    </div>
                    <h1 class="article-title">{{ $blog->titre }}</h1>
                </div>
            </div>
            
            <div class="article-body">
                {!! nl2br(e($blog->description ?? $blog->resume)) !!}
            </div>
        </article>

        {{-- COMMENTAIRES --}}
        <div class="comments-wrapper">
            <h3 class="section-title">
                Discussion <span style="color:#aaa; font-weight:400; font-size:0.8em;">({{ $blog->commentaires->count() }})</span>
            </h3>

            {{-- Formulaire Principal --}}
            @auth
                <form action="{{ route('blog.comment.store', $blog->idblog) }}" method="POST" class="comment-form" style="margin-bottom: 40px;">
                    @csrf
                    <textarea name="contenu" rows="3" placeholder="Partagez votre avis avec la communauté..."></textarea>
                    <button type="submit" class="btn-fifa">
                        <i class="fa-regular fa-paper-plane"></i> Publier
                    </button>
                </form>
            @else
                <div class="login-prompt">
                    Vous devez être <a href="/connexion">connecté</a> pour rejoindre la discussion.
                </div>
                <br>
            @endauth

            {{-- Liste des messages --}}
            @foreach($blog->commentaires as $comment)
                <div class="comment-item">
                    <div class="comment-header">
                        {{-- Avatar avec la première lettre --}}
                        <div class="avatar-circle">
                            {{ substr($comment->user->prenom_user_connecte ?? 'U', 0, 1) }}
                        </div>
                        <div class="user-info">
                            <h4>{{ $comment->user->prenom_user_connecte ?? 'Utilisateur Inconnu' }}</h4>
                            <span>{{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}</span>
                        </div>
                    </div>
                    
                    <div class="comment-text">
                        {{ $comment->contenu }}
                    </div>

                    @auth
                        <div class="actions">
                            <button onclick="toggleReply('reply-form-{{ $comment->id_commentaire }}')" class="btn-reply-link">
                                <i class="fa-solid fa-reply"></i> Répondre
                            </button>
                        </div>

                        {{-- Formulaire de réponse (Caché) --}}
                        <div id="reply-form-{{ $comment->id_commentaire }}" style="display:none; margin-top:15px; margin-left: 55px;">
                            <form action="{{ route('blog.comment.store', $blog->idblog) }}" method="POST" class="comment-form">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id_commentaire }}">
                                <textarea name="contenu" rows="2" placeholder="Répondre à {{ $comment->user->prenom_user_connecte ?? '...' }}"></textarea>
                                <button type="submit" class="btn-fifa" style="font-size: 0.8rem; padding: 8px 20px;">
                                    Envoyer
                                </button>
                            </form>
                        </div>
                    @endauth

                    {{-- Réponses imbriquées --}}
                    @if($comment->replies->count() > 0)
                        <div class="replies-container">
                            @foreach($comment->replies as $reply)
                                <div class="reply-item">
                                    <div class="comment-header" style="margin-bottom: 5px;">
                                        <strong style="color: #001431; font-size: 0.95rem;">
                                            {{ $reply->user->prenom_user_connecte ?? 'Utilisateur' }}
                                        </strong>
                                        <span style="font-size: 0.75rem; color: #888; margin-left: 10px;">
                                            {{ $reply->created_at ? $reply->created_at->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                    <p style="margin: 0; font-size: 0.95rem; color: #555;">
                                        {{ $reply->contenu }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </main>

    <script>
        function toggleReply(id) {
            var element = document.getElementById(id);
            if (element.style.display === "none") {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        }
    </script>
</body>
</html>