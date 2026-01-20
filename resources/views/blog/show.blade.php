<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $blog->titre }} | FIFA</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
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
            color: #2c3e50;
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
            margin-left: 55px;
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

        /* === MODALE DE CONFIRMATION === */
        .modal-overlay {
            display: none; /* Caché par défaut */
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 20, 49, 0.8); /* Fond bleu nuit transparent */
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-top: 5px solid #ff4d4d;
        }

        .modal-box h3 { margin-top: 0; color: #001431; }
        .modal-box p { color: #555; margin-bottom: 25px; }

        .modal-actions { display: flex; justify-content: space-around; gap: 10px; }

        .btn-modal-cancel {
            background: #e0e0e0; color: #333;
            padding: 10px 20px; border-radius: 50px; border: none; cursor: pointer; font-weight: bold;
        }
        .btn-modal-confirm {
            background: #ff4d4d; color: white;
            padding: 10px 20px; border-radius: 50px; border: none; cursor: pointer; font-weight: bold;
        }
        .btn-modal-confirm:hover { background: #cc0000; }

        /* === BOUTON RETOUR === */
        .btn-back {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #001431; /* Bleu nuit */
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 20px; /* Espace sous le bouton */
            transition: color 0.3s;
        }
        
        /* Le cercle blanc autour de la flèche */
        .btn-back i {
            background: white;
            width: 40px; 
            height: 40px;
            border-radius: 50%; /* Rond parfait */
            display: flex; 
            align-items: center; 
            justify-content: center;
            margin-right: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); /* Ombre légère */
            transition: all 0.3s ease;
            color: #001431;
        }

        /* Effet au survol : La flèche devient verte et recule */
        .btn-back:hover i {
            background: #00ff87; /* Vert FIFA */
            transform: translateX(-5px); /* Petite animation vers la gauche */
            box-shadow: 0 4px 15px rgba(0, 255, 135, 0.4);
        }

    </style>
</head>
<body>
@include('header')

    <main class="main-container">
        <a href="/blog" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Retour aux actualités
        </a>
        
        {{-- ARTICLE --}}
        <article class="article-card">
            <div class="article-hero">
                <img src="{{ Str::startsWith(trim($blog->image_path), ['http', 'https']) ? $blog->image_path : asset($blog->image_path) }}" 
                    alt="{{ $blog->titre }}"
                    style="width: 100%; height: 100%; object-fit: cover;">
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
            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <strong>Attention :</strong> {{ session('error') }}
                </div>
            @endif
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
                    <div class="comment-header" style="justify-content: space-between;">
                        <div style="display:flex; align-items:center;">
                            <div class="avatar-circle">
                                {{ substr($comment->user->prenom_user_connecte ?? 'U', 0, 1) }}
                            </div>
                            <div class="user-info">
                                <h4>{{ $comment->user->prenom_user_connecte ?? 'Utilisateur Inconnu' }}</h4>
                                <span>{{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>

                        {{-- BOUTON SUPPRIMER (Modale) --}}
                        @if(Auth::check() && Auth::user()->id_user_connecte == $comment->id_user_connecte)
                            <form id="delete-form-{{ $comment->id_commentaire }}" action="{{ route('blog.comment.destroy', $comment->id_commentaire) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="openDeleteModal('delete-form-{{ $comment->id_commentaire }}')" style="background:none; border:none; color:#ff4d4d; cursor:pointer;" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endif
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
                                    {{-- HEADER RÉPONSE --}}
                                    <div class="comment-header" style="justify-content: space-between;">
                                        <div style="display:flex; align-items:center;">
                                            <strong style="color: #001431; font-size: 0.95rem;">
                                                {{ $reply->user->prenom_user_connecte ?? 'Utilisateur' }}
                                            </strong>
                                            <span style="font-size: 0.75rem; color: #888; margin-left: 10px;">
                                                {{ $reply->created_at ? $reply->created_at->diffForHumans() : '' }}
                                            </span>
                                        </div>

                                        <div style="display:flex; gap:10px;">
                                            {{-- BOUTON SUPPRIMER (Si c'est moi) --}}
                                            @if(Auth::check() && Auth::user()->id_user_connecte == $reply->id_user_connecte)
                                                <form id="delete-form-{{ $reply->id_commentaire }}" action="{{ route('blog.comment.destroy', $reply->id_commentaire) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="openDeleteModal('delete-form-{{ $reply->id_commentaire }}')" style="border:none; background:none; color:#ff4d4d; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <p style="margin: 0; font-size: 0.95rem; color: #555;">{{ $reply->contenu }}</p>

                                    {{-- BOUTON RÉPONDRE À CETTE RÉPONSE --}}
                                    @auth
                                        <button onclick="toggleReply('reply-form-{{ $reply->id_commentaire }}')" class="btn-reply-link" style="font-size:0.8rem; margin-top:5px;">
                                            <i class="fa-solid fa-reply"></i> Répondre
                                        </button>

                                        {{-- FORMULAIRE CACHÉ (Niveau 2) --}}
                                        <div id="reply-form-{{ $reply->id_commentaire }}" style="display:none; margin-top:10px;">
                                            <form action="{{ route('blog.comment.store', $blog->idblog) }}" method="POST">
                                                @csrf
                                                {{-- L'astuce est là : le parent_id est l'ID de la réponse actuelle --}}
                                                <input type="hidden" name="parent_id" value="{{ $reply->id_commentaire }}">
                                                <textarea name="contenu" rows="2" style="font-size:0.9rem;" placeholder="Répondre à {{ $reply->user->prenom_user_connecte ?? '...' }}"></textarea>
                                                <button type="submit" class="btn-fifa" style="font-size: 0.7rem; padding: 5px 15px; margin-top:5px;">Valider</button>
                                            </form>
                                        </div>
                                    @endauth

                                    {{-- === NIVEAU 2 : RÉPONSES AUX RÉPONSES (Nouveau !) === --}}
                                    @foreach($reply->replies as $subReply)
                                        <div style="margin-left: 20px; border-left: 2px solid #ccc; padding-left: 15px; margin-top: 10px; background: rgba(0,0,0,0.02); padding: 10px; border-radius: 5px;">
                                            <div style="display:flex; justify-content:space-between;">
                                                <div>
                                                    <strong style="font-size: 0.85rem; color: #555;">{{ $subReply->user->prenom_user_connecte ?? 'Utilisateur' }}</strong>
                                                    <span style="font-size: 0.7rem; color: #999;"> - {{ $subReply->created_at ? $subReply->created_at->diffForHumans() : '' }}</span>
                                                </div>
                                                {{-- SUPPRESSION NIVEAU 2 --}}
                                                @if(Auth::check() && Auth::user()->id_user_connecte == $subReply->id_user_connecte)
                                                    <form id="delete-form-{{ $subReply->id_commentaire }}" action="{{ route('blog.comment.destroy', $subReply->id_commentaire) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="button" onclick="openDeleteModal('delete-form-{{ $subReply->id_commentaire }}')" style="border:none; background:none; color:#ff4d4d; cursor:pointer; font-size:0.7rem;"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                            <p style="font-size: 0.9rem; margin: 3px 0;">{{ $subReply->contenu }}</p>
                                        </div>
                                    @endforeach
                                    {{-- FIN NIVEAU 2 --}}

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @include('botman')
    </main>

    {{-- LA MODALE (Cachée par défaut) --}}
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-box">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: #ff4d4d; margin-bottom: 15px;"></i>
            <h3>Êtes-vous sûr ?</h3>
            <p>Cette action supprimera définitivement ce message. Impossible de revenir en arrière.</p>
            
            <div class="modal-actions">
                <button onclick="closeDeleteModal()" class="btn-modal-cancel">Annuler</button>
                <button id="confirmDeleteBtn" class="btn-modal-confirm">Oui, supprimer</button>
            </div>
        </div>
    </div>

    <script>
        // Gestion ouverture/fermeture réponse
        function toggleReply(id) {
            var element = document.getElementById(id);
            if (element.style.display === "none") {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        }

        // --- GESTION DE LA MODALE ---
        let formIdToDelete = null;

        function openDeleteModal(formId) {
            formIdToDelete = formId;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            formIdToDelete = null;
            document.getElementById('deleteModal').style.display = 'none';
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (formIdToDelete) {
                document.getElementById(formIdToDelete).submit();
            }
        });

        // Fermer si clic en dehors
        window.onclick = function(event) {
            let modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>