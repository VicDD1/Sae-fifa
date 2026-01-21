<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $blog->titre }} | FIFA</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    
  
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
  
    <link rel="stylesheet" href="{{ asset('css/blog_show.css') }}">
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
                                                <input type="hidden" name="parent_id" value="{{ $reply->id_commentaire }}">
                                                <textarea name="contenu" rows="2" style="font-size:0.9rem;" placeholder="Répondre à {{ $reply->user->prenom_user_connecte ?? '...' }}"></textarea>
                                                <button type="submit" class="btn-fifa" style="font-size: 0.7rem; padding: 5px 15px; margin-top:5px;">Valider</button>
                                            </form>
                                        </div>
                                    @endauth

                                    {{-- === NIVEAU 2 : RÉPONSES AUX RÉPONSES === --}}
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

   
    <script src="js/blog_show.js"></script>
</body>
</html>