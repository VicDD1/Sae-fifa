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
@include('header')

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